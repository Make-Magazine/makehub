<div class="csmm-tile" id="password">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Access</div>
        <p>By default, if Coming Soon mode is enabled, all site visitors except the logged in ones (regardless of their user role) will see the Coming Soon page instead of the "normal" site.<br>The easiest way to show the site to clients or friends is to share the Secret Access Link, or whitelist their IP address if it doesn't change too often.</p>

        <div class="csmm-section-content">

            <div class="csmm-double-group csmm-clearfix">

                <div class="csmm-form-group">
                    <label for="signals_csmm_direct_access_password" class="csmm-strong">Direct Access Password</label>
                    <span style="vertical-align: middle;"><input type="text" name="signals_csmm_direct_access_password" id="signals_csmm_direct_access_password" value="<?php echo isset($signals_csmm_options['direct_access_password']) ? esc_attr_e($signals_csmm_options['direct_access_password']) : ''; ?>" placeholder="" class="csmm-form-control small_80_percent"></span>
                    <p class="csmm-form-help-block">Direct Access Password is a user-friendly way (especially when working with clients) to give selected visitors access to the "normal" site. Enter the password and send users the following link: <?php echo '<a href="' . get_home_url() . '/#access-site-form">' . get_home_url() . '/#access-site-form</a>'; ?> or enable the option on the right to show the password form button.<br>The password has to be at least 4 characters long</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_password_button" class="csmm-strong">Password Form Button</label>
                    <input id="signals_csmm_password_button" type="checkbox" class="csmm-form-ios" name="signals_csmm_password_button" value="1" <?php checked('1', (isset($signals_csmm_options['password_button']) ? $signals_csmm_options['password_button'] : '')); ?>>
                    <p class="csmm-form-help-block">Show a discrete button to the direct access password form in the lower right corner of the coming soon page.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_site_password_toggle" class="csmm-strong">Password to Protect the Coming Soon Page</label>
                    <input id="signals_csmm_site_password_toggle" type="checkbox" class="csmm-form-ios" name="signals_csmm_site_password_toggle" value="1" <?php checked('1', (isset($signals_csmm_options['site_password']) && strlen($signals_csmm_options['site_password']) > 3 ? 1 : '')); ?>>
                    <p class="csmm-form-help-block">Protect the entire site with a password you choose. Only those with the password can view the coming soon page.</p>
                </div>

                <div class="csmm-form-group" id="signals_csmm_site_password_wrapper">
                    <label for="signals_csmm_site_password" class="csmm-strong">Password</label>
                    <span style="vertical-align: middle;"><input type="text" name="signals_csmm_site_password" id="signals_csmm_site_password" value="<?php echo isset($signals_csmm_options['site_password']) ? esc_attr_e($signals_csmm_options['site_password']) : ''; ?>" placeholder="" class="csmm-form-control small_80_percent"></span>
                    <p class="csmm-form-help-block">The password has to be at least 4 characters long.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_login_message" class="csmm-strong">Password Popup Message</label>
                    <span style="vertical-align: middle;"><input type="text" name="signals_csmm_login_message" id="signals_csmm_login_message" value="<?php echo isset($signals_csmm_options['login_message']) ? esc_attr_e($signals_csmm_options['login_message']) : ''; ?>" placeholder="" class="csmm-form-control small_80_percent"></span>
                    <p class="csmm-form-help-block">The message displayed in the password popup above the password input. </p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_login_button_text" class="csmm-strong">Password Popup Button Message</label>
                    <span style="vertical-align: middle;"><input type="text" name="signals_csmm_login_button_text" id="signals_csmm_login_button_text" value="<?php echo isset($signals_csmm_options['login_button_text']) ? esc_attr_e($signals_csmm_options['login_button_text']) : ''; ?>" placeholder="" class="csmm-form-control small_80_percent"></span>
                    <p class="csmm-form-help-block">Text for the password popup button.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_login_wrong_password_text" class="csmm-strong">Wrong Password Text</label>
                    <span style="vertical-align: middle;"><input type="text" name="signals_csmm_login_wrong_password_text" id="signals_csmm_login_wrong_password_text" value="<?php echo isset($signals_csmm_options['login_wrong_password_text']) ? esc_attr_e($signals_csmm_options['login_wrong_password_text']) : ''; ?>" placeholder="" class="csmm-form-control small_80_percent"></span>
                    <p class="csmm-form-help-block">Text for the "Wrong Password" message.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_login_button_tooltip" class="csmm-strong">Coming Soon Direct Access Button Tooltip</label>
                    <span style="vertical-align: middle;"><input type="text" name="signals_csmm_login_button_tooltip" id="signals_csmm_login_button_tooltip" value="<?php echo isset($signals_csmm_options['login_button_tooltip']) ? esc_attr_e($signals_csmm_options['login_button_tooltip']) : ''; ?>" placeholder="" class="csmm-form-control small_80_percent"></span>
                    <p class="csmm-form-help-block">Text for the "Direct Access login" button tooltip.</p>
                </div>
            </div>

        </div>
    </div>
</div><!-- #access -->