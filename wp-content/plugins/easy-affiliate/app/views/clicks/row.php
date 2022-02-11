<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Lib\Utils;
?>
<?php
if(!empty($records)) {
  $row_index = 0;
  foreach($records as $row) {
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
    //Open the line
    ?>
      <tr id="record_<?php echo esc_attr($row->id); ?>" class="<?php echo esc_attr($alternate); ?> <?php echo empty($row->flag) ? '' : 'esaf-flagged-click esaf-' . $row->flag; ?>">
    <?php
    foreach( $columns as $column_name => $column_display_name ) {
      //Style attributes for each col
      $class = 'class="' . esc_attr("$column_name column-$column_name") . '"';
      $style = in_array($column_name, $hidden) ? ' style="display:none;"' : '';

      $attributes = $class . $style;

      $editlink = admin_url( 'user-edit.php?user_id=' . (int)$row->affiliate_id );

      //Display the cell
      switch( $column_name ) {
        case 'col_created_at': ?>
          <td <?php echo $attributes; ?>>
            <?php
              echo esc_html(Utils::format_date($row->created_at, Utils::get_date_format() . ' H:i:s'));
              do_action('esaf_click_column_created_at', $row);
            ?>
          </td>
          <?php break;
        case 'col_user_login': ?>
          <td <?php echo $attributes; ?>><a href="<?php echo esc_url($editlink); ?>"><?php echo esc_html($row->user_login); ?></a></td>
          <?php break;
        case 'col_target_url': ?>
          <td <?php echo $attributes; ?>><?php echo !empty($row->target_url) ? esc_url($row->target_url) : esc_html__('Unknown', 'easy-affiliate'); ?></td>
          <?php break;
        case 'col_ip': ?>
          <td <?php echo $attributes; ?>><?php echo esc_html($row->ip); ?></td>
          <?php break;
        case 'col_referrer': ?>
          <td <?php echo $attributes; ?>>
            <?php if( !empty($row->referrer) ): ?>
              <a href="<?php echo esc_url($row->referrer); ?>"><?php echo esc_url($row->referrer); ?></a>
            <?php endif; ?>
          </td>
          <?php break;
      }
    }
    ?>
    </tr>
    <?php
  }
}
