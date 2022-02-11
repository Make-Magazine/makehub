<div class="csmm-tile" id="design-header">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Header Module</div>
        <p>Short, big &amp; bold. The header should capture your visitors' attention in just a few words.</p>

        <div class="csmm-section-content">
            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_header" class="csmm-strong">Header Text</label>
                    <textarea name="signals_csmm_header" id="signals_csmm_header" rows="3" placeholder="Keep it short, one or two lines"><?php echo esc_textarea(stripslashes($signals_csmm_options['header_text'])); ?></textarea>
                    <p class="csmm-form-help-block">You can put any HTML, but we advise keeping it plain-text only and short, one or two lines.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_header_font" class="csmm-strong">Header Font</label>

                    <select name="signals_csmm_header_font" id="signals_csmm_header_font" class="csmm-google-fonts">
                        <option value="Arial" <?php selected('Arial', $signals_csmm_options['header_font']); ?>>Arial</option>
                        <option value="Helvetica" <?php selected('Helvetica', $signals_csmm_options['header_font']); ?>>Helvetica</option>
                        <option value="Georgia" <?php selected('Georgia', $signals_csmm_options['header_font']); ?>>Georgia</option>
                        <option value="Times New Roman" <?php selected('Times New Roman', $signals_csmm_options['header_font']); ?>>Times New Roman</option>
                        <option value="Tahoma" <?php selected('Tahoma', $signals_csmm_options['header_font']); ?>>Tahoma</option>
                        <option value="Verdana" <?php selected('Verdana', $signals_csmm_options['header_font']); ?>>Verdana</option>
                        <option value="Geneva" <?php selected('Geneva', $signals_csmm_options['header_font']); ?>>Geneva</option>
                        <option disabled>-- via google --</option>
                        <?php
                        // Listing fonts from the array
                        foreach ($signals_google_fonts as $signals_font) {
                            echo '<option value="' . $signals_font . '"' . selected($signals_font, $signals_csmm_options['header_font']) . '>' . $signals_font . '</option>' . "\n";
                        }
                        ?>
                    </select>
                    <h3>This is how the header font is going to look!</h3>
                    <p class="csmm-form-help-block">Choose from over 700 Google Fonts. Make sure other modules use the same, or matching fonts.</p>
                </div>

            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_header_size" class="csmm-strong">Header Text Size</label>

                    <input type="hidden" name="signals_csmm_header_size" value="<?php esc_attr_e($signals_csmm_options['header_font_size']); ?>" data-min="6" data-max="200" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">Make it big and bold.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_header_color" class="csmm-strong">Header Text Color</label>
                    <input type="text" name="signals_csmm_header_color" id="signals_csmm_header_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['header_font_color']); ?>" class="csmm-color csmm-form-control color {required:false}">

                    <p class="csmm-form-help-block">Make sure the header is visible! Use your primary logo color to make it pop.</p>
                </div>
            </div>
        </div>


    </div>
</div><!-- #header -->