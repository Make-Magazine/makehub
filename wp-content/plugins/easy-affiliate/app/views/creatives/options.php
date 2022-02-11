<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
?>
<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($creative->url_str); ?>"><?php esc_html_e('Destination URL', 'easy-affiliate'); ?>: </label>
        <?php
          AppHelper::info_tooltip(
            'link-url',
            esc_html__('The destination URL for this creative.', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <input
          class="regular-text"
          type="text"
          data-validation="url"
          data-validation-error-msg="<?php esc_attr_e('A valid URL is required', 'easy-affiliate'); ?>"
          name="<?php echo esc_attr($creative->url_str); ?>"
          id="<?php echo esc_attr($creative->url_str); ?>"
          value="<?php echo esc_attr($creative->url); ?>" />
      </td>
    </tr>
  </tbody>
</table>

<table class="form-table">
<tbody>
  <tr valign="top">
    <th scope="row">
      <label for="<?php echo esc_attr($creative->link_type_str); ?>"><?php esc_html_e('Type', 'easy-affiliate'); ?>: </label>
      <?php
          AppHelper::info_tooltip(
            'link-url',
            esc_html__('Choose whether this creative is a text link or a banner.', 'easy-affiliate')
          );
        ?>
    </th>
    <td>
      <select
          name="<?php echo esc_attr($creative->link_type_str); ?>"
          id="<?php echo esc_attr($creative->link_type_str); ?>"
          data-validation="required"
          class="esaf-toggle-select">
        <option value="text"   <?php selected( $creative->link_type, 'text' );   ?>><?php esc_html_e('Text', 'easy-affiliate'); ?></option>
        <option value="banner" <?php selected( $creative->link_type, 'banner' ); ?>><?php esc_html_e('Banner', 'easy-affiliate'); ?></option>
      </select>
    </td>
  </tr>
  </tbody>
</table>

<div class="esaf-sub-box banner-box">
  <div class="esaf-arrow esaf-gray esaf-up esaf-sub-box-arrow"> </div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <td>
          <p>
            <input type="hidden"
              name  = "<?php echo esc_attr($creative->image_str); ?>"
              id    = "<?php echo esc_attr($creative->image_str); ?>"
              value = "<?php echo esc_attr($creative->image); ?>" />
            <input type="hidden"
              name  = "<?php echo esc_attr($creative->image_alt_str); ?>"
              id    = "<?php echo esc_attr($creative->image_alt_str); ?>"
              value = "<?php echo esc_attr($creative->image_alt); ?>" />
            <input type="hidden"
              name  = "<?php echo esc_attr($creative->image_title_str); ?>"
              id    = "<?php echo esc_attr($creative->image_title_str); ?>"
              value = "<?php echo esc_attr($creative->image_title); ?>" />
            <input type="hidden"
              name  = "<?php echo esc_attr($creative->image_width_str); ?>"
              id    = "<?php echo esc_attr($creative->image_width_str); ?>"
              value = "<?php echo esc_attr($creative->image_width); ?>" />
            <input type="hidden"
              name  = "<?php echo esc_attr($creative->image_height_str); ?>"
              id    = "<?php echo esc_attr($creative->image_height_str); ?>"
              value = "<?php echo esc_attr($creative->image_height); ?>" />
          </p>

          <p class="hide-if-no-js <?php echo (empty($creative->image) ? '' : 'hidden'); ?>" id="whoami">
            <a href id="set-banner-link"><?php esc_html_e('Set banner', 'easy-affiliate'); ?></a>
          </p>

          <div id="featured-footer-image-container" class="">
              <img src="<?php echo esc_attr($creative->image); ?>" alt="<?php echo esc_attr($creative->image_alt); ?>" title="<?php echo esc_attr($creative->image_title); ?>" style="max-width: 100%;" />
              <div id="ea-link-image-width-height"><em><?php echo esc_html(sprintf('(%1$d x %2$d)', $creative->image_width, $creative->image_height)); ?></em></div>
          </div>

          <p class="hide-if-no-js <?php echo (empty($creative->image) ? 'hidden' : ''); ?>" id="iamgroot">
            <a href id="remove-banner-link"><?php esc_html_e('Remove banner', 'easy-affiliate'); ?></a>
          </p>

        </td>
      </tr>
    </tbody>
  </table>
</div>

<div class="esaf-sub-box text-box">
  <div class="esaf-arrow esaf-gray esaf-up esaf-sub-box-arrow"> </div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($creative->link_text_str); ?>"> <span><?php esc_html_e('Link Text', 'easy-affiliate'); ?>: </span> </label>
          <?php
            AppHelper::info_tooltip(
              'link-text',
              esc_html__('The text of the link.', 'easy-affiliate')
            );
          ?>
        </th>
        <td>
          <input class="regular-text" type="text" name="<?php echo esc_attr($creative->link_text_str); ?>" id="<?php echo esc_attr($creative->link_text_str); ?>" value="<?php echo esc_attr($creative->link_text); ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</div>

<table class="form-table">
<tbody>
  <tr valign="top">
    <th scope="row">

      <label for="<?php echo esc_attr($creative->is_hidden_str); ?>"> <span><?php esc_html_e('Hidden', 'easy-affiliate'); ?>: </span> </label>
      <?php
        AppHelper::info_tooltip(
          'is-hidden',
          esc_html__("This will prevent the creative from displaying on the affiliate's dashboard.", 'easy-affiliate')
        );
      ?>
    </th>
    <td>
      <input type="checkbox"
             name="<?php echo esc_attr($creative->is_hidden_str); ?>"
             id="<?php echo esc_attr($creative->is_hidden_str); ?>"
             <?php checked( $creative->is_hidden); ?> />
    </td>
  </tr>

  <tr valign="top">
    <th scope="row">
      <label for="<?php echo esc_attr($creative->link_info_str); ?>"> <span><?php esc_html_e('Notes', 'easy-affiliate'); ?>: </span> </label>
      <?php
        AppHelper::info_tooltip(
          'link-info',
          esc_html__('Additional information about the creative (not visible to affiliates).', 'easy-affiliate')
        );
      ?>
    </th>
    <td>
      <textarea class="large-text" type="text" name="<?php echo esc_attr($creative->link_info_str); ?>" id="<?php echo esc_attr($creative->link_info_str); ?>"><?php echo esc_textarea($creative->link_info); ?></textarea>
    </td>
  </tr>
  </tbody>
</table>
