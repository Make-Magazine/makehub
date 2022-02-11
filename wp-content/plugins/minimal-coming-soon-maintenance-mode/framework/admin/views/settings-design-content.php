<div class="csmm-tile" id="design-content">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Content Module</div>
        <p>Writing quality copy will help your SEO and increase trust with visitors. Try writing a few sentences even for the most minimalistic pages.</p>

        <div class="csmm-section-content">
            <div class="csmm-form-group">
                <?php wp_editor(stripslashes($signals_csmm_options['secondary_text']), 'signals_csmm_secondary', $settings = array(
                    'textarea_rows' => 10,
                    'media_buttons' => 1,
                    'teeny' => false
                )); ?>
            </div>
        </div>


        <div class="csmm-double-group csmm-clearfix">
            <div class="csmm-form-group">
                <label for="signals_csmm_content_1col_size" class="csmm-strong">Content Text Size</label>
                <input type="hidden" name="signals_csmm_content_1col_size" value="<?php esc_attr_e($signals_csmm_options['content_1col_font_size']); ?>" data-min="6" data-max="200" data-step="1" data-label="%val%px" class="csmm-slide-input">
                <p class="csmm-form-help-block">Don't be afraid to make to font a bit bigger. Default: 16px;</p>
            </div>

            <div class="csmm-form-group">
                <label for="signals_csmm_content_1col_color" class="csmm-strong">Content Text Color</label>
                <input type="text" name="signals_csmm_content_1col_color" id="signals_csmm_content_1col_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['content_1col_font_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                <p class="csmm-form-help-block">Make sure the contrast is good. Whiteish text on a dark background is always a good pick.</p>
            </div>
        </div>

        <div class="csmm-double-group csmm-clearfix">
            <div class="csmm-form-group">
                <label for="signals_csmm_content_1col_font" class="csmm-strong">Content Font</label>
                <select name="signals_csmm_content_1col_font" id="signals_csmm_content_1col_font" class="csmm-google-fonts">
                    <option value="Arial" <?php selected('Arial', $signals_csmm_options['content_1col_font']); ?>>Arial</option>
                    <option value="Helvetica" <?php selected('Helvetica', $signals_csmm_options['content_1col_font']); ?>>Helvetica</option>
                    <option value="Georgia" <?php selected('Georgia', $signals_csmm_options['content_1col_font']); ?>>Georgia</option>
                    <option value="Times New Roman" <?php selected('Times New Roman', $signals_csmm_options['content_1col_font']); ?>>Times New Roman</option>
                    <option value="Tahoma" <?php selected('Tahoma', $signals_csmm_options['content_1col_font']); ?>>Tahoma</option>
                    <option value="Verdana" <?php selected('Verdana', $signals_csmm_options['content_1col_font']); ?>>Verdana</option>
                    <option value="Geneva" <?php selected('Geneva', $signals_csmm_options['content_1col_font']); ?>>Geneva</option>
                    <option disabled>-- via google --</option>
                    <?php

                    // Listing fonts from the array
                    foreach ($signals_google_fonts as $signals_font) {
                        echo '<option value="' . $signals_font . '"' . selected($signals_font, $signals_csmm_options['content_1col_font']) . '>' . $signals_font . '</option>' . "\n";
                    }

                    ?>
                </select>
                <h3><?php _e('This is how the content font is going to look!', 'signals'); ?></h3>
                <p class="csmm-form-help-block">Choose from over 700 Google Fonts. Make sure other modules use the same, or matching fonts.</p>
            </div>
        </div>


    </div>
</div><!-- #content -->