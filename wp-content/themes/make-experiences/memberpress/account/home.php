<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="mp_wrapper mp-info-tab">
  <?php if(!empty($welcome_message)): ?>
    <div id="mepr-account-welcome-message">
      <h2>Membership Information</h2>
    </div>
  <?php endif; ?>

  <?php if( !empty($mepr_current_user->user_message) ): ?>
    <div id="mepr-account-user-message">
      <?php echo MeprHooks::apply_filters('mepr-user-message', wpautop(do_shortcode($mepr_current_user->user_message)), $mepr_current_user); ?>
    </div>
  <?php endif; ?>

  <?php MeprView::render('/shared/errors', get_defined_vars()); ?>

  <?php
    $user_id = bp_displayed_user_id();
    $user = get_user_by('id', $user_id);
  	echo return_membership_widget($user);
  ?>

  <?php MeprHooks::do_action('mepr_account_home', $mepr_current_user); ?>
</div>
