<div class="csmm-tile" id="design-form">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Subscribe Form Module</div>
        <p>Leads are the lifeline of any business. Make sure your form looks trustworthy. Configure technical details on the <a href="#email" class="csmm-change-tab">autoresponder tab</a>.</p>

        <div class="csmm-section-content">
            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_show_name" class="csmm-strong">Show Name Field</label>
                    <input type="checkbox" class="csmm-form-ios" name="signals_show_name" id="signals_show_name" value="1" <?php checked('1', $signals_csmm_options['signals_show_name']); ?>>
                    <p class="csmm-form-help-block">It's preferable to ask for a name as it gives you the option to personalize communication later on.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_message_noname" class="csmm-strong">Name Field Placeholder Text</label>
                    <input type="text" name="signals_csmm_message_noname" id="signals_csmm_message_noname" value="<?php echo esc_attr_e($signals_csmm_options['signals_csmm_message_noname']); ?>" placeholder="How shall we call you?" class="csmm-form-control">
                    <p class="csmm-form-help-block">Make sure visitors understand what they need to write in this field.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_input_text" class="csmm-strong">Email Field Placeholder Text</label>
                    <input type="text" name="signals_csmm_input_text" id="signals_csmm_input_text" value="<?php esc_attr_e(stripslashes($signals_csmm_options['input_text'])); ?>" placeholder="Enter your best email address" class="csmm-form-control">
                    <p class="csmm-form-help-block">Make sure visitors understand what they need to write in this field.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_button_text" class="csmm-strong">Subscribe Button Text</label>
                    <input type="text" name="signals_csmm_button_text" id="signals_csmm_button_text" value="<?php esc_attr_e(stripslashes($signals_csmm_options['button_text'])); ?>" placeholder="Subscribe NOW" class="csmm-form-control">
                    <p class="csmm-form-help-block">Use action words like: new, now, limited, free, instant. Or just go for the classic "Send Message".</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_gdpr_text" class="csmm-strong">GDPR Consent Checkbox Text</label>
                    <input name="signals_csmm_gdpr_text" id="signals_csmm_gdpr_text" type="text" placeholder="Terms users have to accept to subscribe" class="csmm-form-control" value="<?php esc_attr_e($signals_csmm_options['gdpr_text']); ?>">
                    <p class="csmm-form-help-block">A checkbox and the text above are displayed below the form email field. The user has to check the checkbox to subscribe. Leave empty if you don't want to display the checkbox. You can use HTML to link to your privacy policy page or use [policy_popup]Privacy Policy[/policy_popup] to open a popup with the content below.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_gdpr_error_text" class="csmm-strong">Error Message: "GDPR Box Not Checked"</label>
                    <input type="text" name="signals_csmm_gdpr_error_text" id="signals_csmm_gdpr_error_text" value="<?php echo esc_attr_e(stripslashes($signals_csmm_options['gdpr_error_text'])); ?>" placeholder="I understand the site\'s privacy policy and am willingly sharing my email address" class="csmm-form-control">
                    <p class="csmm-form-help-block">Error message displayed when the user does not check the GDPR checkbox.</p>
                </div>
            </div>

            <div class="csmm-form-group">
                <label for="signals_csmm_gdpr_policy_text" class="csmm-strong">GDPR Privacy Policy Popup Text</label>
                <?php wp_editor(stripslashes($signals_csmm_options['gdpr_policy_text']), 'signals_csmm_gdpr_policy_text', $settings = array(
                    'textarea_rows' => 10,
                    'media_buttons' => 1,
                    'teeny' => false
                )); ?>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_antispam" class="csmm-strong">Anti Spam Text</label>
                    <input type="text" name="signals_csmm_antispam" id="signals_csmm_antispam" value="<?php echo esc_attr_e(stripslashes($signals_csmm_options['antispam_text'])); ?>" placeholder="We hate SPAM as much as you do" class="csmm-form-control">
                    <p class="csmm-form-help-block">We all hate SPAM, make sure visitors know you hate it too and won't bother them.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="submit_align" class="csmm-strong">Subscribe Button Position</label>
                    <select name="submit_align" id="submit_align">
                        <?php
                        $positions = array(
                            array('val' => 'left', 'label' => 'Left'),
                            array('val' => 'center', 'label' => 'Center'),
                            array('val' => 'right', 'label' => 'Right'),
                        );
                        csmm_create_select_options($positions, $signals_csmm_options['submit_align']);  ?>
                    </select>

                    <p class="csmm-form-help-block">The horizontal position of the subscribe button below the email field.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_antispam_size" class="csmm-strong">Antispam Text Size</label>

                    <input type="hidden" name="signals_csmm_antispam_size" value="<?php esc_attr_e($signals_csmm_options['antispam_font_size']); ?>" data-min="6" data-max="200" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">Keep it smaller than the rest of the text, or make it less visible with a muted color.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_antispam_color" class="csmm-strong">Antispam Text Color</label>
                    <input type="text" name="signals_csmm_antispam_color" id="signals_csmm_antispam_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['antispam_font_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_success_bg" class="csmm-strong">Success Message Background Color</label>
                    <input type="text" name="signals_csmm_success_bg" id="signals_csmm_success_bg" value="<?php echo csmm_hex2rgba($signals_csmm_options['success_background']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_success_color" class="csmm-strong">Success Message Text Color</label>
                    <input type="text" name="signals_csmm_success_color" id="signals_csmm_success_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['success_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_error_bg" class="csmm-strong">Error Message Background Color</label>
                    <input type="text" name="signals_csmm_error_bg" id="signals_csmm_error_bg" value="<?php echo csmm_hex2rgba($signals_csmm_options['error_background']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_error_color" class="csmm-strong">Error Message Text Color</label>
                    <input type="text" name="signals_csmm_error_color" id="signals_csmm_error_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['error_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="form_placeholder_color" class="csmm-strong">Input Fields Placeholder Color</label>
                    <input type="text" name="form_placeholder_color" id="form_placeholder_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['form_placeholder_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_ignore_styles" class="csmm-strong">Customize Form Styles</label>
                    <input type="checkbox" class="csmm-form-ios" name="signals_csmm_ignore_styles" id="signals_csmm_ignore_styles" value="1" <?php checked('1', $signals_csmm_options['ignore_form_styles']); ?>>
                    <p class="csmm-form-help-block">If disabled, all form styles will be default (ugly) browser ones. Use this option if you plan on adding your own <a href="#advanced" class="csmm-change-tab">custom CSS</a> to fully style the form.</p>
                </div>
            </div>

            <div id="custom-form-styles">
                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_input_size" class="csmm-strong">Input Text Size</label>
                        <input type="hidden" name="signals_csmm_input_size" value="<?php esc_attr_e($signals_csmm_options['input_font_size']); ?>" data-min="6" data-max="200" data-step="1" data-label="%val%px" class="csmm-slide-input">
                        <p class="csmm-form-help-block">Font size for all input fields.</p>
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_csmm_button_size" class="csmm-strong">Button Text Size</label>
                        <input type="hidden" name="signals_csmm_button_size" value="<?php esc_attr_e($signals_csmm_options['button_font_size']); ?>" data-min="6" data-max="200" data-step="1" data-label="%val%px" class="csmm-slide-input">
                        <p class="csmm-form-help-block">Font size for the subscribe button.</p>
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_input_color" class="csmm-strong">Input Text Color</label>
                        <input type="text" name="signals_csmm_input_color" id="signals_csmm_input_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['input_font_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_csmm_button_color" class="csmm-strong">Button Text Color</label>
                        <input type="text" name="signals_csmm_button_color" id="signals_csmm_button_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['button_font_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_input_bg" class="csmm-strong">Input Background Color</label>
                        <input type="text" name="signals_csmm_input_bg" id="signals_csmm_input_bg" value="<?php echo csmm_hex2rgba($signals_csmm_options['input_bg']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_csmm_button_bg" class="csmm-strong">Button Background Color</label>
                        <input type="text" name="signals_csmm_button_bg" id="signals_csmm_button_bg" value="<?php echo csmm_hex2rgba($signals_csmm_options['button_bg']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_input_bg_hover" class="csmm-strong">Input Focus Background Color</label>
                        <input type="text" name="signals_csmm_input_bg_hover" id="signals_csmm_input_bg_hover" value="<?php echo csmm_hex2rgba($signals_csmm_options['input_bg_hover']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_csmm_button_bg_hover" class="csmm-strong">Button Hover Background Color</label>
                        <input type="text" name="signals_csmm_button_bg_hover" id="signals_csmm_button_bg_hover" value="<?php echo csmm_hex2rgba($signals_csmm_options['button_bg_hover']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_input_border" class="csmm-strong">Input Border Color</label>
                        <input type="text" name="signals_csmm_input_border" id="signals_csmm_input_border" value="<?php echo csmm_hex2rgba($signals_csmm_options['input_border']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_csmm_button_border" class="csmm-strong">Button Border Color</label>
                        <input type="text" name="signals_csmm_button_border" id="signals_csmm_button_border" value="<?php echo csmm_hex2rgba($signals_csmm_options['button_border']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_input_border_hover" class="csmm-strong">Input Focus Border Color</label>
                        <input type="text" name="signals_csmm_input_border_hover" id="signals_csmm_input_border_hover" value="<?php echo csmm_hex2rgba($signals_csmm_options['input_border_hover']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_csmm_button_border_hover" class="csmm-strong">Button Hover Border Color</label>
                        <input type="text" name="signals_csmm_button_border_hover" id="signals_csmm_button_border_hover" value="<?php echo csmm_hex2rgba($signals_csmm_options['button_border_hover']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div><!-- #form -->