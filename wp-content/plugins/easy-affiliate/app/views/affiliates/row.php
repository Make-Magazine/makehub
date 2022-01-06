<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Lib\Utils;
//Loop for each record
if(!empty($records)) {
  $row_index = 0;
  foreach($records as $rec) {
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
    ?>
    <tr id="record_<?php echo esc_attr($rec->ID); ?>" class="<?php echo esc_attr($alternate); ?>">
    <?php
    foreach( $columns as $column_name => $column_display_name ) {
      //Style attributes for each col
      $class = 'class="' . esc_attr("$column_name column-$column_name") . '"';
      $style = in_array($column_name, $hidden) ? ' style="display:none;"' : '';
      $attributes = $class . $style;

      //edit link
      $editlink = admin_url( 'user-edit.php?user_id=' . (int)$rec->ID );

      //Display the cell
      switch( $column_name ) {
        case 'col_signup_date': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(Utils::format_date($rec->signup_date)); ?></td>
          <?php
          break;
        case 'col_username': ?>
          <td <?php echo $attributes; ?>><?php echo get_avatar($rec->email, 24); ?><a href="<?php echo esc_url($editlink); ?>"><?php echo esc_html($rec->username); ?></a></td>
          <?php
          break;
        case 'col_name': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html($rec->name); ?></td>
          <?php
          break;
        case 'col_status': ?>
          <td <?php echo $attributes; ?>>
            <?php
              switch($rec->status) {
                case 'blocked':
                  esc_html_e('Blocked', 'easy-affiliate');
                  break;
                case 'inactive':
                  esc_html_e('Inactive', 'easy-affiliate');
                  break;
                case 'active':
                default:
                  esc_html_e('Active', 'easy-affiliate');
                  break;
              }
            ?>
          </td>
          <?php
          break;
        case 'col_ID': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html($rec->ID); ?></td>
          <?php
          break;
        case 'col_mtd_clicks': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html($rec->mtd_clicks); ?></td>
          <?php
          break;
        case 'col_ytd_clicks': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html($rec->ytd_clicks); ?></td>
          <?php
          break;
        case 'col_mtd_commissions': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(AppHelper::format_currency($rec->mtd_commissions)); ?></td>
          <?php
          break;
        case 'col_ytd_commissions': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(AppHelper::format_currency($rec->ytd_commissions)); ?></td>
          <?php
          break;
        case 'col_parent_name': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html($rec->parent_name); ?></td>
          <?php
          break;
        case 'col_notes': ?>
          <td <?php echo $attributes; ?>>
            <?php
              $popup_heading = sprintf(
                // translators: %1s: username, %2$s: email address
                __('Notes for affiliate %1s (%2$s)', 'easy-affiliate'),
                $rec->username,
                $rec->email
              );
            ?>
            <i class="ea-icon ea-icon-doc-text<?php echo !empty($rec->notes) ? ' esaf-has-notes' : ''; ?>"
               data-notes="<?php echo esc_attr($rec->notes); ?>"
               data-heading="<?php echo esc_attr($popup_heading); ?>"
               data-affiliate-id="<?php echo esc_attr($rec->ID); ?>"
            ></i>
          </td>
          <?php
          break;
      }
    }

    ?>
    </tr>
    <?php
  }
}
