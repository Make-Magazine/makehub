<div class="csmm-tile" id="email">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Autoresponder &amp; Emailing Services</div>
        <p>Email settings for the plugin. You can configure your MailChimp account API to store collected emails in a list.</p>

        <div class="csmm-section-content">

            <div class="csmm-double-group clearfix">
                <div class="csmm-form-group">
                    <label for="mail_system_to_use" class="csmm-strong">Select Emailing / Integration System</label>
                    <select id="mail_system_to_use" name="mail_system_to_use" class="csmm-form-control">
                        <option value="mc" <?php if ($signals_csmm_options['mail_system_to_use'] == 'mc') echo ' selected '  ?>><?php _e('MailChimp', 'signals'); ?>
                        <option value="ua" <?php if ($signals_csmm_options['mail_system_to_use'] == 'ua') echo ' selected '  ?>><?php _e('Universal Autoresponder', 'signals'); ?>
                        <option value="zapier" <?php if ($signals_csmm_options['mail_system_to_use'] == 'zapier') echo ' selected '  ?>><?php _e('Zapier/Webhook', 'signals'); ?>
                    </select>
                    <p class="csmm-form-help-block">MailChimp is integrated via the API. For any other services (autoresponders, webinars, CRMs) that can generate an HTML form, use the "Universal Autoresponder" option. 
                        We recommend using Zapier as it seamlessly connects to over 1000 services. You can also use any generic webhook as Coming Soon will send the data (email, name, site_name, site_url, user_ip, user_ua) using query variables.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="mail_debug" class="csmm-strong">Enable Debugging</label>
                    <input type="checkbox" class="csmm-form-ios" name="mail_debug" id="mail_debug" value="1" <?php checked('1', $signals_csmm_options['mail_debug']); ?>>
                    <p class="csmm-form-help-block">If you're having issues with emailing systems enable debugging, open the coming soon page and test the form with some sample emails. Detailed debug information will be shown on the page.</p>
                </div>
            </div>

            <div class="zapier_block_cont single_mail_block">
                <div class="csmm-double-group csmm-clearfix" style="border-bottom: thin solid #0190da9e;">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_message_noemail" class="csmm-strong">Webhook/Zapier URL</label>
                        <input type="text" name="signal_zapier_action_url" id="signal_zapier_action_url" value="<?php echo esc_attr_e($signals_csmm_options['signal_zapier_action_url']); ?>" placeholder="https://hooks.zapier.com/hooks/catch/123456" class="csmm-form-control">
                        <p class="csmm-form-help-block">Enter your webhook URL. If you want to use Zapier, Create a Zap with the "Webhooks by Zapier" as the trigger app and configure it as a "catch hook". Under "View Webhook" you'll see an URL - copy/paste it above.<br /> The following data is delivered with each trigger: email, name, site_name, site_url, user_ip, user_ua.</p>
                    </div>
                </div>
            </div>
            <input type="hidden" name="signals_change_mc_api" id="signals_change_mc_api" value="0">
            <div class="ua_block_cont single_mail_block">
                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signal_ua_action_url" class="csmm-strong">Form Action URL</label>
                        <input type="text" name="signal_ua_action_url" id="signal_ua_action_url" value="<?php echo esc_attr_e($signals_csmm_options['signal_ua_action_url']); ?>" placeholder="https://" class="csmm-form-control">
                        <p class="csmm-form-help-block">Complete action URL, including the http or https prefix.</p>
                    </div>

                    <div class="csmm-form-group">
                        <label for="signal_ua_method" class="csmm-strong">Form Method</label>
                        <select name="signal_ua_method" id="signal_ua_method">
                            <option value="get" <?php if ($signals_csmm_options['signal_ua_method'] == 'get') echo ' selected ';  ?>><?php _e('GET', 'signals'); ?>
                            <option value="post" <?php if ($signals_csmm_options['signal_ua_method'] == 'post') echo ' selected ';  ?>><?php _e('POST', 'signals'); ?>
                        </select>
                        <p class="csmm-form-help-block">If the form is not working try switching from POST to GET and vice versa.</p>
                    </div>
                </div>


                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signal_ua_email_field_name" class="csmm-strong">Email Field Name</label>
                        <input type="text" name="signal_ua_email_field_name" id="signal_ua_email_field_name" value="<?php echo esc_attr_e($signals_csmm_options['signal_ua_email_field_name']); ?>" placeholder="email" class="csmm-form-control">
                        <p class="csmm-form-help-block">Name of the form field that contains the email address. In most cases it's "email".</p>
                    </div>

                    <div class="csmm-form-group">
                        <label for="signal_ua_name_field_name" class="csmm-strong">Name Field Name</label>
                        <input type="text" name="signal_ua_name_field_name" id="signal_ua_name_field_name" value="<?php echo esc_attr_e($signals_csmm_options['signal_ua_name_field_name']); ?>" placeholder="name" class="csmm-form-control">
                        <p class="csmm-form-help-block">Name of the form field that contains the user's name. In most cases it's "name" or "fname".</p>
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix" style="border-bottom: thin solid #0190da9e;">
                    <div class="csmm-form-group">
                        <label for="signal_ua_additional_data" class="csmm-strong">Extra Data</label>
                        <input type="text" name="signal_ua_additional_data" id="signal_ua_additional_data" value="<?php echo esc_attr_e($signals_csmm_options['signal_ua_additional_data']); ?>" placeholder="field1=value1&field2=value2&field3=value3" class="csmm-form-control">
                        <p class="csmm-form-help-block">Additional, fixed form data to send; ie the form ID. Please write it in URL format: <i>field1=value1&field2=value2&field3=value3</i>.</p>
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_autoconfigure" class="csmm-strong">AutoConfigure Autoresponder</label>
                        <textarea rows="10" class="csmm-form-control" name="signals_autoconfigure" id="signals_autoconfigure" placeholder="Complete HTML code for the form, generated by your emailing, CRM or webinar system."></textarea>
                        <div data-default="Copy paste the HTML form code generated by your emailing system in the field above. After the form is parsed click the Populate fields button to use those values and don't forget to save settings." id="form-fields-preview"></div>
                        <br />
                        <div class="csmm-btn" id="prepopulate_fields"><strong>Populate fields with detected values</strong></div>
                    </div>
                </div>
            </div>

            <div class="mc_block_cont single_mail_block">
                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_api" class="csmm-strong">MailChimp API Key</label>
                        <input type="text" name="signals_csmm_api" id="signals_csmm_api" value="<?php esc_attr_e($signals_csmm_options['mailchimp_api']); ?>" placeholder="<?php esc_attr_e('MailChimp API key', 'signals'); ?>" class="csmm-form-control">
                        <p class="csmm-form-help-block">MailChimp API key is located in your <a href="https://us2.admin.mailchimp.com/account/api/" target="_blank">MailChimp account</a> under account, extras, API keys.</p>
                        <p><button type="submit" id="submit-save-api" name="signals_csmm_submit" class="csmm-btn">Save API key &amp; refresh Audiences</button></p>
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix" style="border-bottom: thin solid #0190da9e;">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_list" class="csmm-strong">MailChimp Audience Name</label>
                        <?php

                        // Checking if the API key is present in the database
                        if (!empty($signals_csmm_options['mailchimp_api'])) {
                            // Grabbing lists using the MailChimp API
                            $signals_lists     = $signals_csmm_options['mc_lists'];

                            if ($signals_lists === false) {
                                echo '<p class="csmm-form-help-block">' . __('<b>Error</b> fetching Audiences. Please make sure that the API key you entered is correct and try again.', 'signals') . '</p>';
                            } else if (sizeof($signals_lists) == 0) {
                                echo '<p class="csmm-form-help-block">' . __('It seems that there are no Audiences created for this account. Create one on the MailChimp website and then try again.', 'signals') . '</p>';
                            }
                            else {
                                echo '<select name="signals_csmm_list" id="signals_csmm_list">';
                                echo '<option value="">- select a audience -</option>';
                                foreach ($signals_lists as $tmp) {
                                    echo '<option value="' . $tmp['val'] . '"' . selected($tmp['val'], $signals_csmm_options['mailchimp_list']) . '>' . $tmp['label'] . '</option>';
                                }
                                echo '</select>';
                                echo '<p class="csmm-form-help-block">' . __('Select the MailChimp Audience in which you want to store the subscriber data.', 'signals') . '</p>';
                            }
                        } else {
                            echo '<select disabled name="signals_csmm_list" id="signals_csmm_list"><option value="">Unable to fetch Audiences. Check you API key.</option></select><br>';
                            echo '<p class="csmm-form-help-block">' . __('Enter your MailChimp API key in the field above and click "Save API key". Your Audiences will refresh and appear here.', 'signals') . '</p>';
                        }
                        ?>
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_double_optin" class="csmm-strong">Double Opt-In</label>
                        <input type="checkbox" class="csmm-form-ios" name="signals_double_optin" id="signals_double_optin" value="1" <?php checked('1', $signals_csmm_options['signals_double_optin']); ?>>
                        <p class="csmm-form-help-block">The double opt-in process includes two steps. First, the potential subscriber fills out and submits your signup form. Then, they'll receive a confirmation email and click a link to verify their email, which is then added to your MailChimp list.</p>
                    </div>
                </div>

            </div>


            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_message_noemail" class="csmm-strong">Error Message: "Invalid Email Address"</label>
                    <input type="text" name="signals_csmm_message_noemail" id="signals_csmm_message_noemail" value="<?php echo esc_attr_e($signals_csmm_options['message_noemail']); ?>" placeholder="Please enter a valid email address." class="csmm-form-control">
                    <p class="csmm-form-help-block">The error message displayed when a user enters an invalid email address or leaves the field blank.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_message_no_name" class="csmm-strong">Error Message: "Invalid Name"</label>
                    <input type="text" name="signals_csmm_message_no_name" id="signals_csmm_message_no_name" value="<?php echo esc_attr($signals_csmm_options['message_no_name']); ?>" placeholder="Please provide a valid name." class="csmm-form-control">
                    <p class="csmm-form-help-block">The error message displayed when a user enters an invalid name or leaves the field blank. If you don't see a name field in your form enable it in <a href="#design-form" class="csmm-change-tab">Form module</a>.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_message_wrong" class="csmm-strong">Error Message: "General Error"</label>
                    <input type="text" name="signals_csmm_message_wrong" id="signals_csmm_message_wrong" value="<?php echo esc_attr($signals_csmm_options['message_wrong']); ?>" placeholder="Unknown error. Please reload the page and try again." class="csmm-form-control">
                    <p class="csmm-form-help-block">The error message displayed when an undocumented, technical error occurs.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_message_done" class="csmm-strong">Message: "Successfully Subscribed"</label>
                    <input type="text" name="signals_csmm_message_done" id="signals_csmm_message_done" value="<?php echo esc_attr($signals_csmm_options['message_done']); ?>" placeholder="Thank you for subscribing!" class="csmm-form-control">
                    <p class="csmm-form-help-block">The message displayed when a user successfully subscribes.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_message_subscribed" class="csmm-strong">Message: "Already Subscribed"</label>
                    <input type="text" name="signals_csmm_message_subscribed" id="signals_csmm_message_subscribed" value="<?php echo esc_attr_e($signals_csmm_options['message_subscribed']); ?>" placeholder="That email is already subscribed to our list." class="csmm-form-control">
                    <p class="csmm-form-help-block">The error message displayed when the entered email address is already subscribed to the selected list.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_recaptcha" class="csmm-strong">Show reCAPTCHA</label>
                    <select name="csmm_recaptcha" id="csmm_recaptcha">
                        <?php
                        $bkg_opt = array(
                            array('val' => 'disabled', 'label' => 'Disabled'),
                            array('val' => 'v2', 'label' => 'reCAPTCHA v2'),
                            array('val' => 'v3', 'label' => 'reCAPTCHA v3'),
                        );
                        csmm_create_select_options($bkg_opt, $signals_csmm_options['recaptcha']);  ?>
                    </select>

                    <p class="csmm-form-help-block">Select recaptcha v2 (Shows the "I'm not a robot" Checkbox) or v3 (Verify requests with a score, shows the reCAPTCHA logo in the page corner)</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_recaptcha_site_key" class="csmm-strong">reCAPTCHA Site Key</label>
                    <input type="text" name="csmm_recaptcha_site_key" id="csmm_recaptcha_site_key" value="<?php echo esc_attr($signals_csmm_options['recaptcha_site_key']); ?>" placeholder="" class="csmm-form-control">
                    <p class="csmm-form-help-block">Site key in the HTML code your site serves to users.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="csmm_recaptcha_secret_key" class="csmm-strong">reCAPTCHA Secret Key</label>
                    <input type="text" name="csmm_recaptcha_secret_key" id="csmm_recaptcha_secret_key" value="<?php echo esc_attr($signals_csmm_options['recaptcha_secret_key']); ?>" placeholder="" class="csmm-form-control">
                    <p class="csmm-form-help-block">A secret key for communication between your site and reCAPTCHA.</p>
                </div>
            </div>

            



        </div>
    </div>
</div><!-- #autoresponders -->