<?php
namespace memberpress\gifting\lib;
use memberpress\gifting as base;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class EmailToException extends \Exception { }
class EmailFromException extends \Exception { }

abstract class BaseEmail extends \MeprBaseEmail {
  // It's a requirement for base classes to define these
  public $title, $description, $defaults, $variables, $to, $headers, $show_form, $ui_order, $test_vars;

  public function __construct($args=array()) {
    $this->headers = array();
    $this->defaults = array();
    $this->variables = array();
    $this->test_vars = array();

    $this->set_defaults($args);
  }

  /** Set the default enabled, title, subject, body & other variables */
  abstract public function set_defaults($args=array());

  public function enabled() {
    return ($this->get_stored_field('enabled') != false);
  }

  public function use_template() {
    return ($this->get_stored_field('use_template') != false);
  }

  public function headers() {
    return $this->headers;
  }

  public function subject() {
    return $this->get_stored_field('subject');
  }

  public function body() {
    return $this->get_stored_field('body');
  }

  public function default_subject() {
    return $this->defaults['subject'];
  }

  public function default_body() {
    return $this->defaults['body'];
  }

  public function formatted_subject($values=array(), $subject=false) {
    if($subject)
      return $this->replace_variables($subject,$values);
    else
      return $this->replace_variables($this->subject(),$values);
  }

  public function formatted_body( $values = array(), $type = 'html', $body = false, $use_template = null ) {
    if( $body ) {
      $body = $this->replace_variables( $body, $values );
    }
    else {
      $body = $this->replace_variables( $this->body(), $values );
    }

    $body .= $this->footer();

    if( is_null( $use_template ) ) { $use_template = $this->use_template(); }

    if( $type == 'html' && $use_template ) {
      return \MeprView::get_string( '/emails/template', get_defined_vars() );
    }

    if( $type == 'html' ) { return $body; }

    return \MeprUtils::convert_to_plain_text( $body );
  }

  public function send($values = array(), $subject = false, $body = false, $use_template = null, $content_type = 'html') {
    // Used to filter parameters to be searched and replaced in the email subject & body
    $values  = apply_filters('mepr_email_send_params',  $values,  $this, $subject, $body  );
    $body    = apply_filters('mepr_email_send_body',    $body,    $this, $subject, $values);
    $subject = apply_filters('mepr_email_send_subject', $subject, $this, $body,    $values);
    $attachments = apply_filters('mepr_email_send_attachments', array(), $this, $body,    $values);

    $bkg_enabled = get_option('mp-bkg-email-jobs-enabled');

    if( !$bkg_enabled || ( defined('DOING_CRON') && DOING_CRON ) ) {
      if( !isset($this->to) or empty($this->to) ) {
        throw new EmailToException(__('No email recipient has been set.', 'memberpress-gifting'));
      }

      add_action( 'phpmailer_init', array( $this, 'mailer_init' ) );

      if($content_type=='html') {
        add_filter('wp_mail_content_type', array($this,'set_html_content_type'));
      }

      Utils::wp_mail( $this->to, $this->formatted_subject($values,$subject),
                          $this->formatted_body($values,$content_type,$body,$use_template),
                          $this->headers, $attachments );

      if($content_type=='html') {
        remove_filter('wp_mail_content_type', array($this,'set_html_content_type'));
      }

      remove_action( 'phpmailer_init', array( $this, 'mailer_init' ) );
      do_action( 'mepr_email_sent', $this, $values, $attachments );
    }
    else {
      $job = new \MeprEmailJob();
      $job->values  = $values;
      $job->subject = $subject;
      $job->body    = $body;
      $job->class   = get_class($this);
      $job->to      = $this->to;
      $job->headers = $this->headers;
      $job->use_template = $use_template;
      $job->content_type = $content_type;
      $job->enqueue();
    }
  }

