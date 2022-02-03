<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<p id="config-mepr-<?php echo $email->dashed_name(); ?>" class="mpgft-config-email-row mepr-config-email-row">
  <label for="<?php echo $email->field_name('enabled'); ?>">
    <input type="checkbox"
           name="<?php echo $email->field_name('enabled'); ?>"
           id="<?php echo $email->field_name('enabled', true); ?>"<?php checked($email->enabled()); ?>/>
    <?php printf(__('Send %s','memberpress-gifting'), $email->title); ?>
  </label>
  <?php MeprAppHelper::info_tooltip( $email->dashed_name(),
                                     $email->title,
                                     $email->description ); ?>
  <a href="#"
     class="mpgft-edit-email-toggle button"
     data-id="edit-<?php echo $email->dashed_name(); ?>"
     data-edit-text="<?php _e('Edit', 'memberpress-gifting'); ?>"
     data-cancel-text="<?php _e('Hide Editor', 'memberpress-gifting'); ?>"><?php _e('Edit', 'memberpress-gifting'); ?></a>
  <a href="#"
     class="mpgft-send-test-email button"
     data-obj-dashed-name="<?php echo $email->dashed_name(); ?>"
     data-obj-name="<?php echo get_class($email); ?>"
     data-subject-id="<?php echo $email->field_name('subject', true); ?>"
     data-use-template-id="<?php echo $email->field_name('use_template', true); ?>"
     data-body-id="<?php echo $email->field_name('body', true); ?>"><?php _e('Send Test', 'memberpress-gifting'); ?></a>
  <a href="#"
     class="mpgft-reset-email button"
     data-obj-dashed-name="<?php echo $email->dashed_name(); ?>"
     data-subject-id="<?php echo $email->field_name('subject', true); ?>"
     data-body-obj="<?php echo get_class($email); ?>"
     data-use-template-id="<?php echo $email->field_name('use_template', true); ?>"
     data-body-id="<?php echo $email->field_name('body', true); ?>"><?php _e('Reset to Default', 'memberpress-gifting'); ?></a>
  <img src="<?php echo MEPR_IMAGES_URL . '/square-loader.gif'; ?>" alt="<?php _e('Loading...', 'memberpress-gifting'); ?>" id="mepr-loader-<?php echo $email->dashed_name(); ?>" class="mepr_loader" />
</p>
<div id="edit-<?php echo $email->dashed_name(); ?>" class="mepr-hidden mpgft-options-pane mpgft-edit-email">
  <ul>
    <li>
      <span class="mpgft-field-label"><?php _e('Subject', 'memberpress-gifting'); ?></span><br/>
      <input class="form-field" type="text" id="<?php echo $email->field_name('subject', true); ?>" name="<?php echo $email->field_name('subject'); ?>" value="<?php echo $email->subject(); ?>" />
    </li>
    <li>
      <span class="mpgft-field-label"><?php _e('Body', 'memberpress-gifting'); ?></span><br/>
      <?php wp_editor( $email->body(),
                       $email->field_name('body', true),
                       array( 'textarea_name' => $email->field_name('body') )
                     ); ?>
    </li>
    <li>
      <select id="var-<?php echo $email->dashed_name(); ?>">
        <?php foreach( $email->variables as $var ): ?>
          <option value="{$<?php echo $var; ?>}">{$<?php echo $var; ?>}</option>
        <?php endforeach; ?>
      </select>

      <a href="#" class="button mpgft-insert-email-var" data-variable-id="var-<?php echo $email->dashed_name(); ?>"
         data-textarea-id="<?php echo $email->field_name('body', true); ?>"><?php _e('Insert &uarr;', 'memberpress-gifting'); ?></a>
    </li>
    <li>
      <br/>
      <input type="checkbox"
             name="<?php echo $email->field_name('use_template'); ?>"
             id="<?php echo $email->field_name('use_template', true); ?>"<?php checked($email->use_template()); ?>/>
      <span class="mpgft-field-label">
        <?php _e('Use default template', 'memberpress-gifting'); ?>
        <?php MeprAppHelper::info_tooltip( $email->dashed_name() . '-template',
                                           __('Default Email Template', 'memberpress-gifting'),
                                           __('When this is checked the body of this email will be wrapped in the default email template.', 'memberpress-gifting') ); ?>
      </span>
    </li>
  </ul>
</div>

