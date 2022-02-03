<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php if(isset($notices) && $notices != null && count($notices) > 0): ?>
<div class="mp_wrapper">
  <div class="mpgft_notice" id="mepr_jump">
    <ul>
      <?php foreach($notices as $error): ?>
        <li><strong><?php _ex('NOTE', 'ui', 'memberpress-gifting'); ?></strong>: <?php print $error; ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
<?php endif; ?>

<?php if( isset($message) and !empty($message) ): ?>
<div class="mp_wrapper">
  <div class="mpgft_updated"><?php echo $message; ?></div>
</div>
<?php endif; ?>
