<div class="csmm-tile" id="advanced">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Advanced Settings</div>
        <p>Please double-check any custom code you enter in the settings below. Any typos or mistakes will affect the appearance of the page.</p>

        <div class="csmm-section-content">

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_nocache" class="csmm-strong">Send no-cache Headers</label>
                    <input type="checkbox" class="csmm-form-ios" id="csmm_nocache" name="csmm_nocache" value="1" <?php checked('1', $signals_csmm_options['nocache']); ?>>

                    <p class="csmm-form-help-block">If you don't want the coming soon page's preview to be cached by Facebook and other social media, enable this option. Once you switch to the normal site, social media preview (visible when sharing the site's link) will immediately be refreshed. Normal visitors won't notice any differences with the option enabled.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_disable_adminbar" class="csmm-strong">Disable Coming Soon Toolbar Menu</label>
                    <input type="checkbox" class="csmm-form-ios" id="signals_disable_adminbar" name="signals_disable_adminbar" value="1" <?php checked('1', $signals_csmm_options['disable_adminbar']); ?>>
                    <p class="csmm-form-help-block">By default, a helpful Coming Soon menu and status are added to the admin and front-end toolbar. If your toolbar is too crowded, disable the menu.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_forcessl" class="csmm-strong">Force SSL on Coming Soon Page</label>
                    <input type="checkbox" class="csmm-form-ios" id="csmm_forcessl" name="csmm_forcessl" value="1" <?php checked('1', $signals_csmm_options['forcessl']); ?>>

                    <p class="csmm-form-help-block">If you have a valid SSL certificate installed on your site but people are still visiting the non-secure HTTP version, you can redirect them to HTTPS. The redirection only works for the coming soon page, not for the entire site and not for the preview.<br>
                        DO NOT enable this option unless you have a valid SSL certificate installed. Check if you do by opening your site via the HTTPS protocol - <i><?php echo '<a href="' . str_ireplace('http://', 'https://', home_url('/')) . '" target="_blank">' . str_ireplace('http://', 'https://', home_url('/')) . '</a>'; ?></i></p>
                </div>

                <div class="csmm-form-group">
                    <label for="csmm_wprest" class="csmm-strong">Enable WordPress Rest API</label>
                    <input type="checkbox" class="csmm-form-ios" id="csmm_wprest" name="csmm_wprest" value="1" <?php checked('1', $signals_csmm_options['wprest']); ?>>

                    <p class="csmm-form-help-block">Allow WordPress REST API calls while Coming Soon mode is enabled.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_track_stats" class="csmm-strong">Track visitor stats</label>
                    <input type="checkbox" class="csmm-form-ios" id="csmm_track_stats" name="csmm_track_stats" value="1" <?php checked('1', $signals_csmm_options['track_stats']); ?>>

                    <p class="csmm-form-help-block">If enabled, Coming Soon & Maintenance Mode will track visitors that browse your website while Coming Soon page is enabled and you will see visitor statistics such as daily visits, country, location, device and traffic type, in the Dashboard tab.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <a class="csmm-btn" id="csmm-export-settings" href="<?php echo add_query_arg(array('action' => 'csmm_export_settings'), admin_url('admin.php')); ?>">Export Settings</a>
                    <p class="csmm-form-help-block">All settings are exported except license details. You can safely transfer (export and then import) settings between different domains/sites.</p>
                </div>
                <div class="csmm-form-group">
                    <input type="file" name="csmm_settings_import" id="csmm_settings_import" accept=".txt"> <input type="submit" name="submit-import" id="submit-import" class="csmm-btn" data-confirm="Are you sure you want to import settings? All current settings will be overwritten. There is NO UNDO!" value="Import Settings">
                    <p class="csmm-form-help-block">All settings are imported and overwritten except license details.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <a class="csmm-btn confirm-action" data-confirm="Are you sure you want to reset all settings? There is NO undo!" id="csmm-reset-settings" href="<?php echo add_query_arg(array('action' => 'csmm_reset_settings', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')); ?>">Reset Settings</a>
                    <p class="csmm-form-help-block">All settings are reset to default values except license details. There is NO undo.</p>
                </div>

                <div class="csmm-form-group">
                    <a class="csmm-btn confirm-action" data-confirm="Are you sure you want to reset the stats? There is NO undo!" id="csmm-reset-stats" href="<?php echo add_query_arg(array('action' => 'csmm_reset_stats', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')); ?>">Reset Stats</a>
                    <p class="csmm-form-help-block">All stats will be reset. There is NO undo.</p>
                </div>
            </div>


            <div class="csmm-form-group">
                <label for="signals_csmm_wpm_replace" class="csmm-strong">Customize the built-in WordPress maintenance page</label>
                <input type="checkbox" class="csmm-form-ios" id="signals_csmm_wpm_replace" name="signals_csmm_wpm_replace" value="1" <?php checked('1', $signals_csmm_options['wpm_replace']); ?>>
                <p class="csmm-form-help-block">The built-in WordPress maintenance mode page is automatically shown to everybody (visitors and admins) for a few moments when WordPress is doing updates to themes, plugins, or core. It's a built-in feature and its behaviour can't be changed. This page can't contain any interactive elements such as a contact forms because when updates are performed the database and some files might not be accessible.<br>
              By default the page is quite ugly and the content can't be easily changed (you can read about it in this <a href="https://wpreset.com/disable-enable-modify-wordpress-maintenance-page/" target="_blank">article</a>). This option enables you do have a nicer maintenance page with your custom text on it. It's still automatically enabled and disabled by WordPress.</p>
                <?php
                if(file_exists(trailingslashit(WP_CONTENT_DIR) . 'maintenance.php')){
                    $maintenance_contents = file_get_contents(trailingslashit(WP_CONTENT_DIR) . 'maintenance.php');
                    if(stripos($maintenance_contents, '<!-- CSMM -->') === false){
                        echo '<p class="csmm-red-color">A previous maintenance.php file already exists and will be overwritten!</p>';
                    }
                }
                ?>
                <p></p>
            </div>

            <div id="signals_csmm_wpm_wrapper">
                <div class="csmm-form-group">
                    <label for="signals_csmm_wpm_title" class="csmm-strong">Maintenance page title</label>
                    <span style="vertical-align: middle;"><input type="text" name="signals_csmm_wpm_title" id="signals_csmm_wpm_title" value="<?php echo stripslashes($signals_csmm_options['wpm_title']); ?>" placeholder="" class="csmm-form-control"></span>
                </div>

                <div class="csmm-section-content">
                    <label for="signals_csmm_wpm_content" class="csmm-strong">Maintenance page content</label>
                    <div class="csmm-form-group">
                        <?php wp_editor(stripslashes($signals_csmm_options['wpm_content']), 'signals_csmm_wpm_content', $settings = array(
                            'textarea_rows' => 10,
                            'media_buttons' => 1,
                            'teeny' => false,
                            'editor_class' => 'skip_save'
                        )); ?>
                    </div>
                </div>

                <a class="csmm-btn" target="_blank" href="<?php echo trailingslashit(WP_CONTENT_URL) . 'maintenance.php'; ?>">Preview</a>
            </div>

        </div>
    </div>
</div><!-- #advanced -->