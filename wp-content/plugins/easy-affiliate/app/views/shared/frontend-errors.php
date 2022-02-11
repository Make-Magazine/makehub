<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php if(isset($errors) && is_array($errors) && count($errors) > 0) : ?>
  <div class="esaf-errors">
    <ul>
      <?php foreach($errors as $error) : ?>
        <li><strong><?php esc_html_e('ERROR', 'easy-affiliate'); ?></strong>: <?php echo esc_html($error); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
