<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php if(isset($errors) && is_array($errors) && count($errors) > 0) : ?>
  <div class="notice notice-error">
    <ul>
      <?php foreach($errors as $error) : ?>
        <li><strong><?php esc_html_e('ERROR', 'easy-affiliate'); ?></strong>: <?php echo esc_html($error); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<?php if(isset($message) && !empty($message)) : ?>
  <div class="notice notice-success below-h2">
    <p><?php echo esc_html($message); ?></p>
  </div>
<?php endif; ?>
