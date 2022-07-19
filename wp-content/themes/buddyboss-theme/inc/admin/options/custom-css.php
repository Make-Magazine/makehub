<?php
/*
 * Custom CSS
 */
if ( ! function_exists( 'boss_generate_option_css' ) ) {

	function boss_generate_option_css() {

		$custom_css = array();
		if ( is_customize_preview() ) {
			$custom_css = array();
		} else {
			$custom_css = get_transient( 'buddyboss_theme_compressed_custom_css' );
		}

		if ( ! empty( $custom_css ) && isset( $custom_css['css'] ) ) {

			echo "<style id=\"buddyboss_theme-style\">{$custom_css["css"]}</style>";

			return false;
		}

		$primary_color                                  = buddyboss_theme_get_option( 'accent_color' );
		$body_background                                = buddyboss_theme_get_option( 'body_background' );
		$body_blocks                                    = buddyboss_theme_get_option( 'body_blocks' );
		$light_background_blocks                        = buddyboss_theme_get_option( 'light_background_blocks' );
		$body_blocks_border                             = buddyboss_theme_get_option( 'body_blocks_border' );
		$buddyboss_theme_group_cover_bg                 = buddyboss_theme_get_option( 'buddyboss_theme_group_cover_bg' );
		$heading_text_color                             = buddyboss_theme_get_option( 'heading_text_color' );
		$body_text_color                                = buddyboss_theme_get_option( 'body_text_color' );
		$alternate_text_color                           = buddyboss_theme_get_option( 'alternate_text_color' );
		$primary_button_background_regular              = buddyboss_theme_get_option( 'primary_button_background' )['regular'];
		$primary_button_background_hover                = buddyboss_theme_get_option( 'primary_button_background' )['hover'];
		$primary_button_border_regular                  = buddyboss_theme_get_option( 'primary_button_border' )['regular'];
		$primary_button_border_hover                    = buddyboss_theme_get_option( 'primary_button_border' )['hover'];
		$primary_button_text_color_regular              = buddyboss_theme_get_option( 'primary_button_text_color' )['regular'];
		$primary_button_text_color_hover                = buddyboss_theme_get_option( 'primary_button_text_color' )['hover'];
		$secondary_button_background_regular            = buddyboss_theme_get_option( 'secondary_button_background' )['regular'];
		$secondary_button_background_hover              = buddyboss_theme_get_option( 'secondary_button_background' )['hover'];
		$secondary_button_border_regular                = buddyboss_theme_get_option( 'secondary_button_border' )['regular'];
		$secondary_button_border_hover                  = buddyboss_theme_get_option( 'secondary_button_border' )['hover'];
		$secondary_button_text_color_regular            = buddyboss_theme_get_option( 'secondary_button_text_color' )['regular'];
		$secondary_button_text_color_hover              = buddyboss_theme_get_option( 'secondary_button_text_color' )['hover'];
		$header_background                              = buddyboss_theme_get_option( 'header_background' );
		$header_alternate_background                    = buddyboss_theme_get_option( 'header_alternate_background' );
		$header_links                                   = buddyboss_theme_get_option( 'header_links' );
		$header_links_hover                             = buddyboss_theme_get_option( 'header_links_hover' );

		$sidenav_background                             = buddyboss_theme_get_option( 'sidenav_background' );
		$sidenav_text_color_regular                     = buddyboss_theme_get_option( 'sidenav_text_color' )['regular'];
		$sidenav_text_color_hover                       = buddyboss_theme_get_option( 'sidenav_text_color' )['hover'];
		$sidenav_text_color_active                      = buddyboss_theme_get_option( 'sidenav_text_color' )['active'];
		$sidenav_menu_background_color_regular          = buddyboss_theme_get_option( 'sidenav_menu_background_color' )['regular'];
		$sidenav_menu_background_color_hover            = buddyboss_theme_get_option( 'sidenav_menu_background_color' )['hover'];
		$sidenav_menu_background_color_active           = buddyboss_theme_get_option( 'sidenav_menu_background_color' )['active'];
		$sidenav_count_text_color_regular               = buddyboss_theme_get_option( 'sidenav_count_text_color' )['regular'];
		$sidenav_count_text_color_hover                 = buddyboss_theme_get_option( 'sidenav_count_text_color' )['hover'];
		$sidenav_count_text_color_active                = buddyboss_theme_get_option( 'sidenav_count_text_color' )['active'];
		$sidenav_count_background_color_regular         = buddyboss_theme_get_option( 'sidenav_count_background_color' )['regular'];
		$sidenav_count_background_color_hover           = buddyboss_theme_get_option( 'sidenav_count_background_color' )['hover'];
		$sidenav_count_background_color_active          = buddyboss_theme_get_option( 'sidenav_count_background_color' )['active'];

		$footer_background                              = buddyboss_theme_get_option( 'footer_background' );
		$footer_widget_background                       = buddyboss_theme_get_option( 'footer_widget_background' );
		$footer_text_color                              = buddyboss_theme_get_option( 'footer_text_color' );
		$footer_menu_link_color_regular                 = buddyboss_theme_get_option( 'footer_menu_link_color' )['regular'];
		$footer_menu_link_color_hover                   = buddyboss_theme_get_option( 'footer_menu_link_color' )['hover'];
		$footer_menu_link_color_active                  = buddyboss_theme_get_option( 'footer_menu_link_color' )['active'];
		$admin_screen_bgr_color                         = buddyboss_theme_get_option( 'admin_screen_bgr_color' );
		$admin_screen_txt_color                         = buddyboss_theme_get_option( 'admin_screen_txt_color' );
		$login_register_link_color_regular              = buddyboss_theme_get_option( 'login_register_link_color' )['regular'];
		$login_register_link_color_hover                = buddyboss_theme_get_option( 'login_register_link_color' )['hover'];
		$login_register_button_background_color_regular = buddyboss_theme_get_option( 'login_register_button_background_color' )['regular'];
		$login_register_button_background_color_hover   = buddyboss_theme_get_option( 'login_register_button_background_color' )['hover'];
		$login_register_button_border_color_regular     = buddyboss_theme_get_option( 'login_register_button_border_color' )['regular'];
		$login_register_button_border_color_hover       = buddyboss_theme_get_option( 'login_register_button_border_color' )['hover'];
		$login_register_button_text_color_regular       = buddyboss_theme_get_option( 'login_register_button_text_color' )['regular'];
		$login_register_button_text_color_hover         = buddyboss_theme_get_option( 'login_register_button_text_color' )['hover'];
		$label_background_color                         = buddyboss_theme_get_option( 'label_background_color' );
		$label_text_color                               = buddyboss_theme_get_option( 'label_text_color' );
		$tooltip_background                             = buddyboss_theme_get_option( 'tooltip_background' );
		$tooltip_color                                  = buddyboss_theme_get_option( 'tooltip_color' );
		$default_notice_color                           = buddyboss_theme_get_option( 'default_notice_bg_color' );
		$success_color                                  = buddyboss_theme_get_option( 'success_notice_bg_color' );
		$warning_color                                  = buddyboss_theme_get_option( 'warning_notice_bg_color' );
		$danger_color                                   = buddyboss_theme_get_option( 'error_notice_bg_color' );
		$admin_login_heading_color                      = buddyboss_theme_get_option( 'admin_login_heading_color' );
		$header_height                                  = buddyboss_theme_get_option( 'header_height' );
		$header_shadow                                  = buddyboss_theme_get_option( 'header_shadow' );
		$header_sticky                                  = buddyboss_theme_get_option( 'header_sticky' );
		$header_lesson_topic                            = get_body_class();
		$button_radius                                  = buddyboss_theme_get_option( 'button_default_radius' );
		$mobile_logo_size                               = buddyboss_theme_get_option( 'mobile_logo_size' );
		$theme_style                                    = buddyboss_theme_get_option( 'theme_template' );

		?>
		<style id="buddyboss_theme-style">

			<?php
			ob_start();

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			?>

			:root{
				--bb-primary-color: <?php echo $primary_color; ?>;
				--bb-primary-color-rgb: <?php echo join( ', ', hex_2_RGB( $primary_color ) ); ?>;
				--bb-body-background-color: <?php echo $body_background; ?>;
				--bb-content-background-color: <?php echo $body_blocks; ?>;
				--bb-content-alternate-background-color: <?php echo $light_background_blocks; ?>;
				--bb-content-border-color: <?php echo $body_blocks_border; ?>;
				--bb-content-border-color-rgb: <?php echo join( ', ', hex_2_RGB( $body_blocks_border ) ); ?>;
				--bb-cover-image-background-color: <?php echo $buddyboss_theme_group_cover_bg; ?>;
				--bb-headings-color: <?php echo $heading_text_color; ?>;
				--bb-body-text-color: <?php echo $body_text_color; ?>;
				--bb-alternate-text-color: <?php echo $alternate_text_color; ?>;
				--bb-alternate-text-color-rgb: <?php echo join( ', ', hex_2_RGB( $alternate_text_color ) ); ?>;

				--bb-primary-button-background-regular: <?php echo $primary_button_background_regular; ?>;
				--bb-primary-button-background-hover: <?php echo $primary_button_background_hover; ?>;
				--bb-primary-button-border-regular: <?php echo $primary_button_border_regular; ?>;
				--bb-primary-button-border-hover: <?php echo $primary_button_border_hover; ?>;
				--bb-primary-button-border-hover-rgb: <?php echo join( ', ', hex_2_RGB( $primary_button_border_hover ) ); ?>;
				--bb-primary-button-text-regular: <?php echo $primary_button_text_color_regular; ?>;
				--bb-primary-button-text-regular-rgb: <?php echo join( ', ', hex_2_RGB( $primary_button_text_color_regular ) ); ?>;
				--bb-primary-button-text-hover: <?php echo $primary_button_text_color_hover; ?>;
				--bb-primary-button-text-hover-rgb: <?php echo join( ', ', hex_2_RGB( $primary_button_text_color_hover ) ); ?>;
				--bb-secondary-button-background-regular: <?php echo $secondary_button_background_regular; ?>;
				--bb-secondary-button-background-hover: <?php echo $secondary_button_background_hover; ?>;
				--bb-secondary-button-border-regular: <?php echo $secondary_button_border_regular; ?>;
				--bb-secondary-button-border-hover: <?php echo $secondary_button_border_hover; ?>;
				--bb-secondary-button-border-hover-rgb:  <?php echo join( ', ', hex_2_RGB( $secondary_button_border_hover ) ); ?>;
				--bb-secondary-button-text-regular: <?php echo $secondary_button_text_color_regular; ?>;
				--bb-secondary-button-text-hover: <?php echo $secondary_button_text_color_hover; ?>;

				--bb-header-background: <?php echo $header_background; ?>;
				--bb-header-alternate-background: <?php echo $header_alternate_background; ?>;
				--bb-header-links: <?php echo $header_links; ?>;
				--bb-header-links-hover: <?php echo $header_links_hover; ?>;

				--bb-header-mobile-logo-size: <?php echo $mobile_logo_size; ?>px;
				--bb-header-height: <?php echo $header_height; ?>px;

				--bb-sidenav-background: <?php echo $sidenav_background; ?>;
				--bb-sidenav-text-regular: <?php echo $sidenav_text_color_regular; ?>;
				--bb-sidenav-text-hover: <?php echo $sidenav_text_color_hover; ?>;
				--bb-sidenav-text-active: <?php echo $sidenav_text_color_active; ?>;
				--bb-sidenav-menu-background-color-regular: <?php echo $sidenav_menu_background_color_regular; ?>;
				--bb-sidenav-menu-background-color-hover: <?php echo $sidenav_menu_background_color_hover; ?>;
				--bb-sidenav-menu-background-color-active: <?php echo $sidenav_menu_background_color_active; ?>;
				--bb-sidenav-count-text-color-regular: <?php echo $sidenav_count_text_color_regular; ?>;
				--bb-sidenav-count-text-color-hover: <?php echo $sidenav_count_text_color_hover; ?>;
				--bb-sidenav-count-text-color-active: <?php echo $sidenav_count_text_color_active; ?>;
				--bb-sidenav-count-background-color-regular: <?php echo $sidenav_count_background_color_regular; ?>;
				--bb-sidenav-count-background-color-hover: <?php echo $sidenav_count_background_color_hover; ?>;
				--bb-sidenav-count-background-color-active: <?php echo $sidenav_count_background_color_active; ?>;

				--bb-footer-background: <?php echo $footer_background; ?>;
				--bb-footer-widget-background: <?php echo $footer_widget_background; ?>;
				--bb-footer-text-color: <?php echo $footer_text_color; ?>;
				--bb-footer-menu-link-color-regular: <?php echo $footer_menu_link_color_regular; ?>;
				--bb-footer-menu-link-color-hover: <?php echo $footer_menu_link_color_hover; ?>;
				--bb-footer-menu-link-color-active: <?php echo $footer_menu_link_color_active; ?>;

				--bb-admin-screen-bgr-color: <?php echo $admin_screen_bgr_color; ?>;
				--bb-admin-screen-txt-color: <?php echo $admin_screen_txt_color; ?>;
				--bb-login-register-link-color-regular: <?php echo $login_register_link_color_regular; ?>;
				--bb-login-register-link-color-hover: <?php echo $login_register_link_color_hover; ?>;
				--bb-login-register-button-background-color-regular: <?php echo $login_register_button_background_color_regular; ?>;
				--bb-login-register-button-background-color-hover: <?php echo $login_register_button_background_color_hover; ?>;
				--bb-login-register-button-border-color-regular: <?php echo $login_register_button_border_color_regular; ?>;
				--bb-login-register-button-border-color-hover: <?php echo $login_register_button_border_color_hover; ?>;
				--bb-login-register-button-text-color-regular: <?php echo $login_register_button_text_color_regular; ?>;
				--bb-login-register-button-text-color-hover: <?php echo $login_register_button_text_color_hover; ?>;

				--bb-label-background-color: <?php echo $label_background_color; ?>;
				--bb-label-text-color: <?php echo $label_text_color; ?>;

				--bb-tooltip-background: <?php echo $tooltip_background; ?>;
				--bb-tooltip-background-rgb: <?php echo join( ', ', hex_2_RGB( $tooltip_background ) ); ?>;
				--bb-tooltip-color: <?php echo $tooltip_color; ?>;

				--bb-default-notice-color: <?php echo $default_notice_color; ?>;
				--bb-default-notice-color-rgb: <?php echo join( ', ', hex_2_RGB( $default_notice_color ) ); ?>;
				--bb-success-color: <?php echo $success_color; ?>;
				--bb-success-color-rgb: <?php echo join( ', ', hex_2_RGB( $success_color ) ); ?>;
				--bb-warning-color: <?php echo $warning_color; ?>;
				--bb-warning-color-rgb: <?php echo join( ', ', hex_2_RGB( $warning_color ) ); ?>;
				--bb-danger-color: <?php echo $danger_color; ?>;
				--bb-danger-color-rgb: <?php echo join( ', ', hex_2_RGB( $danger_color ) ); ?>;

				--bb-login-custom-heading-color: <?php echo $admin_login_heading_color; ?>;

				--bb-button-radius: <?php echo $button_radius; ?>px;

				<?php
				if ( ! isset( $theme_style ) ) {
					$theme_style = '1';
				}
				?>

				<?php if ( '1' === $theme_style ) { ?>
					--bb-block-radius: 4px;
					--bb-block-radius-inner: 4px;
					--bb-input-radius: 4px;
					--bb-checkbox-radius: 2.7px;
					--bb-primary-button-focus-shadow: none;
					--bb-secondary-button-focus-shadow: none;
					--bb-outline-button-focus-shadow: none;
					--bb-input-focus-shadow: none;
					--bb-input-focus-border-color: var(--bb-content-border-color);
					--bb-label-type-radius: 100px;
				<?php } else { ?>
					--bb-block-radius: 10px;
					--bb-block-radius-inner: 6px;
					--bb-input-radius: 6px;
					--bb-checkbox-radius: 5.4px;
					--bb-primary-button-focus-shadow: 0px 0px 0px 2px rgba(var(--bb-primary-button-border-hover-rgb), 0.1);
					--bb-secondary-button-focus-shadow: 0px 0px 0px 2px rgba(var(--bb-secondary-button-border-hover-rgb), 0.1);
					--bb-outline-button-focus-shadow: 0px 0px 0px 2px rgba(var(--bb-content-border-color-rgb), 0.1);
					--bb-input-focus-shadow: 0px 0px 0px 2px rgba(var(--bb-primary-color-rgb), 0.1);
					--bb-input-focus-border-color: var(--bb-primary-color);
					--bb-label-type-radius: 6px;
				<?php } ?>

			}

			<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			.bb-style-primary-bgr-color {
				background-color: <?php echo $primary_color; ?>;
			}

			.bb-style-border-radius {
				border-radius: <?php echo $button_radius; ?>px;
			}

			<?php if ( buddyboss_theme_get_option( 'logo_size' ) ) { ?>
				#site-logo .site-title img {
					max-height: inherit;
				}

				.site-header-container .site-branding {
					min-width: <?php echo buddyboss_theme_get_option( 'logo_size' ); ?>px;
				}

				#site-logo .site-title .bb-logo img,
				#site-logo .site-title img.bb-logo,
				.buddypanel .site-title img {
					width: <?php echo buddyboss_theme_get_option( 'logo_size' ); ?>px;
				}
			<?php } ?>

			<?php
			if ( buddyboss_theme_get_option( 'logo_dark', 'id' ) && buddyboss_theme_get_option( 'logo_dark_switch' ) ) {
				?>
				.site-header-container #site-logo .bb-logo.bb-logo-dark,
				.llms-sidebar.bb-dark-theme .site-header-container #site-logo .bb-logo,
				.site-header-container .ld-focus-custom-logo .bb-logo.bb-logo-dark,
				.bb-custom-ld-focus-mode-enabled:not(.bb-custom-ld-logo-enabled) .site-header-container .ld-focus-custom-logo .bb-logo.bb-logo-dark,
				.bb-dark-theme.bb-custom-ld-focus-mode-enabled:not(.bb-custom-ld-logo-enabled) .site-header-container .ld-focus-custom-logo img,
				.bb-sfwd-aside.bb-dark-theme:not(.bb-custom-ld-logo-enabled) .site-header-container #site-logo .bb-logo,
				.buddypanel .site-branding div img.bb-logo.bb-logo-dark,
				.bb-sfwd-aside.bb-dark-theme .buddypanel .site-branding div img.bb-logo,
				.buddypanel .site-branding h1 img.bb-logo.bb-logo-dark,
				.bb-sfwd-aside.bb-dark-theme .buddypanel .site-branding h1 img.bb-logo{display:none;}

				.llms-sidebar.bb-dark-theme .site-header-container #site-logo .bb-logo.bb-logo-dark,
				.bb-dark-theme.bb-custom-ld-focus-mode-enabled:not(.bb-custom-ld-logo-enabled) .site-header-container .ld-focus-custom-logo .bb-logo.bb-logo-dark,
				.bb-sfwd-aside.bb-dark-theme .site-header-container #site-logo .bb-logo.bb-logo-dark,
				.buddypanel .site-branding div img.bb-logo,
				.bb-sfwd-aside.bb-dark-theme .buddypanel .site-branding div img.bb-logo.bb-logo-dark,
				.buddypanel .site-branding h1 img.bb-logo,
				.bb-sfwd-aside.bb-dark-theme .buddypanel .site-branding h1 img.bb-logo.bb-logo-dark{display:inline;}

			<?php } ?>

			<?php if ( buddyboss_theme_get_option( 'logo_dark', 'id' ) && buddyboss_theme_get_option( 'logo_dark_switch' ) ) { ?>
				#site-logo .site-title img {
					max-height: inherit;
				}

				.llms-sidebar.bb-dark-theme .site-header-container .site-branding,
				.bb-sfwd-aside.bb-dark-theme .site-header-container .site-branding {
					min-width: <?php echo buddyboss_theme_get_option( 'logo_dark_size' ); ?>px;
				}

				.llms-sidebar.bb-dark-theme #site-logo .site-title .bb-logo.bb-logo-dark img,
				.bb-sfwd-aside.bb-dark-theme #site-logo .site-title .bb-logo.bb-logo-dark img,
				.llms-sidebar.bb-dark-theme #site-logo .site-title img.bb-logo.bb-logo-dark,
				.bb-sfwd-aside.bb-dark-theme #site-logo .site-title img.bb-logo.bb-logo-dark,
				.bb-custom-ld-focus-mode-enabled .site-header-container .ld-focus-custom-logo .bb-logo.bb-logo-dark,
				.bb-sfwd-aside.bb-dark-theme .buddypanel .site-branding div img.bb-logo.bb-logo-dark {
					width: <?php echo buddyboss_theme_get_option( 'logo_dark_size' ); ?>px;
				}
			<?php } ?>

			<?php if ( buddyboss_theme_get_option( 'mobile_logo_dark', 'id' ) && buddyboss_theme_get_option( 'mobile_logo_dark_switch' ) ) { ?>
				.llms-sidebar.bb-dark-theme .site-title img.bb-mobile-logo.bb-mobile-logo-dark,
				.bb-sfwd-aside.bb-dark-theme:not(.bb-custom-ld-logo-enabled) .site-title img.bb-mobile-logo.bb-mobile-logo-dark {
					display: inline;
				}
				.site-title img.bb-mobile-logo.bb-mobile-logo-dark,
				.llms-sidebar.bb-dark-theme .site-title img.bb-mobile-logo,
				.bb-sfwd-aside.bb-dark-theme:not(.bb-custom-ld-logo-enabled) .site-title img.bb-mobile-logo {
					display: none;
				}
			<?php } ?>

			<?php if ( buddyboss_theme_get_option( 'mobile_logo_dark_size' ) && buddyboss_theme_get_option( 'mobile_logo_dark_switch' ) ) { ?>
				.llms-sidebar.bb-dark-theme .site-title img.bb-mobile-logo.bb-mobile-logo-dark,
				.bb-sfwd-aside.bb-dark-theme .site-title img.bb-mobile-logo.bb-mobile-logo-dark {
					width: <?php echo buddyboss_theme_get_option( 'mobile_logo_dark_size' ); ?>px;
				}
			<?php } ?>

			<?php if ( buddyboss_theme_get_option( 'mobile_logo_size' ) ) { ?>
				.site-title img.bb-mobile-logo {
					width: <?php echo buddyboss_theme_get_option( 'mobile_logo_size' ); ?>px;
				}
				<?php
			}
			if ( buddyboss_theme_get_option( 'footer_logo_size' ) ) {
				?>
				.footer-logo img {
					max-width: <?php echo buddyboss_theme_get_option( 'footer_logo_size' ); ?>px;
				}
			<?php } ?>

			.site-header-container #site-logo .bb-logo img,
			.site-header-container #site-logo .site-title img.bb-logo,
			.site-title img.bb-mobile-logo {
				<?php
				if ( $header_height ) {
					echo 'max-height:' . $header_height . 'px';
				} else {
					echo 'max-height: 76px;';
				}
				?>
			}

			<?php if ( empty( $header_shadow ) ) { ?>
				.site-header,
				.sticky-header .site-header:not(.has-scrolled) {
					-webkit-box-shadow: none;
					-moz-box-shadow: none;
					box-shadow: none;
				}
			<?php } ?>

			<?php
			if (
				(
					in_array( 'single-sfwd-lessons', $header_lesson_topic, true ) ||
					in_array( 'single-sfwd-topic', $header_lesson_topic, true )
				) && empty( $header_sticky )
			) {
				?>
				@media screen and (min-width: 800px) {
					.bb-buddypanel.buddypanel-open.single-sfwd-lessons .site-header,
					.bb-buddypanel.buddypanel-open.single-sfwd-topic .site-header {
						width: -webkit-calc(100% - 220px);
						width: calc(100% - 220px);
						-webkit-transition: all .2s;
						transition: all .2s;
					}
				}

				.single-sfwd-lessons .site-header,
				.single-sfwd-topic .site-header {
					position: fixed;
					z-index: 610;
					width: 100%;
					-webkit-transition: all .2s;
					transition: all .2s;
				}

				.single-sfwd-lessons .site-header.has-scrolled,
				.single-sfwd-topic .site-header.has-scrolled {
					box-shadow: 0 1px 0 0 rgba(0, 0, 0, 0.05), 0 5px 10px 0 rgba(0, 0, 0, 0.15);
					-webkit-transition: all .2s;
					transition: all .2s;
				}

				.single-sfwd-lessons .site-content,
				.single-sfwd-topic .site-content {
					<?php
					if ( $header_height ) {
						echo 'padding-top:' . $header_height . 'px !important';
					} else {
						echo 'padding-top: 76px !important;';
					}
					?>
				}
			<?php } ?>

			<?php if ( ! empty( $header_sticky ) ) { ?>
				.sticky-header .site-header {
					position: fixed;
					z-index: 610;
					width: 100%;
				}

				.sticky-header .bp-search-ac-header {
					position: fixed;
				}

				.sticky-header .site-content,
				body.buddypress.sticky-header .site-content,
				.bb-buddypanel.sticky-header .site-content,
				.single-sfwd-quiz.bb-buddypanel.sticky-header .site-content,
				.single-sfwd-lessons.bb-buddypanel.sticky-header .site-content,
				.single-sfwd-topic.bb-buddypanel.sticky-header .site-content {
					<?php
					if ( $header_height ) {
						echo 'padding-top:' . $header_height . 'px';
					} else {
						echo 'padding-top: 76px;';
					}
					?>
				}

				.sticky-header .site-content {
					min-height: 85vh;
				}
			<?php } ?>

			.site-header .site-header-container,
			.header-search-wrap,
			.header-search-wrap input.search-field,
			.header-search-wrap form.search-form {
				height: <?php echo $header_height; ?>px;
			}

			.sticky-header .bp-feedback.bp-sitewide-notice {
				top: <?php echo $header_height; ?>px;
			}

			@media screen and (max-width: 767px) {
				.bb-mobile-header {
					height: <?php echo $header_height; ?>px;
				}

				#learndash-content .lms-topic-sidebar-wrapper {
					width: 100%;
				}

				#learndash-content .lms-topic-sidebar-wrapper .lms-topic-sidebar-data {
					width: 100%;
					height: calc(90vh - <?php echo $header_height; ?>px);
					max-width: 350px;
				}
			}

			/* Primary color */
			.bb-document-theater .bb-media-section.bb-media-no-preview .img-section a.download-button:hover,
			.bb-document-theater .bb-media-section .img-section img > .download-button:hover,
			.bb-forums .bb-forums__list .bb-forums__item .item-meta .bs-replied > a:hover,
			.bb-forums .bb-forums__list .bb-forums__item .item-title > a:hover,
			#buddypress .users-header .gamipress-buddypress-achievements .gamipress-buddypress-achievement-title:hover,
			#buddypress .users-header .gamipress-buddypress-ranks .gamipress-buddypress-rank-title:hover {
				color: <?php echo $primary_color; ?>;
			}

			#send-private-message.generic-button a:before,
			.toggle-password,
			.toggle-password:hover,
			#message-threads .bp-message-link:hover .thread-to,
			span.triangle-play-icon,
			.bb-document-theater .bb-media-section.bb-media-no-preview .img-section a.download-button,
			.bb-document-theater .bb-media-section .img-section img > .download-button {
				color: <?php echo $primary_color; ?>;
			}

			.bb-shared-screen svg g {
				stroke: <?php echo $primary_color; ?>;
			}

			.group-messages.private-message #group-messages-container .group-messages-members-listing .all-members .group-message-member-li.selected .invite-button .icons:before {
				background: <?php echo $primary_color; ?>;
			}

			span.triangle-play-icon,
			.video-thumbnail-content .bp-video-thumbnail-auto-generated .video-thumb-list li .bb-custom-check:checked ~ a,
			.video-thumbnail-content .bb-dropzone-wrap .bb-custom-check:checked ~ .bb-field-wrap .dropzone .dz-preview .dz-image,
			.video-thumbnail-content .bb-dropzone-wrap .bb-custom-check[name="bb-video-thumbnail-select"]:checked ~ .video-thumbnail-custom {
				border-color: <?php echo $primary_color; ?>;
			}

			.toggle-sap-widgets:hover .cls-1 {
				fill: <?php echo $primary_color; ?>;
			}

			.bb-cover-photo,
			.bb-cover-photo .progress {
				background: <?php echo buddyboss_theme_get_option( 'buddyboss_theme_group_cover_bg' ); ?>;
			}

			.header-button.underlined {
				box-shadow: 0 -1px 0 <?php echo $primary_color; ?> inset;
			}

			/* Tooltips */
			[data-balloon]:after,
			[data-bp-tooltip]:after {
				background-color: <?php echo color2rgba( $tooltip_background, 0.95 ); ?>;
			}

			[data-balloon]:before,
			[data-bp-tooltip]:before {
				background:no-repeat url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http://www.w3.org/2000/svg%22%20width%3D%2236px%22%20height%3D%2212px%22%3E%3Cpath%20fill%3D%22<?php echo color2rgba( $tooltip_background, 0.95 ); ?>%22%20transform%3D%22rotate(0)%22%20d%3D%22M2.658,0.000%20C-13.615,0.000%2050.938,0.000%2034.662,0.000%20C28.662,0.000%2023.035,12.002%2018.660,12.002%20C14.285,12.002%208.594,0.000%202.658,0.000%20Z%22/%3E%3C/svg%3E");
				background-size: 100% auto;
			}

			[data-balloon][data-balloon-pos='right']:before {
				background:no-repeat url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http://www.w3.org/2000/svg%22%20width%3D%2212px%22%20height%3D%2236px%22%3E%3Cpath%20fill%3D%22<?php echo color2rgba( $tooltip_background, 0.95 ); ?>%22%20transform%3D%22rotate(90 6 6)%22%20d%3D%22M2.658,0.000%20C-13.615,0.000%2050.938,0.000%2034.662,0.000%20C28.662,0.000%2023.035,12.002%2018.660,12.002%20C14.285,12.002%208.594,0.000%202.658,0.000%20Z%22/%3E%3C/svg%3E");
				background-size: 100% auto;
			}

			[data-balloon][data-balloon-pos='left']:before {
				background:no-repeat url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http://www.w3.org/2000/svg%22%20width%3D%2212px%22%20height%3D%2236px%22%3E%3Cpath%20fill%3D%22<?php echo color2rgba( $tooltip_background, 0.95 ); ?>%22%20transform%3D%22rotate(-90 18 18)%22%20d%3D%22M2.658,0.000%20C-13.615,0.000%2050.938,0.000%2034.662,0.000%20C28.662,0.000%2023.035,12.002%2018.660,12.002%20C14.285,12.002%208.594,0.000%202.658,0.000%20Z%22/%3E%3C/svg%3E");
				background-size: 100% auto;
			}

			[data-balloon][data-balloon-pos='down']:before {
				background:no-repeat url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http://www.w3.org/2000/svg%22%20width%3D%2236px%22%20height%3D%2212px%22%3E%3Cpath%20fill%3D%22<?php echo color2rgba( $tooltip_background, 0.95 ); ?>%22%20transform%3D%22rotate(180 18 6)%22%20d%3D%22M2.658,0.000%20C-13.615,0.000%2050.938,0.000%2034.662,0.000%20C28.662,0.000%2023.035,12.002%2018.660,12.002%20C14.285,12.002%208.594,0.000%202.658,0.000%20Z%22/%3E%3C/svg%3E");
				background-size: 100% auto;
			}

			/* Header colors */
			.bb-header-buttons a.button.outline,
			.site-header .hideshow .more-button > i {
				color: <?php echo buddyboss_theme_get_option( 'header_links' ); ?>;
			}

			.bb-header-buttons a.button.outline:hover {
				color: <?php echo buddyboss_theme_get_option( 'header_links_hover' ); ?>;
			}

			.bp-messages-content .actions .message_actions .message_action__list li a:hover {
				color: <?php echo buddyboss_theme_get_option( 'header_links_hover' ); ?>;
			}

			.bb-follow-links a,
			.elementor-widget-wp-widget-bbp_views_widget ul li a,
			.elementor-widget-wp-widget-recent-posts ul li a,
			.elementor-element.widget.bp-latest-activities ul li a,
			.elementor-widget-wp-widget-bbp_replies_widget ul li a,
			.elementor-widget-wp-widget-bbp_forums_widget ul li a,
			.elementor-widget-wp-widget-bbp_topics_widget ul li a {
				color: <?php echo buddyboss_theme_get_option( 'alternate_text_color' ); ?>;
			}

			.bb-follow-links a:hover,
			.elementor-widget-wp-widget-bbp_views_widget ul li a:hover,
			.elementor-widget-wp-widget-recent-posts ul li a:hover,
			.elementor-element.widget.bp-latest-activities ul li a:hover,
			.elementor-widget-wp-widget-bbp_replies_widget ul li a:hover,
			.elementor-widget-wp-widget-bbp_forums_widget ul li a:hover,
			.elementor-widget-wp-widget-bbp_topics_widget ul li a:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			/* Headings link color */
			.comment-respond .vcard a,
			.widget.buddypress .bp-login-widget-user-links > div.bp-login-widget-user-link a,
			#whats-new-form .username,
			.bb-recent-posts h4 a.bb-title,
			.bb-activity-media-wrap .bb-activity-media-elem.document-activity .document-description-wrap .document-detail-wrap,
			.activity-list li.bbp_topic_create .bp-activity-head .bb-post-singular,
			.activity-list li.bbp_reply_create .bp-activity-head .bb-post-singular,
			.activity-list li.blogs .bp-activity-head .bb-post-singular,
			.activity-list li.bbp_topic_create .activity-content .activity-inner .bb-post-title,
			.activity-list li.bbp_reply_create .activity-content .activity-inner .bb-post-title,
			.activity-list li.bbp_reply_create .activity-content .activity-inner .activity-discussion-title-wrap {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.comment-respond .vcard a:hover,
			.widget.buddypress .bp-login-widget-user-links > div.bp-login-widget-user-link a:hover,
			#whats-new-form .username:hover,
			.bb-recent-posts h4 a.bb-title:hover,
			.bb-activity-media-wrap .bb-activity-media-elem.document-activity.code-full-view .document-action-wrap .document-action_collapse i,
			.search-document-list .media-folder_items .media-folder_details__bottom .media-folder_author a:hover,
			#media-folder-document-data-table .media-folder_items .media-folder_details__bottom .media-folder_author a:hover,
			.search-document-list .media-folder_items .media-folder_group a:hover,
			.search-media-list .media-album_modified .media-album_details__bottom .media-album_author a:hover,
			.search-media-list .media-album_group_name a:hover,
			.search-document-list .media-folder_items .media-folder_details .media-folder_name:hover,
			#media-folder-document-data-table .media-folder_items .media-folder_details .media-folder_name:hover,
			#media-stream.document-parent.group-column #media-folder-document-data-table .media-folder_items .media-folder_group a:hover,
			.search-media-list .media-album_details .media-album_name:hover,
			.bb-document-theater .bb-media-section.bb-media-no-preview .img-section a.download-button,
			.search-results .bp-list li .item-meta a:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			/* Body Text color */
			#whats-new-textarea-placeholder [contenteditable="true"]:empty:before,
			#whats-new-textarea [contenteditable="true"]:empty:before {
				color: <?php echo color2rgba( buddyboss_theme_get_option( 'body_text_color' ), 0.5 ); ?>;
			}

			.ac-reply-toolbar a .bb-icon,
			#whats-new-toolbar a .bb-icon,
			#whats-new-messages-toolbar a .bb-icon,
			.ac-reply-toolbar .emojionearea .emojionearea-button,
			#whats-new-toolbar .emojionearea .emojionearea-button,
			#whats-new-messages-toolbar .emojionearea .emojionearea-button,
			#editor-toolbar .bb-icon,
			#editor-toolbar .emojionearea-button:before,
			.medium-editor-toolbar .medium-editor-toolbar-actions button,
			.buddypress .medium-editor-toolbar .medium-editor-toolbar-actions button,
			#buddypress .medium-editor-toolbar .medium-editor-toolbar-actions button,
			.medium-editor-toolbar-form a,
			.bp-nouveau-activity-form-placeholder- .bb-model-header a .bb-icon-close,
			.activity-update-form .bb-model-header a .bb-icon-close,
			#buddypress input#privacy-status-back,
			#buddypress input#privacy-status-group-back {
				color: <?php echo color2rgba( buddyboss_theme_get_option( 'heading_text_color' ), 0.5 ); ?>;
			}

			.ac-reply-toolbar a:hover .bb-icon,
			#whats-new-toolbar a:hover .bb-icon,
			#whats-new-messages-toolbar a:hover .bb-icon,
			.ac-reply-toolbar a.active .bb-icon,
			#whats-new-toolbar a.active .bb-icon,
			#whats-new-messages-toolbar a.active .bb-icon,
			.ac-reply-toolbar .active a .bb-icon,
			#whats-new-toolbar .active a .bb-icon,
			#whats-new-messages-toolbar .active a .bb-icon,
			.ac-reply-toolbar a.open .bb-icon,
			#whats-new-toolbar a.open .bb-icon,
			#whats-new-messages-toolbar a.open .bb-icon,
			.ac-reply-toolbar .emojionearea .emojionearea-button.active,
			#whats-new-toolbar .emojionearea .emojionearea-button.active,
			#whats-new-messages-toolbar .emojionearea .emojionearea-button.active,
			#editor-toolbar .emojionearea-button.active:before,
			#editor-toolbar .active .bb-icon:before,
			.medium-editor-toolbar .medium-editor-toolbar-actions button.medium-editor-button-active,
			.buddypress .medium-editor-toolbar .medium-editor-toolbar-actions button.medium-editor-button-active,
			#buddypress .medium-editor-toolbar .medium-editor-toolbar-actions button.medium-editor-button-active,
			.medium-editor-toolbar-form a:hover,
			.bp-nouveau-activity-form-placeholder- .bb-model-header a:hover .bb-icon-close,
			.activity-update-form .bb-model-header a:hover .bb-icon-close,
			#buddypress input#privacy-status-back:hover,
			#buddypress input#privacy-status-group-back:hover {
				color: <?php echo color2rgba( buddyboss_theme_get_option( 'heading_text_color' ), 1 ); ?>;
			}

			/* Heading Text color */
			.show-support h6,
			h4 .bp-reported-type,
			.activity-link-preview-title {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.site-title, .site-title a {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			/* Layout colors */

			<?php $body_bgr_color = buddyboss_theme_get_option( 'body_background' ); ?>

			body #main-wrap,
			.formatted-content {
				background-color: <?php echo $body_bgr_color; ?>;
			}

			.bb_processing_overlay {
				background-color: <?php echo color2rgba( $body_bgr_color, 0.8 ); ?>;
			}

			.bs-item-list.list-view div.bs-item-wrap:not(.no-hover-effect):hover {
				border-left-color: <?php echo buddyboss_theme_get_option( 'body_blocks' ); ?>;
				border-right-color: <?php echo buddyboss_theme_get_option( 'body_blocks' ); ?>;
				border-bottom-color: <?php echo buddyboss_theme_get_option( 'body_blocks' ); ?>;
			}

			@media screen and (max-width: 1024px) and (min-width: 768px) {
				.side-panel {
					background-color: <?php echo $body_bgr_color; ?>;
				}
			}

			.os-loader,
			.medium-editor-insert-plugin .medium-insert-buttons .medium-insert-buttons-addons li,
			.sap-publish-popup,
			.posts-stream,
			.posts-stream .inner,
			.sl-count:after,
			.sl-count:before,
			.sl-icon:after,
			.sl-icon:before,
			.main-navigation li ul ul,
			.main-navigation li ul,
			.header-account-login .pop .bp_components .menupop:not(#wp-admin-bar-my-account) > .ab-sub-wrapper,
			.header-account-login .pop .links li > .sub-menu,
			.header-account-login .pop .bp_components .menupop:not(#wp-admin-bar-my-account) > .ab-sub-wrapper:before,
			.header-account-login .pop .links li > .sub-menu:before,
			.header-notifications .pop,
			.header-account-login .pop,
			#whats-new-header:after,
			a.to-top,
			.bbp-forum-data:before {
				background-color: <?php echo $body_bgr_color; ?>;
			}

			/* Buttons */
			.ld-course-list-items .ld_course_grid .bb-cover-list-item p.ld_course_grid_button a {
				border-radius: <?php echo $button_radius; ?>px;
			}

			.no-results.not-found .search-form .search-submit {
				border-radius: 0 <?php echo $button_radius; ?>px <?php echo $button_radius; ?>px 0;
			}

		<?php

		$css = ob_get_contents();
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		ob_end_clean();

		echo $css;

		if ( ! is_array( $custom_css ) ) {
			$custom_css = array();
		}
		$custom_css['css'] = $css;

		?>

		</style>
		<?php

		// save processed css.
		set_transient( 'buddyboss_theme_compressed_custom_css', $custom_css );

	}

	/* Add Action */
	add_action( 'wp_head', 'boss_generate_option_css', 99 );
}

if ( ! function_exists( 'boss_generate_option_bp_css' ) ) {

	function boss_generate_option_bp_css() {

		if ( is_customize_preview() ) {
			$custom_css = '';
		} else {
			$custom_css = get_transient( 'buddyboss_theme_compressed_bp_custom_css' );
		}

		if ( ! empty( $custom_css ) && isset( $custom_css['css'] ) ) {

			echo "
            <style id=\"buddyboss_theme-bp-style\">
                {$custom_css["css"]}
            </style>
            ";

			return false;

		}

		$primary_color                 = buddyboss_theme_get_option( 'accent_color' );
		$admin_login_background_switch = buddyboss_theme_get_option( 'admin_login_background_switch' );
		$admin_login_background_media  = buddyboss_theme_get_option( 'admin_login_background_media' );
		$admin_login_overlay_opacity   = buddyboss_theme_get_option( 'admin_login_overlay_opacity' );
		$admin_logoimg                 = buddyboss_theme_get_option( 'admin_logo_media' );
		$admin_logowidth               = buddyboss_theme_get_option( 'admin_logo_width' );

		?>
		<style id="buddyboss_theme-bp-style">

		<?php ob_start(); ?>

			/* Primary color */
			#buddypress #members-list .members-meta.action > .generic-button:last-child a,
			#buddypress #members-list .members-meta.action > .generic-button:last-child button,
			body #buddypress .bp-list .action .generic-button a,
			#buddypress .activity-list .action.activity-meta .button:hover,
			#buddypress .activity-list .action.activity-meta .button:hover span,
			#groups-list.bp-list.grid.bb-cover-enabled .item-avatar .generic-button .group-button,
			#buddypress .only-grid-view .follow-button .follow-button,
			.mepr-price-menu.custom .mepr-price-box-benefits-item:before,
			#message-threads li.unread .thread-to:before,
			.messages-wrapper #compose-personal-li a,
			.search-results .pagination-links a,
			#buddypress .activity-lists .action.bp-generic-meta .button,
			#bbpress-forums .activity-list .action.bp-generic-meta .button,
			.recording-preview-img:hover span.triangle-play-icon,
			.activity-list .activity-item .bp-generic-meta.action .open-close-activity:before,
			.activity-list .activity-item .bp-generic-meta.action .buddyboss_edit_activity:before,
			.activity-list .activity-item .bp-generic-meta.action .buddyboss_edit_activity_cancel:before,
			#media-folder-document-data-table .media-folder_items .media-folder_actions .media-folder_action__list ul li a:hover,
			#bp-media-single-folder .bp-media-header-wrap .media-folder_items .media-folder_action__list ul li a:hover,
			.bb-activity-media-wrap .bb-activity-media-elem.media-activity .media-action-wrap .media-action_list ul li a:hover,
			#buddypress .standard-form button.outline,
			#buddypress .standard-form button.outline:hover,
			.video-js .vjs-play-progress:before,
			#media-stream.media .bb-video-thumb .item-action-wrap .item-action_list ul li a:hover,
			.bb-activity-video-wrap .bb-activity-video-elem .item-action-wrap .item-action_list ul li a:hover,
			.bb-activity-media-wrap .bb-activity-video-elem .video-action-wrap .media-action_list ul li a:hover,
			.bb-activity-media-wrap .bb-activity-video-elem .video-action-wrap .video-action_list ul li a:hover,
			.bb-media-model-container .activity-list .video-action-wrap.item-action-wrap .video-action_list ul li a:hover {
				color: <?php echo $primary_color; ?>;
			}

			.topic .forum_single_action_wrap .forum_single_action_options .bb-topic-report-link-wrap a:hover,
			#buddypress .forum_single_action_wrap .forum_single_action_options .bb-topic-report-link-wrap a:hover {
				color: <?php echo $primary_color; ?> !important;
			}

			#buddypress .activity-lists .action.bp-generic-meta .button:hover,
			#bbpress-forums .activity-list .action.bp-generic-meta .button:hover,
			#buddypress .activity-lists .action.bp-generic-meta .button:hover span,
			#bbpress-forums .activity-list .action.bp-generic-meta .button:hover span,
			.activity-list .activity-item .bp-generic-meta.action .unfav:hover:before,
			#buddypress .only-grid-view .follow-button .follow-button:hover,
			#groups-list.bp-list.grid.bb-cover-enabled .item-avatar .generic-button .group-button:hover,
			#certificate_list .bb-certificate-download a:hover,
			.bp-search-results-wrap .view-all-link:hover,
			.bp-search-results-wrap .view-all-link:hover:after,
			.activity-list .activity-item .bp-generic-meta.action .open-close-activity:hover:before,
			.activity-list .activity-item .bp-generic-meta.action .buddyboss_edit_activity:hover:before,
			.activity-list .activity-item .bp-generic-meta.action .buddyboss_edit_activity_cancel:hover:before,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-arrow-circle .comment-count:hover,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-arrow-circle .text:hover,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-discussion .comment-count:hover,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-discussion .text:hover,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-chat .comment-count:hover,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-chat .text:hover,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-discussion .text:hover:before,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-chat .comment-count:hover:before,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-chat .text:hover:before {
				color: <?php echo $primary_color; ?>;
			}

			#buddypress #members-list .members-meta.action > .generic-button:last-child a,
			#buddypress #members-list .members-meta.action > .generic-button:last-child button,
			body #buddypress .bp-list .action .generic-button a,
			input:checked+.bb-time-meridian,
			.group-invites .bp-dir-hori-nav:not(.bp-vertical-navs) #item-body #group-invites-container .subnav li.selected,
			#buddypress .standard-form button.outline,
			#buddypress .standard-form button.outline:hover {
				border-color: <?php echo $primary_color; ?>;
			}

			.video-js .vjs-play-progress {
				background: <?php echo $primary_color; ?>;
			}

			.mepr-price-menu.custom .mepr-price-box-button a,
			body #buddypress a.export-csv,
			input:checked+.bb-time-meridian,
			input:checked+.bb-toggle-slider,
			.bb-groups-messages-left-inner input:checked + .bp-group-message-slider,
			.widget_bp_core_login_widget.buddypress #bp-login-widget-form #bp-login-widget-submit.bp-login-btn-active,
			#item-body #group-invites-container .bp-invites-content #send-invites-editor #bp-send-invites-form .action button#bp-invites-send,
			#message-threads li.unread .thread-date time:after,
			.buddypress-wrap .bp-subnavs ul #bp-zoom-switch-type .bb-toggle-switch .bb-toggle-slider,
			#bbpress-forums div.bbp-reply-content a.bp-video-thumbnail-submit {
				background-color: <?php echo $primary_color; ?>;
			}

			#bbpress-forums .bbp-reply-form.bb-modal a#bbp-close-btn:hover,
			#bbpress-forums .bbp-topic-form.bb-modal a#bbp-close-btn:hover,
			.bbp-reply-form-success-modal .bbp-reply-form-success .reply-content .content-title .close .js-modal-close:hover,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-arrow-circle .comment-count:before,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-discussion .text:before,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-chat .comment-count:before,
			.activity-list .activity-item .activity-inner .activity-inner-meta .button.bb-icon-chat .text:before,
			.activity-list li.blogs .activity-content .activity-inner .activity-discussion-title-wrap a:hover{
				color: <?php echo $primary_color; ?>;
			}

			#buddypress .bp-navs.bb-bp-tab-nav a,
			body #buddypress .bp-list.members-list .action .generic-button button,
			body #buddypress .bp-list.members-list .action .generic-button a,
			#item-body #group-invites-container .bp-navs.group-subnav a,
			#buddypress .profile.edit .button-nav a,
			#message-threads li .thread-content .thread-subject a,
			#message-threads li.unread .thread-subject .subject,
			.avatar-crop-management #avatar-crop-actions a.avatar-crop-cancel,
			#media-folder-document-data-table .media-folder_items .media-folder_actions .media-folder_action__anchor {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.bb-groups-messages-left-inner .input:focus + .bp-group-message-slider {
				box-shadow: 0 0 1px <?php echo $primary_color; ?>;
			}

			#buddypress .bp-navs.bb-bp-tab-nav a:hover,
			.elementor-widget-wrap div.item-options a:hover,
			body #buddypress .bp-list.members-list .action .generic-button button:hover,
			body #buddypress .bp-list.members-list .action .generic-button a:hover,
			#item-body #group-invites-container .bp-navs.group-subnav a:hover,
			#buddypress .profile.edit .button-nav a:hover,
			#buddypress .bp-search-results-wrapper .bp-navs a:hover,
			#message-threads li .thread-content .thread-subject a:hover,
			#message-threads li.unread .thread-subject .subject:hover,
			.avatar-crop-management #avatar-crop-actions a.avatar-crop-cancel:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			.bb-single-album-header .document-breadcrumb li a:hover{
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			#buddypress .bp-navs.bb-bp-tab-nav .current a,
			#buddypress .bp-navs.bb-bp-tab-nav .selected a,
			#buddypress .bp-navs.bb-bp-tab-nav .current a:focus,
			#buddypress .bp-navs.bb-bp-tab-nav .selected a:focus,
			#buddypress .bp-navs.bb-bp-tab-nav .current a:hover,
			#buddypress .bp-navs.bb-bp-tab-nav .selected a:hover,
			#buddypress .bp-search-results-wrapper .bp-navs .current a,
			#buddypress .bp-search-results-wrapper .bp-navs .selected a,
			#item-body #group-invites-container .bp-navs.group-subnav .current a,
			#item-body #group-invites-container .bp-navs.group-subnav .selected a,
			#buddypress .profile.edit .button-nav .current a,
			.groups.group-create .buddypress-wrap .group-create-buttons li.current a,
			.buddypress-wrap .bp-subnavs ul #bp-zoom-switch-type > a.zoom_active,
			#group-messages-container .bp-navs.group-subnav li.selected a {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.groups.group-create .buddypress-wrap .group-create-buttons li:not(:last-child) a:after {
				background-color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			body.invites.has-sidebar #buddypress .bp-settings-container .bp-navs .selected a,
			#buddypress .profile.edit .button-nav .current a,
			#group-messages-container .bp-navs.group-subnav li.selected {
				border-bottom-color: <?php echo $primary_color; ?>;
			}

			/* Headings link color */
			#buddypress .register-section .visibility-toggle-link,
			#buddypress .profile.edit .visibility-toggle-link,
			.thread-to a,
			.bp-messages-content #bp-message-thread-list .message-metadata .user-link,
			.widget.activity_update .activity-update p a:not(.activity-time-since),
			.bp-activity-huddle a.activity-post-user-name,
			.pc_detailed_progress_wrap .single_section_wrap.completed .section_name a {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			#buddypress .register-section .visibility-toggle-link:hover,
			#buddypress .profile.edit .visibility-toggle-link:hover,
			.thread-to a:hover,
			.bp-messages-content #bp-message-thread-list .message-metadata .user-link:hover,
			.widget.activity_update .activity-update p a:not(.activity-time-since):hover,
			.bp-activity-huddle a.activity-post-user-name:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			<?php
			if ( function_exists( 'buddypress' ) && defined( 'BP_PLATFORM_VERSION' ) && version_compare( BP_PLATFORM_VERSION, '1.8.5', '>' ) ) {
				?>
				#buddypress #header-cover-image.has-default,
				#buddypress #header-cover-image.has-default .guillotine-window img,
				.bs-group-cover.has-default a {
					background-color: <?php echo buddyboss_theme_get_option( 'buddyboss_theme_group_cover_bg' ); ?>;
				}

			<?php } else { ?>

				#buddypress #header-cover-image,
				#buddypress #header-cover-image .guillotine-window img,
				.bs-group-cover a {
					background-color: <?php echo buddyboss_theme_get_option( 'buddyboss_theme_group_cover_bg' ); ?>;
				}
				<?php
			}
			?>

			/* Heading Text color */
			body #buddypress div#item-header-cover-image h2,
			.groups.group-create .buddypress-wrap .bp-invites-content #members-list .list-title,
			#item-body #group-invites-container .bp-invites-content .list-title,
			.bp-messages-content .thread-participants,
			#buddypress .profile.edit > #profile-edit-form fieldset .editfield legend {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.item-list.groups-list .list-wrap,
			.item-list.bp-search-results-list .list-wrap,
			#item-body > div.profile,
			.activity-update-form #whats-new-textarea textarea,
			.bb-bp-settings-container {
				background: <?php echo buddyboss_theme_get_option( 'body_blocks' ); ?>;
			}

			#item-body #group-invites-container .bp-invites-content .item-list > li,
			.bp-messages-content #bp-message-thread-list li,
			.groups.group-create .buddypress-wrap .bp-invites-content #members-list li,
			#friend-list.item-list .list-wrap,
			.zoom-meeting-block,
			.media.document-parent,
			#group-settings-form #request-list li,
			.bp-messages-content #bp-message-content .medium-editor-toolbar {
				background-color: <?php echo buddyboss_theme_get_option( 'body_blocks' ); ?>;
			}

			@media screen and (min-width: 768px) {
				.groups.group-create .buddypress-wrap #group-create-tabs.tabbed-links .group-create-buttons li.current {
					background-color: <?php echo buddyboss_theme_get_option( 'body_blocks' ); ?>;
				}
			}

			.buddypress-wrap .bp-tables-report {
				box-shadow: 0 0 0 1px <?php echo buddyboss_theme_get_option( 'body_blocks_border' ); ?>;
			}

			<?php
			if ( $admin_login_background_switch ) {
				if ( $admin_login_background_media['url'] ) {
					?>
					.login-split {
						background-image: url(<?php echo $admin_login_background_media['url']; ?>);
						background-size: cover;
						background-position: 50% 50%;
					}
					<?php
				}
			}
			if ( $admin_login_overlay_opacity ) {
				?>
				body.buddypress.register.login-split-page .login-split .split-overlay,
				body.buddypress.activation.login-split-page .login-split .split-overlay {
					opacity: <?php echo $admin_login_overlay_opacity / 100; ?>;
				}
				<?php
			}

			if ( ! empty( $admin_logoimg['url'] ) ) {
				?>
				body.buddypress.register .register-section-logo img,
				body.buddypress.activation .activate-section-logo img {
					width: <?php echo $admin_logowidth; ?>px;
				}
				<?php
			}
			?>

			<?php $body_bgr_color = buddyboss_theme_get_option( 'body_background' ); ?>

			.buddypress-wrap nav#object-nav.horizontal.group-nav-tabs ul li.selected,
			.buddypress-wrap nav#object-nav.horizontal.user-nav-tabs ul li.selected {
				background-color: <?php echo $body_bgr_color; ?>;
			}

			.buddypress-wrap nav#object-nav.horizontal.group-nav-tabs ul li.selected a,
			.buddypress-wrap nav#object-nav.horizontal.user-nav-tabs ul li.selected a,
			body:not(.group-admin):not(.group-invites) .buddypress-wrap .group-subnav.tabbed-links ul.subnav li.selected,
			body:not(.group-admin):not(.group-invites) .buddypress-wrap .user-subnav.tabbed-links ul.subnav li.selected,
			.buddypress-wrap .user-subnav.tabbed-links ul.subnav li.selected {
				border-bottom-color: <?php echo $body_bgr_color; ?>;
			}

		<?php

		$css = ob_get_contents();
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		ob_end_clean();

		echo $css;

		if ( ! is_array( $custom_css ) ) {
			$custom_css = array();
		}
		$custom_css['css'] = $css;

		?>

		</style>
		<?php

		// save processed css.
		set_transient( 'buddyboss_theme_compressed_bp_custom_css', $custom_css );

	}

	/* Add Action */
	if ( function_exists( 'bp_is_active' ) ) {
		add_action( 'wp_head', 'boss_generate_option_bp_css', 99 );
	}
}

