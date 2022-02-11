<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Helpers\AppHelper;

class Jobs {
  public $config;

  public function __construct() {
    // Setup job configuration
    $this->config = apply_filters('esaf_jobs_config', (object)[
      'status'  => (object)[
        'pending'  => 'pending',
        'complete' => 'complete',
        'failed'   => 'failed',
        'working'  => 'working'
      ],
      'worker' => (object)[
        'interval' => Utils::minutes(1)
      ],
      'cleanup' => (object)[
        'num_retries' => 5, // "num_retries" before transactions fail
        'interval'    => Utils::hours(1),
        'retry_after' => Utils::hours(1),
        'delete_completed_after' => Utils::days(2),
        'delete_failed_after'    => Utils::days(2)
      ]
    ]);

    // Setup the options page
    add_action('wafp_display_general_options', [$this,'display_option_fields']);
    add_action('wafp-process-options', [$this,'store_option_fields']);

    // Set a wp-cron
    add_filter( 'cron_schedules', [$this,'intervals']);
    add_action( 'wafp_jobs_worker', [$this,'worker']);
    add_action( 'wafp_jobs_cleanup', [$this,'cleanup']);

    if( !wp_next_scheduled( 'wafp_jobs_worker' ) ) {
       wp_schedule_event( time(), 'wafp_jobs_interval', 'wafp_jobs_worker' );
    }

    if( !wp_next_scheduled( 'wafp_jobs_cleanup' ) ) {
       wp_schedule_event( time(), 'wafp_jobs_cleanup_interval', 'wafp_jobs_cleanup' );
    }
  }

  public function intervals( $schedules ) {
    $schedules['wafp_jobs_interval'] = [
      'interval' => $this->config->worker->interval,
      'display' => __('Easy Affiliate Jobs Worker', 'easy-affiliate')
    ];

    $schedules['wafp_jobs_cleanup_interval'] = [
      'interval' => $this->config->cleanup->interval,
      'display' => __('Easy Affiliate Jobs Cleanup', 'easy-affiliate')
    ];

    return $schedules;
  }

  public function worker() {
    $max_run_time = 45;
    $start_time = time();

    // We want to allow for at least 15 seconds of buffer
    while( ( ( time() - $start_time ) <= $max_run_time ) and
           ( $job = $this->next_job() ) )
    {
      try {
        $this->work($job);
        if(isset($job->class)) {
          $obj = JobFactory::fetch($job->class, $job);
          Utils::debug_log(sprintf(__('Starting Job - %1$s (%2$s): %3$s', 'easy-affiliate'), $job->id, $job->class, Utils::object_to_string($obj)));
          $obj->perform(); // Run the job's perform method
          Utils::debug_log(sprintf(__('Job Completed - %1$s (%2$s)', 'easy-affiliate'), $job->id, $job->class));
          $this->complete($job); // When we're successful we complete the job
        }
        else {
          $this->fail($job, __('No class was specified in the job config', 'easy-affiliate'));
          Utils::debug_log(__('Job Failed: No class', 'easy-affiliate'));
        }
      }
      catch(\Exception $e) {
        $this->fail($job, $e->getMessage());
        Utils::debug_log(sprintf(__('Job Failed: %s', 'easy-affiliate'), $e->getMessage()));
      }
    }
  }

  public function cleanup() {
    global $wpdb;
    $db = new Db();

    // Retry lingering jobs
    $query = "UPDATE {$db->jobs}
                 SET status = %s
               WHERE status IN (%s,%s)
                 AND tries <= %d
                 AND TIMESTAMPDIFF(SECOND,lastrun,%s) >= %d";
    $query = $wpdb->prepare( $query,
      $this->config->status->pending, // Set status to pending
      $this->config->status->working, // if status = working or
      $this->config->status->failed, // status = failed and
      $this->config->cleanup->num_retries, // number of tries <= num_retries
      Utils::db_now(),
      $this->config->cleanup->retry_after // and the correct number of seconds since lastrun has elapsed
    );
    $wpdb->query($query);

    // Delete completed jobs that have been in the system for over a day?
    $query = "DELETE FROM {$db->jobs}
               WHERE status = %s
                 AND TIMESTAMPDIFF(SECOND,lastrun,%s) >= %d";
    $query = $wpdb->prepare( $query, // Delete jobs
      $this->config->status->complete, // which have a status = complete
      Utils::db_now(),
      $this->config->cleanup->delete_completed_after // and the correct number of seconds since lastrun has elapsed
    );
    $wpdb->query($query);

    // Delete jobs that have been retried and are still in a working state
    $query = "DELETE FROM {$db->jobs}
               WHERE tries > %d
                 AND TIMESTAMPDIFF(SECOND,lastrun,%s) >= %d";
    $query = $wpdb->prepare( $query, // Delete jobs
      $this->config->cleanup->num_retries, // which have only been 'n' retries
      Utils::db_now(),
      $this->config->cleanup->delete_failed_after // and the correct number of seconds since lastrun has elapsed
    );
    $wpdb->query($query);
  }

  /** Returns a full list of all the pending jobs in the queue */
  public function queue() {
    global $wpdb;

    $db = new Db();

    $query = "
      SELECT * FROM {$db->jobs}
       WHERE status = %s
         AND runtime <= %s
       ORDER BY priority ASC, runtime ASC
    ";
    $query = $wpdb->prepare( $query, $this->config->status->pending, Utils::db_now() );

    return $wpdb->get_results($query,OBJECT);
  }

