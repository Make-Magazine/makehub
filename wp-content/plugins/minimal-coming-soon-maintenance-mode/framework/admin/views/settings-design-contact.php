<div class="csmm-tile" id="design-contact">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Contact Form Module</div>
        <p>Let people get in touch if they have any questions. Make sure your form looks trustworthy.</p>

        <div class="csmm-section-content">
            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_admin_email" class="csmm-strong">Admin Email</label>
                    <input type="text" name="csmm_contact_admin_email" id="csmm_contact_admin_email" value="<?php echo esc_attr_e(str_replace('gordan@webfactoryltd.com', '', $signals_csmm_options['contact_admin_email'])); ?>" placeholder="Enter your email" class="csmm-form-control">
                    <p class="csmm-form-help-block">Enter the email address where messages should be sent to. Make sure it is correct as Coming Soon will not store a copy of the messages.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="csmm_contact_email_subject" class="csmm-strong">Email Subject</label>
                    <input type="text" name="csmm_contact_email_subject" id="csmm_contact_email_subject" value="<?php echo esc_attr_e($signals_csmm_options['contact_email_subject']); ?>" placeholder="Enter the email subject" class="csmm-form-control">
                    <p class="csmm-form-help-block">What subject should the email you receive have?.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_show_name" class="csmm-strong">Show Name Field</label>
                    <input type="checkbox" class="csmm-form-ios" name="csmm_contact_show_name" id="csmm_contact_show_name" value="1" <?php checked('1', $signals_csmm_options['contact_show_name']); ?>>
                    <p class="csmm-form-help-block">It's preferable to ask for a name as it gives you the option to personalize communication later on.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="csmm_contact_message_noname" class="csmm-strong">Name Field Placeholder Text</label>
                    <input type="text" name="csmm_contact_message_noname" id="csmm_contact_message_noname" value="<?php echo esc_attr_e($signals_csmm_options['contact_message_noname']); ?>" placeholder="How shall we call you?" class="csmm-form-control">
                    <p class="csmm-form-help-block">Make sure visitors understand what they need to write in this field.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_input_text" class="csmm-strong">Email Field Placeholder Text</label>
                    <input type="text" name="csmm_contact_input_text" id="csmm_contact_input_text" value="<?php esc_attr_e(stripslashes($signals_csmm_options['contact_input_text'])); ?>" placeholder="Enter your best email address" class="csmm-form-control">
                    <p class="csmm-form-help-block">Make sure visitors understand what they need to write in this field.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="csmm_contact_button_text" class="csmm-strong">Subscribe Button Text</label>
                    <input type="text" name="csmm_contact_button_text" id="csmm_contact_button_text" value="<?php esc_attr_e(stripslashes($signals_csmm_options['contact_button_text'])); ?>" placeholder="Send Message" class="csmm-form-control">
                    <p class="csmm-form-help-block">Use action words like: new, now, limited, free, instant. Or just go for the classic "Subscribe".</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_message_text" class="csmm-strong">Message Field Placeholder Text</label>
                    <input type="text" name="csmm_contact_message_text" id="csmm_contact_message_text" value="<?php esc_attr_e(stripslashes($signals_csmm_options['contact_message_text'])); ?>" placeholder="Enter your awesome message" class="csmm-form-control">
                    <p class="csmm-form-help-block">Let visitors know what information they should send you.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_gdpr_text" class="csmm-strong">GDPR Consent Checkbox Text</label>
                    <input name="csmm_contact_gdpr_text" id="csmm_contact_gdpr_text" type="text" placeholder="Terms users have to accept to send the message" class="csmm-form-control" value="<?php esc_attr_e($signals_csmm_options['contact_gdpr_text']); ?>">
                    <p class="csmm-form-help-block">A checkbox and the text above are displayed below the form email field. User has to check the checkbox to send the message. Leave empty if you don't want to display the checkbox. You can use HTML to link to your privacy policy page or use [policy_popup]Privacy Policy[/policy_popup] to open a popup with the content below.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="csmm_contact_gdpr_error_text" class="csmm-strong">Error Message: "GDPR Box Not Checked"</label>
                    <input type="text" name="csmm_contact_gdpr_error_text" id="csmm_contact_gdpr_error_text" value="<?php echo esc_attr_e(stripslashes($signals_csmm_options['contact_gdpr_error_text'])); ?>" placeholder="I understand the site\'s privacy policy and am willingly sharing my email address" class="csmm-form-control">
                    <p class="csmm-form-help-block">Error message displayed when the user does not check the GDPR checkbox.</p>
                </div>
            </div>

            <div class="csmm-form-group">
                <label for="csmm_contact_gdpr_policy_text" class="csmm-strong">GDPR Privacy Policy Popup Text</label>
                <?php wp_editor(stripslashes($signals_csmm_options['contact_gdpr_policy_text']), 'csmm_contact_gdpr_policy_text', $settings = array(
                    'textarea_rows' => 10,
                    'media_buttons' => 1,
                    'teeny' => false
                )); ?>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_antispam" class="csmm-strong">Anti Spam Text</label>
                    <input type="text" name="csmm_contact_antispam" id="csmm_contact_antispam" value="<?php echo esc_attr_e(stripslashes($signals_csmm_options['contact_antispam'])); ?>" placeholder="We hate SPAM as much as you do" class="csmm-form-control">
                    <p class="csmm-form-help-block">We all hate SPAM, make sure visitors know you hate it too and won't bother them.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="csmm_contact_submit_align" class="csmm-strong">Subscribe Button Position</label>
                    <select name="csmm_contact_submit_align" id="csmm_contact_submit_align">
                        <?php
                        $positions = array(
                            array('val' => 'left', 'label' => 'Left'),
                            array('val' => 'center', 'label' => 'Center'),
                            array('val' => 'right', 'label' => 'Right'),
                        );
                        csmm_create_select_options($positions, $signals_csmm_options['contact_submit_align']);  ?>
                    </select>

                    <p class="csmm-form-help-block">The horizontal position of the subscribe button below the email field.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_antispam_size" class="csmm-strong">Antispam Text Size</label>

                    <input type="hidden" name="csmm_contact_antispam_size" value="<?php esc_attr_e($signals_csmm_options['contact_antispam_size']); ?>" data-min="6" data-max="50" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">Keep it smaller than the rest of the text, or make it less visible with a muted color.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="csmm_contact_antispam_color" class="csmm-strong">Antispam Text Color</label>
                    <input type="text" name="csmm_contact_antispam_color" id="csmm_contact_antispam_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_antispam_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_success_bg" class="csmm-strong">Success Message Background Color</label>
                    <input type="text" name="csmm_contact_success_bg" id="csmm_contact_success_bg" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_success_bg']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>

                <div class="csmm-form-group">
                    <label for="csmm_contact_success_color" class="csmm-strong">Success Message Text Color</label>
                    <input type="text" name="csmm_contact_success_color" id="csmm_contact_success_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_success_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_error_bg" class="csmm-strong">Error Message Background Color</label>
                    <input type="text" name="csmm_contact_error_bg" id="csmm_contact_error_bg" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_error_bg']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>

                <div class="csmm-form-group">
                    <label for="csmm_contact_error_color" class="csmm-strong">Error Message Text Color</label>
                    <input type="text" name="csmm_contact_error_color" id="csmm_contact_error_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_error_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_placeholder_color" class="csmm-strong">Input Fields Placeholder Color</label>
                    <input type="text" name="csmm_contact_placeholder_color" id="csmm_contact_placeholder_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_placeholder_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_contact_ignore_styles" class="csmm-strong">Customize Form Styles</label>
                    <input type="checkbox" class="csmm-form-ios" name="csmm_contact_ignore_styles" id="csmm_contact_ignore_styles" value="1" <?php checked('1', $signals_csmm_options['contact_ignore_styles']); ?>>
                    <p class="csmm-form-help-block">If disabled, all form styles will be default (ugly) browser ones. Use this option if you plan on adding your own <a href="#advanced" class="csmm-change-tab">custom CSS</a> to fully style the form.</p>
                </div>
            </div>

            <div id="custom-form-styles">
                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="csmm_contact_input_size" class="csmm-strong">Input Text Size</label>
                        <input type="hidden" name="csmm_contact_input_size" value="<?php esc_attr_e($signals_csmm_options['contact_input_size']); ?>" data-min="6" data-max="50" data-step="1" data-label="%val%px" class="csmm-slide-input">
                        <p class="csmm-form-help-block">Font size for all input fields.</p>
                    </div>

                    <div class="csmm-form-group">
                        <label for="csmm_contact_button_size" class="csmm-strong">Button Text Size</label>
                        <input type="hidden" name="csmm_contact_button_size" value="<?php esc_attr_e($signals_csmm_options['contact_button_size']); ?>" data-min="6" data-max="50" data-step="1" data-label="%val%px" class="csmm-slide-input">
                        <p class="csmm-form-help-block">Font size for the subscribe button.</p>
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="csmm_contact_input_color" class="csmm-strong">Input Text Color</label>
                        <input type="text" name="csmm_contact_input_color" id="csmm_contact_input_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_input_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>

                    <div class="csmm-form-group">
                        <label for="csmm_contact_button_color" class="csmm-strong">Button Text Color</label>
                        <input type="text" name="csmm_contact_button_color" id="csmm_contact_button_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_button_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="csmm_contact_input_bg" class="csmm-strong">Input Background Color</label>
                        <input type="text" name="csmm_contact_input_bg" id="csmm_contact_input_bg" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_input_bg']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>

                    <div class="csmm-form-group">
                        <label for="csmm_contact_button_bg" class="csmm-strong">Button Background Color</label>
                        <input type="text" name="csmm_contact_button_bg" id="csmm_contact_button_bg" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_button_bg']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="csmm_contact_input_bg_hover" class="csmm-strong">Input Focus Background Color</label>
                        <input type="text" name="csmm_contact_input_bg_hover" id="csmm_contact_input_bg_hover" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_input_bg_hover']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>

                    <div class="csmm-form-group">
                        <label for="csmm_contact_button_bg_hover" class="csmm-strong">Button Hover Background Color</label>
                        <input type="text" name="csmm_contact_button_bg_hover" id="csmm_contact_button_bg_hover" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_button_bg_hover']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="csmm_contact_input_border" class="csmm-strong">Input Border Color</label>
                        <input type="text" name="csmm_contact_input_border" id="csmm_contact_input_border" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_input_border']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>

                    <div class="csmm-form-group">
                        <label for="csmm_contact_button_border" class="csmm-strong">Button Border Color</label>
                        <input type="text" name="csmm_contact_button_border" id="csmm_contact_button_border" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_button_border']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="csmm_contact_input_border_hover" class="csmm-strong">Input Focus Border Color</label>
                        <input type="text" name="csmm_contact_input_border_hover" id="csmm_contact_input_border_hover" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_input_border_hover']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>

                    <div class="csmm-form-group">
                        <label for="csmm_contact_button_border_hover" class="csmm-strong">Button Hover Border Color</label>
                        <input type="text" name="csmm_contact_button_border_hover" id="csmm_contact_button_border_hover" value="<?php echo csmm_hex2rgba($signals_csmm_options['contact_button_border_hover']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div><!-- #form -->