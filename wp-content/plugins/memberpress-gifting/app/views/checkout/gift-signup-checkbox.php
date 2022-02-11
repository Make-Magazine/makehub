<div class="mp-form-row">
    <label for="mpgft-signup-gift-checkbox<?php echo $unique_suffix; ?>" class="mepr-checkbox-field mepr-form-input">
    <input type="checkbox" name="mpgft-signup-gift-checkbox" id="mpgft-signup-gift-checkbox<?php echo $unique_suffix; ?>" class="mepr-form-input <?php echo $force_checked ? 'mpgft-force-signup-checked' : '' ?>" <?php checked($force_checked) ?> />
    <?php echo esc_html_e("Is this a gift?", 'memberpress-gifting'); ?>
    </label>
</div>