  public function next_job() {
    global $wpdb;

    $db = new Db();

    $query = "SELECT * FROM {$db->jobs}
               WHERE status = %s
                 AND runtime <= %s
               ORDER BY priority ASC, runtime ASC
               LIMIT 1";
    $query = $wpdb->prepare( $query, $this->config->status->pending, Utils::db_now() );

    return $wpdb->get_row($query,OBJECT);
  }

  public function enqueue_in($in, $classname, $args = [], $priority = 10) {
    $when = time() + $this->interval2seconds($in);
    $this->enqueue($classname,$args,$when,$priority);
  }

  public function enqueue_at($at, $classname, $args = [], $priority = 10) {
    $when = $at;
    $this->enqueue($classname,$args,$when,$priority);
  }

  public function enqueue($classname, $args = [], $when = 'now', $priority = 10) {
    $db = new Db();

    if($when==='now') { $when = time(); }

    $config = [
      'runtime' => gmdate('c', $when),
      'firstrun' => gmdate('c', $when),
      'priority' => $priority,
      'tries' => 0,
      'class' => $classname,
      'args' => json_encode($args),
      'reason' => '',
      'status' => $this->config->status->pending,
      'lastrun' => gmdate('c')
    ];

    // returns the job id to dequeue later if necessary
    return $db->create_record($db->jobs, $config, true);
  }

  public function dequeue($job_id) {
    if($job_id==0) { return; }

    $db = new Db();
    return $db->delete_records($db->jobs, ['id' => $job_id]);
  }

  public function work($job) {
    $db = new Db();

    $args = [
      'status' => $this->config->status->working,
      'tries' => $job->tries + 1,
      'lastrun' => gmdate('c')
    ];

    $db->update_record($db->jobs, $job->id, $args);
  }

  public function retry($job, $reason='') {
    $db = new Db();

    $args = ['status' => $this->config->status->pending,
                   'runtime' => gmdate('c'),
                   'reason' => $reason];

    $db->update_record($db->jobs, $job->id, $args);
  }

  public function complete($job) {
    $db = new Db();

    $args = ['status' => $this->config->status->complete];

    $db->update_record($db->jobs, $job->id, $args);
  }

  public function fail($job, $reason='') {
    $db = new Db();

    // We fail and then re-enqueue for an hour later 5 times before giving up
    if($job->tries >= $this->config->cleanup->num_retries) {
      $args = ['status' => $this->config->status->failed, 'reason' => $reason];
      $db->update_record($db->jobs, $job->id, $args);
    }
    else {
      $this->retry($job,$reason);
    }
  }

  private function interval2seconds($interval) {
    $units = ['m','h','d','w','M','y'];
    $seconds = 0;

    foreach($units as $u) {
      preg_match_all("/(\d+){$u}/", $interval, $matches);
      if(isset($matches[1])) {
        foreach($matches[1] as $m) {
          if($u=='m') { $seconds += Utils::minutes($m); }
          elseif($u=='h') { $seconds += Utils::hours($m); }
          elseif($u=='d') { $seconds += Utils::days($m); }
          elseif($u=='w') { $seconds += Utils::weeks($m); }
          elseif($u=='M') { $seconds += Utils::months($m); }
          elseif($u=='y') { $seconds += Utils::years($m); }
        }
      }
    }

    return $seconds;
  }

  public function unschedule_events() {
    $timestamp = wp_next_scheduled( 'wafp_jobs_worker' );
    wp_unschedule_event( $timestamp, 'wafp_jobs_worker' );

    $timestamp = wp_next_scheduled( 'wafp_jobs_cleanup' );
    wp_unschedule_event( $timestamp, 'wafp_jobs_cleanup' );
  }

  public function display_option_fields() {
    $enabled = get_option('mp-bkg-email-jobs-enabled',isset($_POST['bkg_email_jobs_enabled']));

    ?>
    <div id="mp-bkg-email-jobs">
      <br/>
      <h3><?php esc_html_e('Background Jobs', 'easy-affiliate'); ?></h3>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row">
              <label for="bkg_email_jobs_enabled"><?php esc_html_e('Asynchronous Emails', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'wafp-asynchronous-emails',
                  sprintf(
                    // translators: %1$s: open strong tag, %2$s: close strong tag
                    esc_html__('This option will allow you to send all Easy Affiliate emails asynchronously. This option can increase the speed & performance of the checkout process but may also result in a delay in when emails are received. %1$sNote:%2$s This option requires wp-cron to be enabled and working.', 'easy-affiliate'),
                    '<strong>',
                    '</strong>'
                  )
                );
              ?>
            </th>
            <td>
              <input type="checkbox" name="bkg_email_jobs_enabled" id="bkg_email_jobs_enabled" <?php checked($enabled); ?> />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <?php
  }

  public function validate_option_fields($errors)
  {
    // Nothing to validate yet -- if ever
  }

  public function update_option_fields()
  {
    // Nothing to do yet -- if ever
  }

  public function store_option_fields()
  {
    update_option('mp-bkg-email-jobs-enabled',isset($_POST['bkg_email_jobs_enabled']));
  }
}
