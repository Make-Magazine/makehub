<?php

/**
 * Settings panel view for the plugin
 *
 * @link       http://www.webfactoryltd.com
 * @since      0.1
 * @package    Signals_Maintenance_Mode
 */

require_once 'header.php';
require_once CSMM_PATH . 'framework/admin/include/fonts.php';
?>

<form role="form" method="post" class="csmm-admin-form" enctype="multipart/form-data">
    <input type="hidden" name="signals_doing_save" id="signals_doing_save" value="0">
    <input type="hidden" name="signals_csmm_status" id="signals_csmm_status" value="<?php echo (string) $signals_csmm_options['status']; ?>">
    <div class="csmm-body csmm-clearfix">
        <?php
        // Display the message if $signals_csmm_err is set
        $signals_csmm_err = get_transient('signals_csmm_err_' . get_current_user_id());
        delete_transient('signals_csmm_err_' . get_current_user_id());
        if ($signals_csmm_err !== false) {
            echo $signals_csmm_err;
        }
        if (get_transient('csmm_error_msg')) {
            echo get_transient('csmm_error_msg');
        }

        $current_user = wp_get_current_user();
        $name = '';
        if (!empty($current_user->user_firstname)) {
            $name = ' ' . $current_user->user_firstname;
        }
        $meta = csmm_get_meta();

        
        ?>

        <div class="csmm-float-left">
            <div class="csmm-mobile-menu">
                <a href="#">
                    <img src="<?php echo CSMM_URL; ?>/framework/admin/img/toggle.png" />
                </a>
            </div>

            <ul class="csmm-main-menu">
                <?php if ($csmm_lc->is_active()) { ?>
                    <li><a title="Dashboard" href="#dashboard">Dashboard</a></li>
                    <li><a title="SEO" href="#seo">SEO</a></li>
                    <li><a title="Themes" class="parent-menu" href="#themes">Themes</a>
                        <div class="csmm-submenu">
                            <a title="Premade" href="#themes">Premade</a>
                            <a title="User Made" href="#themes-user">User Made</a>
                        </div>
                    </li>
                    <li>
                        <a title="Design" class="parent-menu" href="#design">Design</a>
                        <div class="csmm-submenu">
                            <a title="Layout" href="#design-layout">Layout</a>
                            <div class="csmm-design-layout-modules" <?php echo ($signals_csmm_options['mode'] != 'layout'?'style="display:none;"':''); ?>>
                                <a title="Background" href="#design-background">Background</a>
                                <a title="Logo" href="#design-logo"><span class="dashicons"></span>Logo</a>
                                <a title="Header" href="#design-header"><span class="dashicons"></span>Header</a>
                                <a title="Content" href="#design-content"><span class="dashicons"></span>Content</a>
                                <a title="2 Column Content" href="#design-content2col"><span class="dashicons"></span>2 Column Content</a>
                                <a title="Divider" href="#design-divider"><span class="dashicons"></span>Divider</a>
                                <a title="Video" href="#design-video"><span class="dashicons"></span>Video</a>
                                <a title="Map" href="#design-map"><span class="dashicons"></span>Map</a>
                                <a title="Subscribe Form" href="#design-form"><span class="dashicons"></span>Subscribe Form</a>
                                <a title="Contact Form" href="#design-contact"><span class="dashicons"></span>Contact Form</a>
                                <a title="Social" href="#design-social"><span class="dashicons"></span>Social Icons</a>
                                <a title="Countdown" href="#design-countdown"><span class="dashicons"></span>Countdown</a>
                                <a title="Progress Bar" href="#design-progress"><span class="dashicons"></span>Progress Bar</a>
                                <a title="HTML" href="#design-html"><span class="dashicons"></span>Custom HTML</a>
                            </div>
                        </div>
                    </li>
                    <li><a title="Autoresponder Services" href="#email">Autoresponder &amp; Emailing Services</a></li>
                    <li><a title="Access" href="#access">Access</a></li>
                    <li><a title="Password Protect" href="#password">Password Protect</a></li>
                    <li><a title="Advanced" href="#advanced">Advanced</a></li>
                    <li><a title="Custom Code" href="#custom">Custom Code</a></li>
                <?php
                }
                if (csmm_whitelabel_filter()) { ?>
                    <li><a title="License" href="#license">License</a></li>
                    <li><a title="Support" href="#support">Support</a></li>
                <?php } ?>
                <?php do_action('csmm_tabs_list'); ?>
            </ul>
        </div><!-- .csmm-float-left -->

        <div class="csmm-float-right">
            <div id="csmm-ajax-notification" style="display: none;">
                <p><span class="dashicons dashicons-yes"></span> Settings have been saved!</p>
            </div>
            <?php
            // Including tabs content

            if ($csmm_lc->is_active()) {
                require_once 'settings-dashboard.php';
                require_once 'settings-seo.php';
                require_once 'settings-themes.php';
                require_once 'settings-themes-user.php';

                require_once 'settings-design-layout.php';
                require_once 'settings-design-background.php';
                require_once 'settings-design-logo.php';
                require_once 'settings-design-header.php';
                require_once 'settings-design-content.php';
                require_once 'settings-design-content2col.php';
                require_once 'settings-design-divider.php';
                require_once 'settings-design-video.php';
                require_once 'settings-design-map.php';
                require_once 'settings-design-form.php';
                require_once 'settings-design-contact.php';
                require_once 'settings-design-social.php';
                require_once 'settings-design-countdown.php';
                require_once 'settings-design-progress.php';
                require_once 'settings-design-html.php';

                require_once 'settings-email.php';
                require_once 'settings-access.php';
                require_once 'settings-password.php';
                require_once 'settings-advanced.php';
                require_once 'settings-custom.php';
            }

            if (csmm_whitelabel_filter()) {
                require_once 'settings-license.php';
                require_once 'settings-support.php';
            }

            do_action('csmm_tabs_content');
            ?>
            <div class="csmm-tile csmm-tile-first"><span class="dashicons dashicons-update"></span></div>
        </div><!-- .csmm-float-right -->

        <?php if ($csmm_lc->is_active()) { ?>
            <div class="csmm-fixed-save-btn">
                <div>
                    <p class="footer-buttons-left">
                        <button data-caption="Save Changes <i style='font-weight: normal;'>(Ctrl+Shift+S)</i>" name="signals_csmm_submit" id="signals_csmm_submit" class="csmm-btn csmm-btn-red"><span class="dashicons dashicons-update"></span> <strong>Save Changes <i style="font-weight: normal;">(Ctrl+Shift+S)</i></strong></button>
                        <a id="csmm-preview" style="margin: 0 0 0 15px;" href="<?php echo home_url('/'); ?>?csmm_preview" class="csmm-btn" target="_blank"><strong>Preview Page</strong></a>
                        <a id="save-theme" style="margin: 0 0 0 15px;" href="#" class="csmm-btn"><strong>Save Theme</strong></a>
                    </p>
                    <?php if (csmm_whitelabel_filter()) { ?>
                        <p class="footer-buttons-right">
                            <?php
                            if(csmm_get_rebranding() !== false){
                                echo csmm_get_rebranding('footer_text');
                            } else {
                                echo 'Thank you for creating with <a href="https://comingsoonwp.com/" target="_blank">Coming Soon PRO</a> v' . csmm_get_plugin_version();
                            }
                            ?>
                        </p>
                    <?php } ?>
                </div><!-- .csmm-tile-body -->
            </div><!-- .csmm-fixed-save-btn -->
        <?php } ?>
    </div><!-- .csmm-body -->
</form><!-- form.csmm-admin-form -->

<?php

//csmm_onboarding();

require_once 'footer.php';