if ( ! function_exists( 'boss_generate_option_forums_css' ) ) {
	function boss_generate_option_forums_css() {

		if ( is_customize_preview() ) {
			$custom_css = '';
		} else {
			$custom_css = get_transient( 'buddyboss_theme_compressed_forums_custom_css' );
		}

		if ( ! empty( $custom_css ) && isset( $custom_css['css'] ) ) {

			echo "
            <style id=\"buddyboss_theme-forums-style\">
                {$custom_css["css"]}
            </style>
            ";

			return false;

		}

		$primary_color = buddyboss_theme_get_option( 'accent_color' );

		?>
		<style id="buddyboss_theme-forums-style">

		<?php ob_start(); ?>

			.bbpress .scrubber .handle:after,
			.scrubber .handle:after {
				background-color: <?php echo $primary_color; ?>;
			}

			.bbp_widget_login a.button.logout-link {
				color: <?php echo $primary_color; ?>;
			}

			.bbp_widget_login a.button.logout-link:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			.bs-forums-banner.has-banner-img h1,
			.bs-forums-banner.has-banner-img p {
				color: <?php echo buddyboss_theme_get_option( 'bbpress_banner_text' ); ?>;
			}

			.bbpress .widget_display_forums li a,
			.bbpress-sidebar .widget_tag_cloud .tagcloud a,
			.bb-modal.bbp-topic-form .bbp-submit-wrapper a#bbp-close-btn,
			.bbp-mfp-zoom-in fieldset.bbp-form .bbp-submit-wrapper a#bbp-close-btn,
			/*#bbpress-forums .bbp-reply-form.bb-modal a#bbp-close-btn,*/
			#bbpress-forums .bbp-reply-form.bb-modal a#bbp-cancel-reply-to-link {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.bbpress .widget_display_forums li a:hover,
			.bbpress-sidebar .widget_tag_cloud .tagcloud a:hover,
			.bb-modal.bbp-topic-form .bbp-submit-wrapper a#bbp-close-btn:hover,
			.bbp-mfp-zoom-in fieldset.bbp-form .bbp-submit-wrapper a#bbp-close-btn:hover,
			#bbpress-forums .bbp-reply-form.bb-modal a#bbp-cancel-reply-to-link:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			/* Headings link color */
			.item-title a,
			#bbpress-forums#bbpress-forums .bs-forums-items .item-tags ul li a:hover,
			.bb-quick-reply-form-wrap .bbp-reply-form .bbp-form > legend #bbp-reply-exerpt,
			.bbp-reply-form-success-modal .bbp-reply-form-success .reply-content .content-title .activity-discussion-title-wrap a {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.item-title a:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			.bbpress .widget_display_forums > ul.bb-sidebar-forums > li a:before {
				border-color: <?php echo textToColor( bbp_get_topic_forum_title() ); ?>;
			}

			.bbpress .widget_display_forums > ul.bb-sidebar-forums > li a:before {
				background-color: <?php echo color2rgba( textToColor( bbp_get_topic_forum_title() ), 0.5 ); ?>;
			}

		<?php

		$css = ob_get_contents();
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		ob_end_clean();

		echo $css;
		if ( ! is_array( $custom_css ) ) {
			$custom_css = array();
		}
		$custom_css['css'] = $css;

		?>

		</style>
		<?php

		// save processed css.
		set_transient( 'buddyboss_theme_compressed_forums_custom_css', $custom_css );

	}

	/* Add Action */
	if ( class_exists( 'bbPress' ) ) {
		add_action( 'wp_head', 'boss_generate_option_forums_css', 99 );
	}
}

if ( ! function_exists( 'boss_generate_option_learndash_css' ) ) {
	function boss_generate_option_learndash_css() {

		if ( is_customize_preview() ) {
			$custom_css = '';
		} else {
			$custom_css = get_transient( 'buddyboss_theme_compressed_learndash_custom_css' );
		}

		if ( ! empty( $custom_css ) && isset( $custom_css['css'] ) ) {

			echo "
            <style id=\"buddyboss_theme-learndash-style\">
                {$custom_css["css"]}
            </style>
            ";

			return false;

		}

		$primary_color = buddyboss_theme_get_option( 'accent_color' );
		$header_height = buddyboss_theme_get_option( 'header_height' );
		$is_admin_bar  = is_admin_bar_showing() ? 32 : 0;

		$button_radius = buddyboss_theme_get_option( 'button_default_radius' );
		?>

		<style id="buddyboss_theme-learndash-style">

		<?php ob_start(); ?>

			.learndash-wrapper .bb-ld-tabs #learndash-course-content {
				top: -<?php echo $header_height + $is_admin_bar + 10; ?>px;
			}

			.wpProQuiz_content .wpProQuiz_results .quiz_continue_link a#quiz_continue_link {
				background:  <?php echo $primary_color; ?>;
			}

			.wpProQuiz_content .wpProQuiz_reviewDiv .wpProQuiz_reviewQuestion > ol li.wpProQuiz_reviewQuestionTarget,
			.wpProQuiz_content .wpProQuiz_reviewDiv .wpProQuiz_reviewQuestion > ol li.wpProQuiz_reviewQuestionTarget.wpProQuiz_reviewQuestionSolved,
			.wpProQuiz_content .wpProQuiz_reviewDiv .wpProQuiz_reviewQuestion > ol li.wpProQuiz_reviewQuestionTarget.wpProQuiz_reviewQuestionReview,
			.wpProQuiz_content .wpProQuiz_box > ol li.wpProQuiz_reviewQuestionTarget,
			.wpProQuiz_content .wpProQuiz_box > ol li.wpProQuiz_reviewQuestionTarget.wpProQuiz_reviewQuestionSolved,
			.wpProQuiz_content .wpProQuiz_box > ol li.wpProQuiz_reviewQuestionTarget.wpProQuiz_reviewQuestionReview,
			.wpProQuiz_content .wpProQuiz_resultTable table tr:nth-child(2) td:first-child:before,
			.wpProQuiz_sending dd.course_progress div.course_progress_blue,
			.wpProQuiz_content .wpProQuiz_resultTable table tr:nth-child(2) td:first-child:before,
			.wpProQuiz_sending dd.course_progress div.course_progress_blue,
			.lms-topic-sidebar-progress .course-progress-bar,
			.bb-sfwd-aside.bb-dark-theme .lms-lesson-item.current > a .i-progress.i-progress-completed,
			.bb-sfwd-aside.bb-dark-theme .lms-topic-item.current > a .i-progress.i-progress-completed,
			.bb-sfwd-aside.bb-dark-theme .lms-quiz-item.current .i-progress.i-progress-completed,
			.wpProQuiz_content ul.wpProQuiz_questionList[data-type='matrix_sort_answer'] li ul.wpProQuiz_maxtrixSortCriterion li.wpProQuiz_sortStringItem.ui-sortable-helper,
			.wpProQuiz_content .wpProQuiz_matrixSortString .wpProQuiz_sortStringList.ui-sortable li.wpProQuiz_sortStringItem.ui-sortable-helper,
			.i-progress.i-progress-completed,
			.learndash-wrapper .ld-breadcrumbs .ld-status.ld-status-progress,
			.learndash-wrapper .ld-status-icon.ld-quiz-complete,
			.wpProQuiz_content .wpProQuiz_questionList[data-type='essay'] form input[type=file] + label,
			.learndash-wrapper .wpProQuiz_content input[type=radio].wpProQuiz_questionInput.bbstyled:checked + span:after,
			.learndash-wrapper .wpProQuiz_content input[type=checkbox].wpProQuiz_questionInput.bbstyled:checked + span,
			.learndash-wrapper .wpProQuiz_content .wpProQuiz_time_limit .wpProQuiz_progress,
			.ld-modal.ld-login-modal .ld-login-modal-form input[type='submit'],
			#learndash-page-content .sfwd-course-nav .learndash_next_prev_link a:hover,
			.wpProQuiz_questionList[data-type="assessment_answer"] .wpProQuiz_questionListItem label.is-selected:before,
			.wpProQuiz_questionList[data-type="single"] .wpProQuiz_questionListItem label.is-selected:before,
			.wpProQuiz_questionList[data-type="multiple"] .wpProQuiz_questionListItem label.is-selected:before {
				background-color:  <?php echo $primary_color; ?>;
			}

			.group_courses ul.courses-group-list .bp-learndash-progress-bar progress::-moz-progress-bar {
				background-color:  <?php echo $primary_color; ?>;
			}

			.learndash-complete .ld-item-list-item-expanded .ld-table-list-items .ld-table-list-item .ld-table-list-item-quiz .ld-quiz-complete,
			.group_courses ul.courses-group-list .bp-learndash-progress-bar progress::-webkit-progress-value {
				background-color:  <?php echo $primary_color; ?>;
			}

			.wpProQuiz_content .wpProQuiz_button2,
			.bb_progressbar_label,
			.wpProQuiz_content .wpProQuiz_questionList[data-type='essay'] form input[type=submit],
			.lms-topic-sidebar-wrapper .lms-links ul li a,
			.lms-topic-sidebar-wrapper .lms-links ul li a:hover,
			#learndash-page-content .ld-focus-comments .ld-expand-button.ld-button-alternate,
			#learndash-page-content .ld-focus-comments .ld-comment-avatar .ld-comment-avatar-author a.ld-comment-permalink,
			#learndash-page-content .ld-focus-comments .form-submit #submit,
			.widget_ldcoursenavigation .learndash-wrapper .ld-course-navigation-actions .ld-expand-button.ld-button-alternate,
			.ld-sidebar-widgets li.widget .widgettitle a {
				color:  <?php echo $primary_color; ?>;
			}

			.wpProQuiz_content .wpProQuiz_button2,
			.wpProQuiz_content .wpProQuiz_questionList[data-type='essay'] form input[type=submit],
			.wpProQuiz_content ul.wpProQuiz_questionList[data-type='matrix_sort_answer'] li ul.wpProQuiz_maxtrixSortCriterion li.wpProQuiz_sortStringItem.ui-sortable-helper,
			.wpProQuiz_content .wpProQuiz_matrixSortString .wpProQuiz_sortStringList.ui-sortable li.wpProQuiz_sortStringItem.ui-sortable-helper,
			.bb-progress .bb-progress-circle,
			.bb-sfwd-aside.bb-dark-theme .bb-progress .bb-progress-circle,
			.lms-topic-sidebar-wrapper .lms-links ul li a,
			.learndash-wrapper .wpProQuiz_content input[type=checkbox].wpProQuiz_questionInput.bbstyled:checked + span,
			.learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label.is-selected,
			.learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label:focus-within,
			#learndash-page-content .ld-focus-comments .comment .ld-comment-avatar > img,
			#learndash-page-content .ld-focus-comments .form-submit #submit,
			.wpProQuiz_questionList[data-type="multiple"] .wpProQuiz_questionListItem label.is-selected:before {
				border-color:  <?php echo $primary_color; ?>;
			}

			.bb-course-video-overlay .bb-course-play-btn:hover:after {
				border-color:  <?php echo 'transparent transparent transparent' . $primary_color; ?>;
			}

			.learndash-wrapper .ld-loading::before {
				border-top-color: <?php echo $primary_color; ?>;
			}

			.quiz_progress_container #quiz_shape_progress {
				stroke: <?php echo $primary_color; ?>;
			}

			.ld-modal.ld-login-modal.ld-can-register .ld-login-modal-register {
				background-color: <?php echo $primary_color; ?>;
			}

			.bb-quiz-list .bb-quiz-icon g {
				stroke: <?php echo buddyboss_theme_get_option( 'body_text_color' ); ?>;
			}

			.learndash_next_prev_link a,
			#buddypress ul.courses-group-list .course-link a.button,
			.learndash-wrapper .ld-pagination .ld-pages a,
			.learndash-wrapper #ld-profile .ld-item-list-item-expanded .ld-table-list .ld-table-list-item-preview .ld-table-list-title a {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.learndash_next_prev_link a:hover,
			#buddypress ul.courses-group-list .course-link a.button:hover,
			.learndash-wrapper .ld-pagination .ld-pages a:hover,
			.learndash-wrapper #ld-profile .ld-item-list-item-expanded .ld-table-list .ld-table-list-item-preview .ld-table-list-title a:hover,
			#certificate_list .bb-certificate-title a:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			/* Headings link color */

			.bb-lesson-head,
			.bb-about-instructor h5 a,
			.bb_profile_course_wrapper a,
			#quiz_progress_details p a,
			.lms-topic-sidebar-wrapper .lms-lessions-list > ol li a.bb-lesson-head,
			.learndash_course_content .bb-lessons-list li a.bb-lesson-head,
			.learndash_course_content .bb-quiz-list a,
			.bb-type-list.bb-lms-list-inside a,
			.bb-quiz-list a,
			.lms-topic-sidebar-wrapper .lms-course-members-list .course-members-list a,
			.lms-topic-sidebar-wrapper .group-exec-list a .lms-group-lead span:first-child,
			.lms-topic-sidebar-wrapper .lms-group-flag .lms-group-heading a span,
			.lms-topic-sidebar-wrapper .course-group-list a,
			.learndash-wrapper .ld-alert .ld-alert-content a {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.learndash-wrapper .ld-table-list .ld-table-list-items div.ld-table-list-item a.ld-table-list-item-preview:hover .ld-topic-title:before,
			.learndash-wrapper .ld-item-list-item-expanded .ld-table-list-items .ld-table-list-item .ld-table-list-item-quiz .ld-table-list-item-preview:hover .ld-item-title,
			.learndash-wrapper .ld-table-list .ld-table-list-items div.ld-table-list-item a.ld-table-list-item-preview:hover .ld-topic-title,
			.learndash-wrapper .learndash_content_wrap .ld-table-list-item-quiz:hover .ld-item-title,
			.learndash-wrapper .ld-item-list .ld-item-list-item.ld-item-lesson-item .ld-item-list-item-preview:hover .ld-item-name .ld-item-title .ld-item-components span,
			.learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-list-item-preview:hover a.ld-item-name .ld-item-title,
			.bb-course-meta strong a:hover,
			.bb-lesson-head:hover,
			.bb-about-instructor h5 a:hover,
			.bb_profile_course_wrapper a:hover,
			#quiz_progress_details p a:hover,
			.lms-topic-sidebar-wrapper .lms-lessions-list > ol li a.bb-lesson-head:hover,
			.learndash_course_content .bb-lessons-list li a.bb-lesson-head:hover,
			.learndash_course_content .bb-quiz-list a:hover,
			.bb-type-list.bb-lms-list-inside a:hover,
			.bb-quiz-list a:hover,
			.group_courses ul.courses-group-list .course-name a:hover,
			.lms-header-instructor .bb-about-instructor h5 a:hover,
			.lms-topic-sidebar-wrapper .lms-course-members-list .course-members-list a:hover,
			.lms-topic-sidebar-wrapper .group-exec-list a:hover .lms-group-lead span:first-child,
			.lms-topic-sidebar-wrapper .lms-group-flag .lms-group-heading a:hover span,
			.lms-topic-sidebar-wrapper .course-group-list a:hover,
			.learndash-wrapper .bb-ld-info-bar .ld-breadcrumbs .ld-breadcrumbs-segments span a:hover,
			.bb-course-meta strong a:hover,
			.learndash-wrapper .ld-alert .ld-alert-content a:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			/* Body Text color */
			.bb-course-categories,
			.lms-topic-sidebar-instructor .bb-about-instructor > h4,
			.learndash-wrapper .ld-item-list.ld-lesson-list .ld-lesson-section-heading,
			.learndash-wrapper #ld-profile .ld-profile-summary .ld-profile-stats .ld-profile-stat strong,
			.bb-course-footer,
			.learndash-wrapper .ld-login-modal .ld-login-modal-form label,
			.learndash-wrapper .ld-login-modal .ld-login-modal-login .ld-modal-heading {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.group_courses ul.courses-group-list li.item-entry .item-avatar > a {
				background: <?php echo buddyboss_theme_get_option( 'buddyboss_theme_group_cover_bg' ); ?>;
			}

			.wpProQuiz_content .wpProQuiz_catOverview span,
			.group_courses ul.courses-group-list li.item-entry .list-wrap {
				background-color: <?php echo buddyboss_theme_get_option( 'body_blocks' ); ?>;
			}

			/* Buttons */
			.learndash-wrapper .bb-single-course-sidebar .ld-status,
			.bb-single-course-sidebar .btn-join, .bb-single-course-sidebar #btn-join {
				border-radius: <?php echo $button_radius; ?>px;
			}

		<?php

		$css = ob_get_contents();
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		ob_end_clean();

		echo $css;

		if ( ! is_array( $custom_css ) ) {
			$custom_css = array();
		}
		$custom_css['css'] = $css;

		?>

		</style>
		<?php

		// save processed css.
		set_transient( 'buddyboss_theme_compressed_learndash_custom_css', $custom_css );

	}

	/* Add Action */
	if ( class_exists( 'SFWD_LMS' ) ) {
		add_action( 'wp_head', 'boss_generate_option_learndash_css', 99 );
	}
}

