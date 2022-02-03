<select class="mepr_filter_field" id="type">
  <option value="all" <?php selected($types, false); ?>><?php _e('All Types', 'memberpress-gifting'); ?></option>
  <option value="purchased" <?php selected($types, 'purchased'); ?>><?php _e('Gifts Purchased', 'memberpress-gifting'); ?></option>
  <option value="claimed" <?php selected($types, 'claimed'); ?>><?php _e('Gifts Claimed', 'memberpress-gifting'); ?></option>
</select>
