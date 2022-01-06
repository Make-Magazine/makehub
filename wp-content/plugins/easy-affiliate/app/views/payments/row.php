<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Lib\Utils;

if(!empty($records)) {
  $row_index = 0;
  foreach($records as $row) {
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
    ?>
    <tr id="record_<?php echo esc_attr($row->id); ?>" class="<?php echo esc_attr($alternate); ?>">
    <?php
    foreach( $columns as $column_name => $column_display_name ) {
      //Style attributes for each col
      $attributes = 'class="' . esc_attr("$column_name column-$column_name") . (in_array($column_name, $hidden) ? ' hidden' : '') . '"';
      $editlink = admin_url( 'user-edit.php?user_id=' . (int) $row->affiliate_id );

      //Display the cell
      switch( $column_name ) {
        case 'cb': ?>
          <?php
          echo '<th scope="row" class="check-column">';
          echo $this->column_cb( $row );
          echo '</th>';
          ?>
          <?php break;
        case 'col_affiliate': ?>
          <td <?php echo $attributes; ?>><a href="<?php echo esc_url($editlink); ?>"><?php echo esc_html($row->affiliate); ?></a></td>
          <?php break;
        case 'col_created_at': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(Utils::format_datetime($row->created_at)); ?></a></td>
          <?php break;
        case 'col_sales_count': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html($row->sales_count); ?></a></td>
          <?php break;
        case 'col_net_sales_amount': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(AppHelper::format_currency($row->net_sales_amount)); ?></a></td>
          <?php break;
        case 'col_amount': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(AppHelper::format_currency($row->amount)); ?></a></td>
          <?php break;
        case 'col_actions': ?>
          <td <?php echo $attributes; ?>>
            <i class="ea-icon ea-icon-trash esaf-delete-payment" data-payment-id="<?php echo esc_attr($row->id); ?>" title="<?php esc_attr_e('Delete', 'easy-affiliate'); ?>"></i>
          </td>
          <?php break;
      }
    }
    ?>
    </tr>
    <?php
  }
}
