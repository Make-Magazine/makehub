<div class="csmm-tile" id="access">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Access</div>
        <p>By default, if Coming Soon mode is enabled, all site visitors except the logged in ones (regardless of their user role) will see the Coming Soon page instead of the "normal" site.<br>The easiest way to show the site to clients or friends is to share the Secret Access Link, or whitelist their IP address if it doesn't change too often.</p>

        <div class="csmm-section-content">
            <div class="csmm-double-group  clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_showlogged" class="csmm-strong">Show Normal Site to Logged in Users</label>
                    <input id="signals_csmm_showlogged" type="checkbox" class="csmm-form-ios" name="signals_csmm_showlogged" value="1" <?php checked('1', $signals_csmm_options['show_logged_in']); ?>>

                    <p class="csmm-form-help-block">Logged in users (regardless of their user role) will not be affected by the coming soon mode and will see the "normal" site.</p>
                </div>
                <div class="csmm-form-group">
                    <label for="signals_ip_whitelist" class="csmm-strong">IP Whitelisting</label>

                    <textarea rows="2" class="csmm-form-control" name="signals_ip_whitelist" id="signals_ip_whitelist"><?php echo esc_attr_e($signals_csmm_options['signals_ip_whitelist']); ?></textarea>
                    <p class="csmm-form-help-block">Noted IPs will not be affected by the coming soon mode, and their users will see the "normal" site.<br>Write one IP per line. Wildcards are not supported. If the user's IP changes, he will no longer be whitelisted. Your IP address is: <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
                </div>
            </div>


            <div class="csmm-double-group clearfix">
                <div class="csmm-form-group">
                    <label for="per_url_settings" class="csmm-strong">URL Based Rules</label>
                    <select name="per_url_settings" id="per_url_settings">
                        <option value="" <?php if ($signals_csmm_options['per_url_settings'] == '') echo ' selected ';  ?>><?php _e('Disabled', 'signals'); ?>
                        <option value="whitelist" <?php if ($signals_csmm_options['per_url_settings'] == 'whitelist') echo ' selected ';  ?>><?php _e('Listed URLs will NEVER be affected by coming soon mode', 'signals'); ?>
                        <option value="blacklist" <?php if ($signals_csmm_options['per_url_settings'] == 'blacklist') echo ' selected ';  ?>><?php _e('ONLY listed URLs CAN BE affected by coming soon mode', 'signals'); ?>
                    </select>
                    <p class="csmm-form-help-block">Use this option to set per-URL rules and enable coming soon mode on the entire site except for selected pages, or enable it on just some pages and leave all others accessible to visitors. If the second option is used, all other access rules apply too.</p>
                </div>

                <div class="csmm-form-group per-url-wrapper">
                    <label for="per_url_enable_disable" class="csmm-strong">URL List</label>
                    <textarea rows="3" class="csmm-form-control" name="per_url_enable_disable" id="per_url_enable_disable"><?php echo esc_attr_e($signals_csmm_options['per_url_enable_disable']); ?></textarea>
                    <p class="csmm-form-help-block">Enter one URL per line. Start and end URLs with a forward slash (/).</p>

                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="direct_access_link" class="csmm-strong">Secret Access Link</label>
                    <span style="vertical-align: middle;"><?php echo trailingslashit(get_home_url()); ?>?</span><input type="text" name="direct_access_link" id="direct_access_link" value="<?php echo esc_attr_e($signals_csmm_options['direct_access_link']); ?>" placeholder="preview-full-site" class="csmm-form-control small_80_percent"><a data-base-url="<?php echo trailingslashit(get_home_url()) . '?'; ?>" href="javascript: void(0);" class="copy-secret-link"><span class="dashicons dashicons-clipboard"></span></a>
                    <p class="csmm-form-help-block">Share this link with people who need to see the normal site behind the coming soon page.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_custom_login" class="csmm-strong">Custom Login URL</label>
                    <span style="vertical-align: middle;"><span style="vertical-align: middle;"><?php echo trailingslashit(get_home_url()); ?></span><input type="text" name="signals_csmm_custom_login" id="signals_csmm_custom_login" value="<?php echo esc_attr_e($signals_csmm_options['custom_login_url']); ?>" placeholder="my-login-url/" class="csmm-form-control small_80_percent"></span>
                    <p class="csmm-form-help-block">If you're using a custom login URL and can't access it, enter the custom login URL here. That URL will never be affected by the coming soon mode.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_login_button_toggle" class="csmm-strong">Show Login Button</label>
                    <input id="signals_csmm_login_button_toggle" type="checkbox" class="csmm-form-ios" name="signals_csmm_login_button" value="1" <?php checked('1', (isset($signals_csmm_options['login_button']) ? $signals_csmm_options['login_button'] : '')); ?>>

                    <p class="csmm-form-help-block">Show a discrete button that links to the WordPress login page in the lower right corner of the coming soon page.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_wplogin_button_tooltip" class="csmm-strong">WordPress Login Button Tooltip</label>
                    <span style="vertical-align: middle;"><input type="text" name="signals_csmm_wplogin_button_tooltip" id="signals_csmm_wplogin_button_tooltip" value="<?php echo isset($signals_csmm_options['wplogin_button_tooltip']) ? esc_attr_e($signals_csmm_options['wplogin_button_tooltip']) : 'Access WordPress admin'; ?>" placeholder="" class="csmm-form-control small_80_percent"></span>
                    <p class="csmm-form-help-block">Text for the "Access WordPress admin" button tooltip.</p>
                </div>
            </div>

        </div>
    </div>
</div><!-- #access -->