  public function set_html_content_type($content_type = 'text/html') {
    // return 'text/html;charset="UTF-8"'; //UTF-8 is breaking internal WP checks
    return 'text/html';
  }

  // This is for some severe multipart mailing
  public function mailer_init( $phpmailer ) {
    // Plain text
    // Decode body
    $phpmailer->AltBody = wp_specialchars_decode( $phpmailer->Body, ENT_QUOTES );
    $phpmailer->AltBody = \MeprUtils::convert_to_plain_text( $phpmailer->AltBody );

    // Replace variables in email
    $phpmailer->AltBody = apply_filters( 'mepr-email-plaintext-body', $phpmailer->AltBody );

    if($phpmailer->ContentType=='text/html') {
      // HTML
      // Replace variables in email
      $phpmailer->Body = apply_filters( 'mepr-email-html-body', $phpmailer->Body );
    }
  }

  public function send_if_enabled($values=array(),$content_type='html') {
    if($this->enabled()) { $this->send($values,false,false,null,$content_type); }
  }


  public function replace_variables($text, $values) {
    return \MeprUtils::replace_vals($text, $values);
  }

  // This is the most important part here to determine the content of the default email
  public function body_partial($vars=array()) {
    ob_start();
    require_once(base\VIEWS_PATH .'/emails/'.$this->view_name() . '.php');
    $view = ob_get_clean();
    return $view;
  }

  public function view_name() {
    $classname = get_class($this);
    preg_match('/([^\\\]*)$/', $classname, $m);
    $file_name = $m[1];
    $view = preg_replace('/^Mepr(.*)Email$/', '$1', $file_name);
    $view = preg_replace('/\B([A-Z])/', '-$1', $view);
    return strtolower($view);
  }

  public function dashed_name() {
    $classname = get_class($this);
    preg_match('/([^\\\]*)$/', $classname, $m);
    $file_name = $m[1];
    $tag = preg_replace('/\B([A-Z])/', '-$1', $file_name);
    return strtolower($tag);
  }

  public function display_form() {
    $email = $this;
    ob_start();
    require_once(base\VIEWS_PATH .'/admin/emails/options.php');
    $view = ob_get_clean();
    echo $view;
  }

  private function footer() {
    $links = $this->footer_links();
    $links_str = join('&#124;', $links);
    ob_start();
    ?>
      <div id="footer" style="width: 680px; padding: 0px; margin: 0 auto; text-align: center;">
        <?php echo $links_str; ?>
      </div>
    <?php

    return ob_get_clean();
  }

  private function footer_links() {
    $mepr_options = \MeprOptions::fetch();
    $links = array();

    if($mepr_options->include_email_privacy_link) {
      $privacy_policy_page_link = \MeprAppHelper::privacy_policy_page_link();
      if($privacy_policy_page_link !== false) {
        $links[] = '<a href="' . $privacy_policy_page_link . '">' . __('Privacy Policy', 'memberpress-gifting') . '</a>';
      }
    }

    return $links;
  }

  // abstract public function field_name($field='enabled', $id=false);

  // This will vary based on what part of the
  // code is sending out the email
  // abstract public function get_stored_field($fieldname);

  public function get_stored_field($fieldname) {
    $mepr_options = \MeprOptions::fetch();
    $classname = get_class($this);

    $default = isset($this->defaults[$fieldname]) ? $this->defaults[$fieldname] : false;

    if( !isset($mepr_options->emails[$classname]) or !isset($mepr_options->emails[$classname][$fieldname]) )
    { return $default; }

    return $mepr_options->emails[$classname][$fieldname];
  }

  public function field_name($field='enabled', $id=false) {
    $mepr_options = \MeprOptions::fetch();
    $classname = get_class($this);

    if($id)
      return $mepr_options->emails_str . '-' . $this->dashed_name() . '-' . $field;
    else
      return $mepr_options->emails_str . '[' . $classname . '][' . $field . ']';
  }





}