if ( ! function_exists( 'boss_generate_option_woocommerce_css' ) ) {
	function boss_generate_option_woocommerce_css() {

		if ( is_customize_preview() ) {
			$custom_css = '';
		} else {
			$custom_css = get_transient( 'buddyboss_theme_compressed_woocommerce_custom_css' );
		}

		if ( ! empty( $custom_css ) && isset( $custom_css['css'] ) ) {

			echo "
            <style id=\"buddyboss_theme-woocommerce-style\">
                {$custom_css["css"]}
            </style>
            ";

			return false;

		}

		$primary_color = buddyboss_theme_get_option( 'accent_color' );

		$button_radius = buddyboss_theme_get_option( 'button_default_radius' );

		?>
		<style id="buddyboss_theme-woocommerce-style">

		<?php ob_start(); ?>

			/* Primary color */
			.woocommerce #respond input#submit.alt,
			.woocommerce input.button.alt,
			.woocommerce-checkout input[type=checkbox]:checked + span:before,
			.woocommerce-product-search button {
				background-color: <?php echo $primary_color; ?>;
			}

			.woocommerce-cart table.shop_table td.actions > button.butto:hover,
			.woocommerce-checkout input[type=checkbox]:checked + span:before {
				border-color: <?php echo $primary_color; ?>;
			}

			.woocommerce div.product span.price,
			.woocommerce [type='checkbox']:checked + span,
			.widget .woocommerce-product-search button i:before {
				color: <?php echo $primary_color; ?>;
			}

			.woocommerce-checkout [type='checkbox']:checked + span:before {
				-webkit-box-shadow: 0px 0px 0px 1px <?php echo $primary_color; ?>;
				-moz-box-shadow: 0px 0px 0px 1px <?php echo $primary_color; ?>;
				box-shadow: 0px 0px 0px 1px <?php echo $primary_color; ?>;
			}

			article.job_listing ul.job-listing-meta li.location a {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			article.job_listing ul.job-listing-meta li.location a:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

		<?php

		$css = ob_get_contents();
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		ob_end_clean();

		echo $css;
		if ( ! is_array( $custom_css ) ) {
			$custom_css = array();
		}
		$custom_css['css'] = $css;

		?>

		</style>
		<?php

		// save processed css.
		set_transient( 'buddyboss_theme_compressed_woocommerce_custom_css', $custom_css );

	}

	/* Add Action */
	if ( function_exists( 'WC' ) ) {
		add_action( 'wp_head', 'boss_generate_option_woocommerce_css', 99 );
	}
}

if ( ! function_exists( 'boss_generate_option_events_css' ) ) {
	function boss_generate_option_events_css() {

		if ( is_customize_preview() ) {
			$custom_css = '';
		} else {
			$custom_css = get_transient( 'buddyboss_theme_compressed_events_custom_css' );
		}

		if ( ! empty( $custom_css ) && isset( $custom_css['css'] ) ) {

			echo "
            <style id=\"buddyboss_theme-events-style\">
                {$custom_css["css"]}
            </style>
            ";

			return false;

		}

		$primary_color = buddyboss_theme_get_option( 'accent_color' );

		$button_radius = buddyboss_theme_get_option( 'button_default_radius' );

		?>
		<style id="buddyboss_theme-events-style">

		<?php ob_start(); ?>

			/* Buttons */
			#tribe-bar-form .tribe-bar-submit input[type=submit],
			#tribe-events .bs-single-content .tribe-events-cal-links a.tribe-events-button,
			#tribe-events-content.tribe-events-list a.tribe-events-ical,
			.tribe-events-venue .tribe-events-venue-meta a.tribe-events-gmap {
				border-radius: <?php echo $button_radius; ?>px;
			}

			/* Primary color */
			.tribe-grid-allday .tribe-events-week-allday-single h3.entry-title,
			.tribe-grid-body .tribe-events-week-hourly-single h3.entry-title {
				background-color: <?php echo color2rgba( $primary_color, 0.75 ); ?>;
				border-color: <?php echo color2rgba( $primary_color, 0.75 ); ?>;
			}

			.tribe-grid-allday .tribe-events-week-allday-single h3.entry-title:hover,
			.tribe-grid-body .tribe-events-week-hourly-single h3.entry-title:hover {
				background-color: <?php echo color2rgba( $primary_color, 0.95 ); ?>;
				border-color: <?php echo color2rgba( $primary_color, 0.95 ); ?>;
			}

			.tribe-grid-allday .tribe-week-today,
			.tribe-week-grid-wrapper .tribe-grid-content-wrap .tribe-events-mobile-day.tribe-week-today {
				background-color: <?php echo color2rgba( $primary_color, 0.05 ); ?>;
			}

			#tribe-events .tribe-events-button,
			#tribe-events .tribe-events-button:hover,
			#tribe-bar-form .tribe-bar-submit input[type=submit],
			#tribe-bar-form .tribe-bar-submit input[type=submit]:hover,
			.tribe-events-venue .tribe-events-venue-meta a.tribe-events-gmap,
			.tribe-common .tribe-common-c-btn,
			.tribe-common a.tribe-common-c-btn,
			.tribe-common .tribe-common-c-btn:hover,
			.tribe-common a.tribe-common-c-btn:hover,
			.tribe-events-venue .tribe-events-venue-meta a.tribe-events-gmap:hover {
				background: <?php echo $primary_color; ?>;
			}

			.tribe-events-venue .tribe-events-venue-meta a.tribe-events-gmap:before,
			.tribe-events .tribe-events-c-ical__link:hover,
			.events-archive.events-gridview #tribe-events-content table.tribe-events-calendar tbody td div.tribe_events,
			.events-archive.events-gridview #tribe-events-content table.tribe-events-calendar tbody td div.type-tribe_events,
			.tribe-events-grid .tribe-grid-header .tribe-week-today {
				background-color: <?php echo $primary_color; ?>;
			}

			#tribe-events .bs-single-content .tribe-events-cal-links a.tribe-events-button,
			#tribe-events .bs-single-content .tribe-events-cal-links a.tribe-events-button:hover,
			.tribe-events-venue .tribe-events-venue-meta a.tribe-events-gmap,
			.tribe-events .tribe-events-c-ical__link,
			.tribe-events-venue .tribe-events-venue-meta a.tribe-events-gmap:hover {
				border-color: <?php echo $primary_color; ?>;
			}

			#tribe-events .bs-single-content .tribe-events-cal-links a.tribe-events-button,
			#tribe-events .bs-single-content .tribe-events-cal-links a.tribe-events-button:hover,
			.tribe-events .tribe-events-calendar-month__day--current .tribe-events-calendar-month__day-date,
			.tribe-events .tribe-events-calendar-month__day--current .tribe-events-calendar-month__day-date-link,
			.tribe-events .tribe-events-c-ical__link,
			#tribe-geo-results .tribe-event-featured a {
				color: <?php echo $primary_color; ?>;
			}

			#tribe-geo-results .tribe-event-featured a:hover {
				color: <?php echo $primary_color; ?>;
			}

			/* Headings link color */
			.tribe_ext_sch_day_view .tribe-events-list-event-title a,
			.type-tribe_events .bs-event-heading h2.tribe-events-list-event-title a,
			.type-tribe_events .bs-event-heading h2.tribe-events-map-event-title a,
			#tribe-events-photo-events .type-tribe_events.tribe-events-photo-event h2.tribe-events-list-event-title a,
			.tribe-events-single ul.tribe-related-events h3.tribe-related-events-title a,
			.single-tribe_events #tribe-events-content .tribe-events-event-meta dl a,
			#tribe-geo-results .tribe-event-featured h2.tribe-events-map-event-title a,
			.type-tribe_events .tribe-mini-calendar-event .list-info .tribe-events-title a {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.tribe_ext_sch_day_view .tribe-events-list-event-title a:hover,
			.type-tribe_events .bs-event-heading h2.tribe-events-list-event-title a:hover,
			.type-tribe_events .bs-event-heading h2.tribe-events-map-event-title a:hover,
			#tribe-events-photo-events .type-tribe_events.tribe-events-photo-event h2.tribe-events-list-event-title a:hover,
			.tribe-events-single ul.tribe-related-events h3.tribe-related-events-title a:hover,
			.single-tribe_events #tribe-events-content .tribe-events-event-meta dl a:hover,
			#tribe-geo-results .tribe-event-featured h2.tribe-events-map-event-title a:hover,
			.type-tribe_events .tribe-mini-calendar-event .list-info .tribe-events-title a:hover {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			/* Body Text color */
			.type-tribe_events .bs-event-heading .tribe-event-schedule-short .bs-schedule-short-d,
			#tribe_events_filters_wrapper.tribe-events-filters-vertical label.tribe-events-filters-label,
			.tribe-events-day .tribe-events-day-time-slot h5,
			#tribe-events-content .tribe-events-day-time-slot h5,
			#tribe-events-content .tribe-events-tooltip h4,
			.tribe-events-week #tribe-events-content .tribe-events-right .tribe-events-tooltip h4,
			.single-tribe_events .bs-event-heading .tribe-event-schedule-short .bs-schedule-short-d,
			.type-tribe_events .tribe-mini-calendar-event .list-date .list-daynumber {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			@media (max-width: 768px) {
				.tribe-mobile-day-date {
					color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
				}

				.tribe-events-sub-nav li a,
				.tribe-events-sub-nav li a:visited {
					color: <?php echo $primary_color; ?>;
				}

				.tribe-events-sub-nav li a:hover {
					color: <?php echo $primary_color; ?>;
				}
			}

			.type-tribe_events .tribe-mini-calendar-event{
				background-color: <?php echo buddyboss_theme_get_option( 'body_blocks' ); ?>;
			}


		<?php

		$css = ob_get_contents();
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		ob_end_clean();

		echo $css;
		if ( ! is_array( $custom_css ) ) {
			$custom_css = array();
		}
		$custom_css['css'] = $css;

		?>

		</style>
		<?php

		// save processed css.
		set_transient( 'buddyboss_theme_compressed_events_custom_css', $custom_css );

	}

	/* Add Action */
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		add_action( 'wp_head', 'boss_generate_option_events_css', 99 );
	}
}

if ( ! function_exists( 'boss_generate_option_plugins_css' ) ) {
	function boss_generate_option_plugins_css() {

		if ( is_customize_preview() ) {
			$custom_css = '';
		} else {
			$custom_css = get_transient( 'buddyboss_theme_compressed_plugins_custom_css' );
		}

		if ( ! empty( $custom_css ) && isset( $custom_css['css'] ) ) {

			echo "
            <style id=\"buddyboss_theme-plugins-style\">
                {$custom_css["css"]}
            </style>
            ";

			return false;

		}

		$primary_color = buddyboss_theme_get_option( 'accent_color' );

		?>
		<style id="buddyboss_theme-plugins-style">

		<?php ob_start(); ?>

			.edd-submit.button.blue,
			#edd-purchase-button,
			.edd-submit,
			[type=submit].edd-submit,
			.edd-submit.button.blue:hover,
			#edd-purchase-button:hover,
			.edd-submit:hover,
			[type=submit].edd-submit:hover {
				background: <?php echo $primary_color; ?>;
			}
			.edd_download_inner .edd_download_buy_button .edd_purchase_submit_wrapper a.edd-add-to-cart,
			.edd_download_inner .edd_download_buy_button .edd_purchase_submit_wrapper a.edd_go_to_checkout,
			.edd_download_inner .edd_download_buy_button [type=submit].edd-add-to-cart.edd-submit {
				color: <?php echo $primary_color; ?>;
			}

			.edd_download_inner .edd_download_buy_button .edd_purchase_submit_wrapper a.edd-add-to-cart:hover,
			.edd_download_inner .edd_download_buy_button .edd_purchase_submit_wrapper a.edd_go_to_checkout:hover,
			.edd_download_inner .edd_download_buy_button [type=submit].edd-add-to-cart.edd-submit:hover,
			.gfield_radio input[type=radio]:checked + label:after,
			.gfield_radio input[type=radio]:checked + .bb-radio-label:after,
			.gfield_checkbox input[type=checkbox]:checked + label:before {
				background-color: <?php echo $primary_color; ?>;
			}

			.edd_download_inner .edd_download_buy_button .edd_purchase_submit_wrapper a.edd-add-to-cart:hover,
			.edd_download_inner .edd_download_buy_button .edd_purchase_submit_wrapper a.edd_go_to_checkout:hover,
			.edd_download_inner .edd_download_buy_button [type=submit].edd-add-to-cart.edd-submit:hover,
			.gfield_checkbox input[type=checkbox]:checked + label:before {
				border-color: <?php echo $primary_color; ?>;
			}

			/* Headings link color */


			/* Body Text color */


		<?php

		$css = ob_get_contents();
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		ob_end_clean();

		echo $css;
		if ( ! is_array( $custom_css ) ) {
			$custom_css = array();
		}
		$custom_css['css'] = $css;

		?>

		</style>
		<?php

		// save processed css.
		set_transient( 'buddyboss_theme_compressed_plugins_custom_css', $custom_css );

	}

	/* Add Action */
	if ( class_exists( 'BuddyForms' ) || class_exists( 'WPCF7' ) || class_exists( 'Easy_Digital_Downloads' ) || class_exists( 'GFForms' ) || class_exists( 'LifterLMS' ) || class_exists( 'IT_Exchange' ) || class_exists( 'Ninja_Forms' ) || class_exists( 'WPForms' ) ) {
		add_action( 'wp_head', 'boss_generate_option_plugins_css', 99 );
	}
}

/**
 * Buddyboss theme custom styling
 */
if ( ! function_exists( 'boss_generate_option_custom_css' ) ) {

	function boss_generate_option_custom_css() {

		global $post;

		$fullscreen_page_padding = false;

		if ( ! empty( $post ) ) {
			$fullscreen_page_padding = get_post_meta( $post->ID, '_wp_page_padding', true );
		}

		$admin_bar_offset = is_admin_bar_showing() ? 67 : 21;
		?>

		<style id="buddyboss_theme-custom-style">

		<?php ob_start(); ?>

		<?php if ( $fullscreen_page_padding ) { ?>
			.page-template-page-fullscreen.page-id-<?php echo $post->ID; ?> .site-content {
				padding: <?php echo $fullscreen_page_padding; ?>px;
			}
		<?php } ?>

		a.bb-close-panel i {
			top: <?php echo $admin_bar_offset; ?>px;
		}


		<?php

		$css = ob_get_contents();
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		ob_end_clean();

		echo $css;
		?>

		</style>
		<?php

	}

	/* Add Action */
	add_action( 'wp_head', 'boss_generate_option_custom_css', 99 );
}

/**
 * WC Vendor Custom Styling
 */
if ( ! function_exists( 'boss_generate_option_wc_vendors_css' ) ) {
	function boss_generate_option_wc_vendors_css() {

		if ( is_customize_preview() ) {
			$custom_css = '';
		} else {
			$custom_css = get_transient( 'buddyboss_theme_compressed_wc_vendor_custom_css' );
		}

		if ( ! empty( $custom_css ) && isset( $custom_css['css'] ) ) {

			echo "
            <style id=\"buddyboss_theme-wc_vendors-style\">
                {$custom_css["css"]}
            </style>
            ";

			return false;

		}

		$primary_color               = buddyboss_theme_get_option( 'accent_color' );
		$alternate_link_color        = buddyboss_theme_get_option( 'heading_text_color' );
		$alternate_link_active_color = buddyboss_theme_get_option( 'heading_text_color' );

		?>
		<style id="buddyboss_theme-wc-vendor-style">

		<?php ob_start(); ?>

			.wcv-dashboard-navigation ul li a,
			.wc_table-export_orders.table-bordered thead th,
			table.table-vendor-sales-report thead th,
			.wc_table-export_orders.table-bordered tbody tr td,
			table.table-vendor-sales-report tbody tr td,
			div.wcv-grid .wcv-navigation ul.menu.black li a,
			.form-table tbody tr td label {
				color: <?php echo $alternate_link_color; ?>;
			}

			.wcv-dashboard-navigation ul li a:hover,
			.wcv-dashboard-navigation ul li a:focus,
			div.wcv-grid .wcv-navigation ul.menu.black li a:hover,
			div.wcv-grid .wcv-navigation ul.menu.black li a:focus,
			div.wcv-grid .wcv-navigation ul.menu.black li.active a,
			div.wcv-grid a:hover {
				color: <?php echo $alternate_link_active_color; ?>;
			}

			div.wcv-grid .wcv-search-form .control-group .control.append-button .wcv-button.wcv-search-product,
			.woocommerce .woocommerce-MyAccount-content input[type="submit"][name="apply_for_vendor_submit"] {
				background-color: <?php echo $alternate_link_active_color; ?>;
			}

			 div.wcv-grid .wcv-navigation ul.menu.black li a:hover,
			 div.wcv-grid .wcv-navigation ul.menu.black li a:focus,
			 div.wcv-grid .wcv-navigation ul.menu.black li.active a {
				border-bottom-color: <?php echo $alternate_link_active_color; ?>;
			 }

			 div.wcv-grid h1, div.wcv-grid h2, div.wcv-grid h3, div.wcv-grid h4, div.wcv-grid h5, div.wcv-grid h6{
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			 }

		<?php

		$css = ob_get_contents();
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		ob_end_clean();

		echo $css;
		if ( ! is_array( $custom_css ) ) {
			$custom_css = array();
		}
		$custom_css['css'] = $css;

		?>

		</style>
		<?php

		// save processed css.
		set_transient( 'buddyboss_theme_compressed_wc_vendor_custom_css', $custom_css );

	}

	/* Add Action */
	if ( class_exists( 'WC_Vendors' ) ) {
		add_action( 'wp_head', 'boss_generate_option_wc_vendors_css', 99 );
	}
}


/**
 * LifterLMS Custom Styling
 */
if ( ! function_exists( 'boss_generate_option_lifterLMS_css' ) ) {
	function boss_generate_option_lifterLMS_css() {

		if ( is_customize_preview() ) {
			$custom_css = '';
		} else {
			$custom_css = get_transient( 'buddyboss_theme_compressed_lifterLMS_custom_css' );
		}

		if ( ! empty( $custom_css ) && isset( $custom_css['css'] ) ) {

			echo "
            <style id=\"buddyboss_theme-lifterLMS-style\">
                {$custom_css["css"]}
            </style>
            ";

			return false;

		}

		$primary_color = buddyboss_theme_get_option( 'accent_color' );

		$button_radius = buddyboss_theme_get_option( 'button_default_radius' );

		?>
		<style id="buddyboss_theme-lifterLMS-style">

		<?php ob_start(); ?>

			.llms-pointer.llms-inner .lifterlms-lessions-list .llms-syllabus-wrapper-quizzes .llms-quizz-preview .llms-lesson-link h5,
			.single-course.llms-pointer .llms-instructor-info .llms-author-holder .meta-saperator {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.llms-pointer.llms-inner .lifterlms-lessions-list .llms-syllabus-wrapper-quizzes .llms-lesson-preview.is-incomplete .llms-lesson-link:after,
			.llms-pointer.llms-inner .lifterlms-lessions-list .llms-syllabus-wrapper-quizzes .llms-lesson-preview.is-complete .llms-lesson-preview.llms-quizz-preview.is-incomplete .llms-lesson-link:after,
			.llms-pointer.llms-inner .llms-lesson-link-locked:after,
			.llms-loop-item.course .llms-pa-permalink
			{
				border-color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			  }

			.llms-pointer.llms-inner .lifterlms-lessions-list .llms-syllabus-wrapper .llms-lesson-link:hover .llms-main h5,
			.llms-pointer.llms-inner .lifterlms-lessions-list .llms-syllabus-wrapper-quizzes .llms-quizz-preview .llms-lesson-link:hover h5,
			.llms-pointer.llms-inner .llms-lesson-preview.llms-quizz-preview:hover .llms-lesson-link h5,
			.llms-syllabus-wrapper .llms-lesson-preview.llms-quizz-preview:hover .llms-lesson-link h5,
			.llms-syllabus-wrapper .llms-lesson-preview .llms-lesson-link:hover .llms-lesson-title,
			.bb-llms-content-wrap .llms-syllabus-wrapper .llms-lesson-preview .llms-lesson-link:hover .llms-extra .llms-lesson-counter,
			.single-course.llms-pointer .llms-instructor-info .llms-author-holder a:hover .llms-author-info.name,
			.single-llms_quiz.llms-pointer #lifterlms-page-content .lifterlms_content_wrap .llms-quiz-attempt-question-header:hover h3.llms-question-title,
			.single-llms_quiz.llms-pointer #lifterlms-page-content .lifterlms_content_wrap .llms-quiz-attempt-question-header:hover .toggle-answer:before,
			.post-type-archive-course.llms-pointer .bb-course-items .bb-course-item-wrap .bb-cover-list-item .bb-card-course-details .bb-course-meta strong a:hover,
			.post-type-archive-course.llms-pointer .bb-course-items .bb-course-item-wrap .bb-cover-list-item .bb-card-course-details .bb-course-title a:hover,
			.llms-loop-item-content .llms-loop-title:hover,
			.llms-widget-syllabus .lesson-title a:hover,
			.llms-widget-syllabus .lesson-title.done a:hover,
			.llms-syllabus-wrapper .llms-lesson-main .llms-lesson-holder .llms-lesson-link:hover .llms-main h5 {
				color: <?php echo buddyboss_theme_get_option( 'accent_color' ); ?>;
			}

			.llms-pa-posts.llms-pa-posts--single .llms-pa-discussion-list .llms_pa_post_comment.llms-pa-read:after,
			.nf-form-wrap .nf-form-content .nf-field-element input[type=button],
			body div.wpforms-container-full .wpforms-form button[type=submit],
			body div.wpforms-container-full .wpforms-form button[type=submit]:hover,
			.frm_submit button,
			.llms-sl .llms-button-primary,
			.frm_forms .frm_form_fields fieldset .frm_submit .frm_button_submit,
			.llms-button-action.clicked,
			.llms-button-primary.clicked,
			.llms-quiz-ui .llms-question-wrapper.type--scale .llms-aq-scale .llms-aq-scale-range .llms-aq-scale-radio input[type="radio"]:checked + .llms-aq-scale-button,
			.llms-quiz-ui .llms-question-wrapper.type--scale .llms-aq-scale .llms-aq-scale-range .llms-aq-scale-radio .llms-aq-scale-button:hover,
			.llms-pointer.llms-inner .lifterlms-lesson-nav .lifterlms_next_prev_link a:hover,
			.lifter-sidebar-widgets li.widget a.button:not(.logout-link):hover {
				background-color: <?php echo $primary_color; ?>;
			}

			.llms-pa-posts.llms-pa-posts--single .llms-pa-discussion-list .llms_pa_post_comment.llms-pa-read:after,
			.llms_pa_post_comment.llms-pa-unread:after,
			.llms-pa-posts.llms-pa-posts--single #llms-pa-post-discussion-form .llms-form-field.type-submit #llms_pa_leave_comment,
			.nf-form-wrap .nf-form-content .nf-field-element input[type=button],
			body div.wpforms-container-full .wpforms-form button[type=submit]:hover,
			body .wpforms-confirmation-container-full,
			body div[submit-success] > .wpforms-confirmation-container-full:not(.wpforms-redirection-message),
			#wp-llms_pa_post_comment-wrap.wp-core-ui .button.button-small,
			.bb-progress .bb-progress-circle {
				border-color: <?php echo $primary_color; ?>;
			}

			.lifterlms .llms-sd-items .llms-sd-item:hover a, 
			.llms-widget-syllabus .lesson-complete-placeholder.done,
			.llms-quiz-ui .llms-question-wrapper.type--upload .llms-aq-uploader .fa.fa-upload,
			.frm_forms .frm_form_fields fieldset .frm_dropzone .dz-message .frm_icon_font.frm_upload_icon:before {
				color: <?php echo $primary_color; ?>;
			}

			.single-llms_assignment #lifterlms-page-content .lifterlms_content_wrap p a,
			.post-type-archive-course.llms-pointer .bb-course-items .bb-course-item-wrap .bb-cover-list-item .bb-card-course-details .bb-course-meta strong a,
			.single-llms_quiz.llms-pointer #lifterlms-page-content .lifterlms_content_wrap p:not(.llms-error) a {
				color: <?php echo buddyboss_theme_get_option( 'heading_text_color' ); ?>;
			}

			.single-lesson.llms-pointer #lifterlms-page-content #lifterlms-lesson-header .lifterlms-header-instructor .bb-about-instructor > .flex .bb-content-wrap h5 .bb-about-instructor-date::before,
			.single-llms_quiz.llms-pointer #lifterlms-page-content #lifterlms-lesson-header .lifterlms-header-instructor .bb-about-instructor > .flex .bb-content-wrap h5 .bb-about-instructor-date::before{
				background-color: <?php echo buddyboss_theme_get_option( 'alternate_text_color' ); ?>;
			}

			/* Buttons */
			.single-llms_quiz #llms-quiz-header:before {
				content: "<?php echo __( 'Quiz Progress', 'buddyboss-theme' ); ?>";
			}

		<?php

		$css = ob_get_contents();
		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		ob_end_clean();

		echo $css;
		if ( ! is_array( $custom_css ) ) {
			$custom_css = array();
		}
		$custom_css['css'] = $css;

		?>

		</style>
		<?php

		// save processed css.
		set_transient( 'buddyboss_theme_compressed_lifterLMS_custom_css', $custom_css );

	}

	/* Add Action */
	if ( class_exists( 'lifterLMS' ) ) {
		add_action( 'wp_head', 'boss_generate_option_lifterLMS_css', 99 );
	}
}
