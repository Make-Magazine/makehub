<?php
$lds_sizes = array(
    array(
        'name'      =>  'course_icon',
        'width'     =>  150,
        'height'    =>  150,
        'crop'      =>  true,
    )
);

foreach( $lds_sizes as $size ) {
    add_image_size( $size['name'], $size['width'], $size['height'], $size['crop'] );
}

function lds_appearance_settings() {

	if( !current_user_can( 'manage_options' ) )
		wp_die( __('You do not have sufficient permissions to access this page.') );

	// Listen for license activation
	lds_skins_activate_license();

	$active_tab = ( isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'lds_visuals' ); ?>

    <div class="wrap">
        <div id="icon-tools" class="icon32"></div>

			<form method="post" action="options.php">

				<?php
				$current 	= ( isset( $_GET['ldvcpage'] ) ? $_GET['ldvcpage'] : 'overview' );
				$tabs 		= apply_filters( 'ldvc_settings_tabs', array(
					'overview'	=>	__( 'Overview', 'lds_skins' ),
					'themes'	=>	__( 'Themes &amp; Templates', 'lds_skins' ),
					'fonts'		=>	__( 'Fonts', 'lds_skins' ),
					'colors'	=>	__( 'Colors', 'lds_skins' ),
					'shortcodes'=> __( 'Shortcodes', 'lds_skins' ),
					'advanced'	=>	__( 'Advanced', 'lds_skins' )
				) );
				do_settings_sections( 'lds_customizer' );
				settings_fields('lds_customizer');

				$settings = array(
					'license'		=>	get_option( 'lds_skins_license_key' ),
					'status'		=>	get_option( 'lds_skins_license_status' ),
				); ?>

				<ul class="lds-settings-tabs">
					<?php
					foreach( $tabs as $tab => $name ):
						$class = ( $tab == $current ? ' nav-tab-active' : '' ); ?>
						<li class="<?php echo esc_attr( $class ); ?>"><a class="lds-settings-tab" href="<?php echo admin_url() . 'admin.php?page=learndash-appearance&ldvcpage=' . esc_attr( $tab ); ?>"><?php echo esc_html( $name ); ?></a></li>
					<?php endforeach; ?>
				</ul>

	            <?php
				if( isset( $_GET[ 'lds_activate_response' ] ) ): ?>
	                <div class="lds-status-message">
	                    <pre>
	                        <?php lds_check_activation_response(); ?>
	                    </pre>
	                </div>
	            <?php
				endif; ?>

				<div class="lds-section <?php if( 'lds-' . $current == 'lds-overview' ) echo 'active'; ?>" id="lds-overview">

					<h2><?php esc_html_e( 'Overview', 'lds_skins' ); ?></h2>

					<table class="form-table">
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('License Key','lds_skins'); ?>
							</th>
							<td>
								<input id="lds_skins_license_key" name="lds_skins_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $settings[ 'license' ] ); ?>" />
								<label class="description" for="lds_skins_license_key"><?php _e('Enter your license key','psp_projects'); ?></label>
							</td>
						</tr>
					</table>

					<table class="form-table">
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License','lds_skins'); ?>
							</th>
							<td>
								<?php if( $settings[ 'status' ] !== false && $settings[ 'status' ] == 'valid' ) { ?>

									<span style="color:green;" class="lds-activation-notice"><?php _e('Active','lds_skins'); ?></span>

									<?php wp_nonce_field( 'lds_nonce', 'lds_nonce' ); ?>

									<input type="submit" class="button-secondary" name="lds_license_deactivate" value="<?php _e('Deactivate License','lds_skins'); ?>"/>

								<?php } else { ?>

									<span style="color:red;" class="lds-activation-notice"><?php _e('Inactive','lds_skins'); ?></span>

									<?php wp_nonce_field( 'lds_nonce', 'lds_nonce' ); ?>

									<input type="submit" class="button-secondary" name="lds_license_activate" value="<?php _e('Activate License','lds_skins'); ?>"/>

									<a class="button" href="<?php echo admin_url(); ?>admin.php?page=learndash-appearance&tab=lds_license&settings-updated=true&lds_activate_response=true">Check Activation Message</a>

								<?php } ?>
							</td>
						</tr>
					</table>
				</div> <!--/#lds-overview-->

				<div class="lds-section <?php if( 'lds-' . $current == 'lds-shortcodes' ) echo 'active'; ?>" id="lds-shortcodes">

					<h1><?php esc_html_e( 'Shortcodes', 'lds_skins' ); ?></h1>

					<style type="text/css">
						ul.list {
							list-style: disc;
							padding-left: 15px;
						}
					</style>

					<h3>Course Listings</h3>

					<p><strong>[lds_course_list]</strong></p>

					<p>The course list shortcode will output a grid of your courses with a featured image or icon. There are the following arguments:</p>

					<ul class="list">
					    <li>style – This can be “icon” or “banner” i.e. [lds_course_list style="icon"] or [lds_course_list style="banner"]</li>
					    <li>cols – This can be 2 or 3 and will change how many columns the courses are in, i.e. [lds_course_list cols="2"]</li>
					</ul>

					<p><strong>[lds_expanded_course_list]</strong></p>

					<p>This will output your course listing in the “Expanded Style” template, consider switching all your content listings to this style by selecting “Expanded Style” in the <a href="<?php echo esc_url( admin_url() . '?page=learndash-appearance&ldvcpage=themes' ); ?>">template option of LearnDash</a>.</p>

					<h3>Extra</h3>

					<p><strong>[lds_progress]</strong></p>

					<p>This will output an enhanced course progress bar including an output of what percentage complete the logged in user is and how many steps are remaining.</p>

					<p><strong>[lds_login]</strong></p>

					<p>Allows you to embed a stylized login form on your page, accepts the following arguments:</p>

					<ul class="list">
					    <li>redirect – What web address should the user be redirected to upon successful login?</li>
					    <li>username – What label should be used in place of “username”</li>
					    <li>password – What label should be used in place of “password”</li>
					    <li>button – What label should be used in place of “login”</li>
					    <li>remember_me – What language should be used in place of “Remember Me”</li>
					    <li>lost_password – What wording should be used in place of “Lost Password”</li>
					</ul>

				</div> <!--/#lds-overview-->

				<div class="lds-section <?php if( 'lds-' . $current == 'lds-themes' ) echo 'active'; ?>" id="lds-themes">

					<?php
					$theme_settings = array(
						'lds_skin'			=>	get_option('lds_skin'),
						'lds_animation'		=>	get_option('lds_animation'),
						'lds_icon_style'	=>	get_option('lds_icon_style'),
						'lds_listing_style'	=>	get_option('lds_listing_style'),
						'lds_page_template'	=>	get_option('lds_page_template')
					);

					$listing_styles = apply_filters( 'lds_listing_styles', array(
						'default'		=>	__( 'LearnDash Default', 'lds_skins' ),
						'enhanced'		=>	__( 'LearnDash Enhanced', 'lds_skins' ),
						'expanded'		=>	__( 'Expanded Style', 'lds_skins' ),
						'grid-banner'	=>	__( 'Grid with banners', 'lds_skins' ),
						// 'grid-icon'		=>	__( 'Grid with icons', 'lds_skins' )
					) );

					$themes = apply_filters( 'lds_themes', array(
						'modern'	=>	__( 'Modern', 'lds_skins' ),
						'classic'	=>	__( 'Classic', 'lds_skins' ),
						'playful'	=>	__( 'Playful', 'lds_skins' ),
						'upscale'	=>	__( 'Upscale', 'lds_skins' ),
                        'rustic'    =>  __( 'Rustic', 'lds_skins' ),
					) );

					$icon_styles = apply_filters( 'lds_icon_styles', array(
							'default'	=>	__( 'Default', 'lds_skins' ),
							'modern'	=>	__( 'Modern', 'lds_skins' ),
							'minimal'	=>	__( 'Minimal', 'lds_skins' ),
							'chunky'	=>	__( 'Chunky', 'lds_skins' ),
							'circles'	=>	__( 'Circles', 'lds_skins' ),
							'playful'	=>	__( 'Playful', 'lds_skins' ),
					) ); ?>

		            <h2><?php _e('Themes','lds_skins'); ?></h2>

					<table class="form-table">
						<?php /*
						<tr>
							<th><label for="lds_page_template"><?php esc_html_e( 'Content Template', 'lds_skins' ); ?></label></th>
							<td>
								<select name="lds_page_template" id="lds_page_template">
									<option value=""><?php esc_html_e( 'Default', 'lds_skins' ); ?></option>
									<option value="page.php"><?php esc_html_e( 'Page', 'lds_skins' ); ?></option>
									<?php
									$templates = get_page_templates();
									foreach( $templates as $template_name => $template_file ): ?>
										<option value="<?php esc_attr_e( $template_file ); ?>" <?php if( $template_file == $theme_settings['lds_page_template'] ) echo 'selected'; ?>><?php esc_html_e( $template_name ); ?></option>
									<?php endforeach; ?>
								</select>
								<p><label for="lds_page_template"><em><?php esc_html_e( 'Select a template from your theme to display LearnDash content on', 'lds_skins' ); ?></em></label></p>
							</td>
						</tr>
						*/ ?>
						<tr>
							<th><label for="lds_listing_style"><?php esc_html_e( 'LearnDash Template', 'lds_skins' ); ?></label></th>
							<td>
								<select name="lds_listing_style" id="lds_listing_style">
									<?php if( isset($theme_settings['lds_listing_style']) && !empty($theme_settings['lds_listing_style'])): ?>
										<option value="<?php echo esc_attr($theme_settings['lds_listing_style']); ?>"><?php echo esc_html($listing_styles[$theme_settings['lds_listing_style']]); ?></option>
										<option value="" disabled>---</option>
									<?php
									endif;
									foreach( $listing_styles as $value => $label ): ?>
										<option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
									<?php endforeach; ?>
								</select>
								<p><label for="lds_listing_style"><em><?php esc_html_e( 'LearnDash Enhanced adds content type, duration and short description to listings.', 'lds_skins' ); ?></em></label></p>
							</td>
						</tr>
						<tr id="lds_show_leaderboard_settings" class="lds-hide">
							<th>
								<label for="lds_show_leaderboard"><?php esc_html_e( 'Show Course Stats', 'lds_skins' ); ?></label>
							</th>
							<td>
								<select name="lds_show_leaderboard" id="lds_show_leaderboard">
									<option value="yes" <?php if(get_option('lds_show_leaderboard') == 'yes' ) { echo 'selected'; } ?>><?php esc_html_e( 'Yes', 'lds_skins' ); ?></option>
									<option value="no" <?php if(get_option('lds_show_leaderboard') == 'no' ) { echo 'selected'; } ?>><?php esc_html_e( 'No', 'lds_skins' ); ?></option>
								</select>
							</td>
						</tr>
                        <tr class="lds-hide" id="lds-grid-columns-settings">
                            <th>
                                <label for="lds_grid_columns"><?php esc_html_e('Number of Columns', 'lds_skins' ); ?></label>
                            </th>
                            <td>
                                <select name="lds_grid_columns" id="lds_grid_columns">
                                    <?php
                                    $cols = get_option( 'lds_grid_columns', '4' );
                                    $opts = array(
                                        '2',
                                        '3',
                                        '4'
                                    );
                                    foreach( $opts as $opt ) {
                                        $checked = ( $opt == $cols ? ' selected ' : '' );
                                        echo '<option value="' . $opt . '" ' . $checked . '>' . $opt . '</option>';
                                    } ?>
                                </select>
                            </td>
	                    <tr>
	                        <th><label for="lds_skin"><?php _e('Theme','lds_skins'); ?></label></th>
	                        <td><select name="lds_skin" id="learndash-skin">
									<?php if( isset( $theme_settings[ 'lds_skin' ] ) && !empty( $theme_settings[ 'lds_skin' ] ) ): ?>
										<option value="<?php echo esc_attr( $theme_settings[ 'lds_skin' ] ); ?>"><?php echo esc_attr( $theme_settings[ 'lds_skin' ] ); ?></option>
										<option value="---" disabled>---</option>
		                                <option value="default"><?php esc_html_e('Default','lds_skins'); ?></option>
									<?php else: ?>
		                                <option value="default"><?php esc_html_e('Default','lds_skins'); ?></option>
										<option value="---" disabled>---</option>
									<?php endif; ?>
	                                <?php foreach( $themes as $theme => $title ): ?>
										<option value="<?php echo esc_attr( $theme ); ?>"><?php echo esc_html( $title ); ?></option>
									<?php endforeach; ?>
									<?php do_action('lds_skin_options'); ?>
	                            </select>
								<p><label for="lds_skin"><em><?php esc_html_e( 'Note: Changing skins will reset color selections to theme default.', 'lds_skins'); ?></em></label>
							</td>
	                    </tr>
						<tr>
							<th><label for="lds_icon_style"><?php esc_html_e( 'Icon Style', 'lds_skins' ); ?></label></th>
							<td>
								<select id="lds_icon_style" name="lds_icon_style">
									<?php if( !empty( $theme_settings['lds_icon_style']) ): ?>
										<option value="<?php echo esc_attr( $theme_settings['lds_icon_style'] ); ?>"><?php echo esc_html( $theme_settings['lds_icon_style']); ?></option>
									<?php endif; ?>
									<option value="---" disabled>---</option>
									<?php foreach( $icon_styles as $style => $title ): ?>
										<option value="<?php echo esc_attr( $style ); ?>"><?php echo esc_html( $title ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th><label for="lds_animation"><?php _e('Animation','lds_skins'); ?></label>
							</th>
							<td><input type="checkbox" name="lds_animation" value="yes" <?php if($theme_settings['lds_animation'] == 'yes') { echo 'checked'; } ?>>							<em><?php _e('Fade in course listing and user profile','lds_skins'); ?></em></td>
						</tr>
					</table>

					<div class="lds-preview">

						<div id="lds-grid-banner" class="lds-layout-preview">

							<p><strong><?php _e('Grid With Banners', 'lds_skins' ); ?></strong></p>
							<img src="<?php echo plugin_dir_url(__FILE__); ?>/assets/img/previews/grid-banner.jpg" alt="Grid with banners">

						</div>

						<div id="lds-expanded" class="lds-layout-preview">

							<p><strong><?php _e('Expanded Template', 'lds_skins' ); ?></strong></p>
							<img src="<?php echo plugin_dir_url(__FILE__); ?>/assets/img/previews/expanded.jpg" alt="An expand course listing style">

						</div>

						<p><em><?php _e('Preview of this theme\'s default color scheme','lds_skins'); ?></em></p>

						<div id="lds-modern" class="lds-theme-preview">

							<p><strong><?php _e('Modern','lds_skins'); ?></strong> <?php _e('Flat, minimal and clean with rich blues, grays and greens.','lds_skins'); ?></p>

							<img src="<?php echo plugin_dir_url(__FILE__); ?>/assets/img/previews/modern.jpg" alt="A preview of the modern theme">

						</div> <!--/.lds-theme-preview-->

						<div id="lds-classic" class="lds-theme-preview">

							<p><strong><?php _e('Classic','lds_skins'); ?></strong> <?php _e('Soft grays, strong geometric lines add up to a clean, professional appearance.','lds_skins'); ?></p>

							<img src="<?php echo plugin_dir_url(__FILE__); ?>/assets/img/previews/classic.jpg" alt="A preview of the classic theme">

						</div> <!--/.lds-theme-preview-->

						<div id="lds-rustic" class="lds-theme-preview">

							<p><strong><?php _e('Rustic','lds_skins'); ?></strong> <?php _e('Rounded corners and earth tones paired with a dash of depth.','lds_skins'); ?></p>

							<img src="<?php echo plugin_dir_url(__FILE__); ?>/assets/img/previews/rustic.jpg" alt="A preview of the rustic theme">

						</div> <!--/.lds-theme-preview-->

						<div id="lds-playful" class="lds-theme-preview">

							<p><strong><?php _e('Playful','lds_skins'); ?></strong> <?php _e('Fun and entertaining, with bright colors, handwriting fonts and a dash of style.')?></p>
							<img src="<?php echo plugin_dir_url(__FILE__); ?>/assets/img/previews/playful.jpg" alt="A preview of the playful theme">

						</div> <!--/.lds-theme-preview-->

						<div id="lds-default" class="lds-theme-preview">

							<p><strong><?php _e('Default','lds_skins'); ?></strong> <?php _e('The default LearnDash theme. Customize the colors but keep the original look and feel.'); ?></p>

							<img src="<?php echo plugin_dir_url(__FILE__); ?>/assets/img/previews/default.jpg" alt="A preview of the default theme">

						</div> <!--/.lds-theme-preview-->

						<div id="lds-upscale" class="lds-theme-preview">

							<p><strong><?php _e('Upscale','lds_skins'); ?></strong> <?php _e('A luxurious mix of rich leather and gold, perfect for any premium course.'); ?></p>
							<img src="<?php echo plugin_dir_url(__FILE__); ?>/assets/img/previews/upscale.jpg" alt="A preview of the upscale theme">

						</div> <!--/.lds-theme-preview-->

					</div>

				</div> <!--/#lds-themes-->

				<div class="lds-section <?php if( 'lds-' . $current == 'lds-fonts' ) echo 'active'; ?>" id="lds-fonts">

					<h2><?php _e('Font Sizes','lds_skins'); ?></h2>

					<?php
					$fields = array(
						'lds_table_heading_font_size'		=>	__( 'Table Heading', 'lds_skins' ),
						'lds_table_row_font_size'			=>	__( 'Table Row', 'lds_skins' ),
						'lds_table_sub_row_font_size'		=>	__( 'Table Sub Row', 'lds_skins' ),
						'lds_widget_heading_font_size'		=>	__( 'Widget Heading', 'lds_skins' ),
						'lds_widget_text_font_size'			=>	__( 'Widget Text', 'lds_skins' )
					);

					$sizes = apply_filters( 'lds_font_sizes', array(
						'---'	=>	'---',
						''		=>	'Default',
						'8'		=>	'8px',
						'9'		=>	'9px',
						'10'	=>	'10px',
						'11'	=>	'11px',
						'12'	=>	'12px',
						'14'	=>	'14px',
						'16'	=>	'16px',
						'18'	=>	'18px',
						'20'	=>	'20px',
						'24'	=>	'24px',
						'28'	=>	'28px',
						'32'	=>	'32px',
						'38'	=>	'38px',
						'42'	=>	'42px',
						'48'	=>	'48px',
						'64'	=>	'64px',
						'72'	=>	'72px',
						'92'	=>	'92px',
					) ); ?>

					<fieldset class="lds-group">
						<table class="form-table">
							<?php foreach( $fields as $field_name => $label ) { ?>
								<tr>
									<th><label for="<?php echo $field_name; ?>"><?php echo $label; ?></label></th>
									<td>
										<select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>">
											<?php if( get_option( $field_name ) ) { ?>
												<option value="<?php echo get_option( $field_name ); ?>">
													<?php echo get_option( $field_name ); ?>

													<?php if ( get_option( $field_name ) != 'default' ) { echo 'px'; } ?>
												</option>
											<?php } else { ?>
												<option value="default"><?php _e( 'Default', 'lds_skins' ); ?></option>
											<?php } ?>
											<?php foreach( $sizes as $size => $label ): ?>
												<option value="<?php echo esc_attr( $size ); ?>"><?php echo esc_html( $label ); ?></option>
											<?php endforeach; ?>
										</select>
									</td>
								</tr>
							<?php } ?>
						</table>
					</fieldset>

				</div> <!--/#lds-fonts-->

				<div class="lds-section <?php if( 'lds-' . $current == 'lds-colors' ) echo 'active'; ?>" id="lds-colors">

					<h2><?php esc_html_e('Customize Colors','lds_skins'); ?></h2>

					<p><?php esc_html_e('Customize major colors of the selected Learn Dash LMS theme','lds_skins'); ?></p>

                    <p><a href="#" class="lds-clear-colors button">Clear All Colors</a></p>

					<fieldset class="lds-group">
						<h3 class="lds-ntb"><?php _e('Course, Lesson and Quiz Listings','lds_skins'); ?></h3>

						<p><?php _e('Headings that appear in course and lesson listing tables','lds_skins'); ?></p>

						<table class="form-table color-picker-table">
							<tr>
								<th><label for="lds_heading_bg"><?php _e('Table heading background','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_heading_bg" name="lds_heading_bg" value="<?php echo get_option('lds_heading_bg');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_heading_txt"><?php _e('Table & grid heading text','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_heading_txt" name="lds_heading_txt" value="<?php echo get_option('lds_heading_txt');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_bg"><?php _e('Table row background','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_row_bg" name="lds_row_bg" value="<?php echo get_option('lds_row_bg');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_bg"><?php _e('Alt. table row background','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_row_bg_alt" name="lds_row_bg_alt" value="<?php echo get_option('lds_row_bg_alt');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_bg"><?php _e('Table row text','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_row_txt" name="lds_row_txt" value="<?php echo get_option('lds_row_txt');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_bg"><?php _e('Sub table row background','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_sub_row_bg" name="lds_sub_row_bg" value="<?php echo get_option('lds_sub_row_bg');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_bg"><?php _e('Sub table row alt background','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_sub_row_bg_alt" name="lds_sub_row_bg_alt" value="<?php echo get_option('lds_sub_row_bg_alt');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_bg"><?php _e('Table sub row text','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_sub_row_txt" name="lds_sub_row_txt" value="<?php echo get_option('lds_sub_row_txt');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_quiz_bg"><?php _e('Quiz Background Color','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_quiz_bg" name="lds_quiz_bg" id="lds_quiz_bg" value="<?php echo get_option('lds_quiz_bg');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_quiz_txt"><?php _e('Quiz Text Color','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_quiz_txt" name="lds_quiz_txt" id="lds_quiz_txt" value="<?php echo get_option('lds_quiz_txt');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_quiz_border_color"><?php _e('Quiz Border Color','lds_skins'); ?></label></th>
								<td>
									<select name="lds_quiz_border_color" class="lds_quiz_border_color" name="lds_quiz_border_color" id="lds_quiz_border_color">
										<option value=""><?php esc_html_e('Default','lds_skins'); ?></option>
										<option value="light" <?php if(get_option('lds_quiz_border_color') == 'light') echo 'selected'; ?>><?php esc_html_e('Light','lds_skins'); ?></option>
										<option value="dark" <?php if(get_option('lds_quiz_border_color') == 'dark') echo 'selected'; ?>><?php esc_html_e('Dark','lds_skins'); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="lds_quiz_correct_bg"><?php _e('Quiz Correct Answer Background','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_quiz_txt" name="lds_quiz_correct_bg" id="lds_quiz_correct_bg" value="<?php echo get_option('lds_quiz_correct_bg');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_quiz_correct_txt"><?php _e('Quiz Correct Answer Text','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_quiz_txt" name="lds_quiz_correct_txt" id="lds_quiz_correct_txt" value="<?php echo get_option('lds_quiz_correct_txt');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_quiz_incorrect_bg"><?php _e('Quiz Incorrect Answer Background','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_quiz_txt" name="lds_quiz_incorrect_bg" id="lds_quiz_incorrect_bg" value="<?php echo get_option('lds_quiz_incorrect_bg');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_quiz_incorrect_txt"><?php _e('Quiz Incorrect Answer Text','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_quiz_txt" name="lds_quiz_incorrect_txt" id="lds_quiz_incorrect_txt" value="<?php echo get_option('lds_quiz_incorrect_txt');?>" />
								</td>
							</tr>
						</table>

					</fieldset>

					<fieldset class="lds-group">

						<h3><?php _e('Visual Elements','lds_skins'); ?></h3>

						<p><?php _e('Core visual elements like the progress bar, completed colors, icons, etc...','lds_skins'); ?></p>


						<table class="form-table color-picker-table">
							<tr>
								<th><label for="lds_progress"><?php _e('Progress bar','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_progress" name="lds_progress" value="<?php echo get_option('lds_progress');?>" />
								</td>
							</tr>
							<?php /*
							<tr>
								<th><label for="lds_complete"><?php _e('Completed','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_complete" name="lds_complete" value="<?php echo get_option('lds_complete');?>" />
								</td>
							</tr>
							*/ ?>
							<tr>
								<th><label for="lds_checkbox_incomplete"><?php _e('Checkbox incomplete','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_checkbox_incomplete" name="lds_checkbox_incomplete" value="<?php echo get_option('lds_checkbox_incomplete');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_checkbox_complete"><?php _e('Checkbox complete','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_checkbox_complete" name="lds_checkbox_complete" value="<?php echo get_option('lds_checkbox_complete');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_arrow_incomplete"><?php _e('Arrow incomplete','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_arrow_incomplete" name="lds_arrow_incomplete" value="<?php echo get_option('lds_arrow_incomplete');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_arrow_complete"><?php _e('Arrow complete','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_arrow_complete" name="lds_arrow_complete" value="<?php echo get_option('lds_arrow_complete');?>" />
								</td>
							</tr>
						</table>

					</fieldset>

					<fieldset class="lds-group">
						<h3><?php _e('Buttons','lds_skins'); ?></h3>

						<p><?php _e('Complete, course, apply buttons','lds_skins'); ?></p>

						<table class="form-table color-picker-table">
							<tr>
								<th><label for="lds_button_border_radius"><?php _e('Rounded Corners','lds_skins'); ?></label></th>
								<td>
									<select name="lds_button_border_radius" id="lds_button_border_radius">
										<?php
										if(get_option('lds_button_border_radius')): ?>
											<option value="<?php echo get_option('lds_button_border_radius'); ?>"><?php echo get_option('lds_button_border_radius'); ?></option>
										<?php endif; ?>
										<option value="" disabled>---</option>
										<option value="0">0px - Square</option>
										<option value="2">2</option>
										<option value="4">4</option>
										<option value="6">6</option>
										<option value="8">8</option>
										<option value="10">10</option>
										<option value="12">12</option>
										<option value="14">14</option>
										<option value="16">16</option>
										<option value="18">18</option>
										<option value="20">20</option>
									</select>
								</td>
							<tr>
								<th><label for="lds_button_bg"><?php _e('Standard button','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_button_bg" name="lds_button_bg" value="<?php echo get_option('lds_button_bg');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_button_txt"><?php _e('Standard button text','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_button_txt" name="lds_button_txt" value="<?php echo get_option('lds_button_txt');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_complete_button_bg"><?php _e('Complete button background','lds_skins'); ?></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_complete_button_bg" name="lds_complete_button_bg" value="<?php echo get_option('lds_complete_button_bg');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_complete_button_txt"><?php _e('Complete button text','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_complete_button_txt" name="lds_complete_button_txt" value="<?php echo get_option('lds_complete_button_txt');?>" />
								</td>
							</tr>
						</table>

					</fieldset>

					<fielset class="lds-group">
						<h3><?php _e('Widgets','lds_skins'); ?></h3>

						<p><?php _e('Course listing, progress widget, etc...','lds_skins'); ?></p>


						<table class="form-table color-picker-table">
							<tr>
								<th><label for="lds_widget_bg"><?php _e('Widget background','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_widget_bg" name="lds_widget_bg" value="<?php echo get_option('lds_widget_bg');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_widget_txt"><?php _e('Widget text','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_widget_txt" name="lds_widget_txt" value="<?php echo get_option('lds_widget_txt');?>" />
								</td>
							</tr>
							<th><label for="lds_links"><?php _e('Widget Links','lds_skins'); ?></label></th>
							<td>
								<input type="text" class="learndash-skin-color-picker lds_links" name="lds_links" value="<?php echo get_option('lds_links');?>" />
							</td>
							<tr>
								<th><label for="lds_widget_header_bg"><?php _e('Header background','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_widget_header_bg" name="lds_widget_header_bg" value="<?php echo get_option('lds_widget_header_bg');?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_widget_header_txt"><?php _e('Header text','lds_skins'); ?></label></th>
								<td>
									<input type="text" class="learndash-skin-color-picker lds_widget_header_txt" name="lds_widget_header_txt" value="<?php echo get_option('lds_widget_header_txt');?>" />
								</td>
							</tr>
						</table>
					</fielset>

				</div> <!--/#lds-colors-->

				<div class="lds-section <?php if( 'lds-' . $current == 'lds-advanced' ) echo 'active'; ?>" id="lds-advanced">

					<fieldset class="lds-group">
						<h3><?php _e('Custom CSS','lds_skins'); ?></h3>
						<p><?php _e('Customize LearnDash even further with CSS (for experts only)','lds_skins'); ?></p>

						<table class="form-table">
							<tr>
								<th><label for="lds_open_css"><?php _e('Custom CSS','lds_skins'); ?></label></th>
								<td>
									<textarea name="lds_open_css" id="lds_open_css" rows="10" cols="50"><?php echo get_option('lds_open_css'); ?></textarea>
								</td>
							</tr>
						</table>
					</fieldset>

					<fieldset class="lds-group">

						<h3><?php _e('Advanced Settings','lds_skins'); ?></h3>

						<p><?php _e('Adjust default CSS selectors and enqueue method','lds_skins'); ?></p>

						<?php
                        $lds_enqueue_method = get_option( 'ldvc_add_method' , 'inline' );
                        $lds_fontawesome_ver = get_option( 'lds_fontawesome_ver', '5' ); ?>

						<table class="form-table">
                            <tr>
                                <th><label for="lds_fontawesome_ver"><?php esc_html_e( 'FontAwesome Version', 'lds_skin' ); ?></label></th>
                                <td>
                                    <select id="lds_fontawesome_ver" name="lds_fontawesome_ver">
                                        <option value="5" <?php if( $lds_fontawesome_ver == 5 ) echo 'selected'; ?>>5</option>
                                        <option value="4" <?php if( $lds_fontawesome_ver == 4 ) echo 'selected'; ?>>4</option>
                                    </select>
                                </td>
							<tr>
								<th><label for="ldvc_add_method"><?php _e('Enqueue Method', 'lds_skins' ); ?></label></th>
								<td><select id="ldvc_add_method" name="ldvc_add_method">
										<option value="generated" <?php if($lds_enqueue_method == 'generated' ) { echo 'selected'; } ?>>Generated file</option>
										<option value="dynamic" <?php if($lds_enqueue_method == 'dynamic' ) { echo 'selected'; } ?>>Dynamic file</option>
										<option value="inline" <?php if($lds_enqueue_method == 'inline' ) { echo 'selected'; } ?>>Inline head</option>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="lds_dequeue_styles"><?php _e('Dequeue Styles','lds_skins'); ?></label></th>
								<td>
									<input type="text" name="lds_dequeue_styles" value="<?php echo get_option( 'lds_dequeue_styles' );?>" />
									<label class="description"><?php _e( 'Enter stylesheet handles separated by a comma', 'lds_skins' ); ?></label>
								</td>
							</tr>
							<tr>
								<th><label for="lds_widget_wrapper"><?php _e('Widget Wrapper','lds_skins'); ?></label></th>
								<td>
									<input type="text" name="lds_widget_wrapper" value="<?php echo get_option( 'lds_widget_wrapper' );?>" />
								</td>
							</tr>
							<tr>
								<th><label for="lds_widget_title"><?php _e('Widget Title','lds_skins'); ?></label></th>
								<td>
									<input type="text" name="lds_widget_title" value="<?php echo get_option( 'lds_widget_title' , '.widget-title' );?>" />
								</td>
							</tr>
						</table>

				</fieldset>

			</div> <!--/#lds-advanced-->

		    <p class="submit"><?php submit_button(); ?></p>

        </form>

	</div>

    <?php
}

add_action( 'admin_menu', 'ldvc_nonexistant_submenu_page' );
function ldvc_nonexistant_submenu_page() {
    add_submenu_page( 'learndash-lms-non-existant',__('LearnDash Visual Customizer','lds_skins'), __('LearnDash Visual Customizer','lds_skins'), 'manage_options', 'learndash-visual-customizer', 'lds_appearance_settings' );
}

/*
 * Add tab to the learndash settings
 *
 */
add_filter("learndash_admin_tabs", "lds_customizer_tabs");
function lds_customizer_tabs($admin_tabs) {

	$admin_tabs["apperance"] = array(
		"link"  		=>      'admin.php?page=learndash-appearance',
		"name" 			=>      __( "Appearance", "lds_skins" ),
		"id"    		=>      "admin_page_learndash-appearance",
		"menu_link"     =>      "edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses",
	);

   	return $admin_tabs;

}


add_filter("learndash_admin_tabs_on_page", "learndash_customizer_learndash_admin_tabs_on_page", 3, 3);
function learndash_customizer_learndash_admin_tabs_on_page($admin_tabs_on_page, $admin_tabs, $current_page_id) {

     if( isset($admin_tabs_on_page) && !empty($admin_tabs_on_page["admin_page_learndash-appearance"]) ) {
         $admin_tabs_on_page["admin_page_learndash-appearance"] = array_merge($admin_tabs_on_page["sfwd-courses_page_sfwd-lms_sfwd_lms_post_type_sfwd-courses"], (array) $admin_tabs_on_page["admin_page_learndash-appearance"]);
     }

	foreach ($admin_tabs as $key => $value) {
		if($value["id"] == $current_page_id && $value["menu_link"] == "edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses")
		{
			$admin_tabs_on_page[$current_page_id][] = "apperance";
			return $admin_tabs_on_page;
		}
	}

	return $admin_tabs_on_page;
}

add_action('admin_init', 'lds_register_settings');
function lds_register_settings() {

	$lds_settings = array(
		'lds_skins_license_key',
		'lds_skins_sanitize_license',
		'lds_animation',
		'lds_skin',
		'lds_icon_style',
		'lds_heading_bg',
		'lds_heading_txt',
		'lds_row_bg',
		'lds_row_bg_alt',
		'lds_sub_row_bg',
		'lds_sub_row_bg_alt',
		'lds_sub_row_txt',
		'lds_row_txt',
		'lds_button_bg',
		'lds_button_border_radius',
		'lds_button_txt',
		'lds_complete_button_bg',
		'lds_complete_button_txt',
		'lds_progress',
		'lds_links',
		'lds_checkbox_incomplete',
		'lds_checkbox_complete',
		'lds_arrow_incomplete',
		'lds_arrow_complete',
		'lds_complete',
		'lds_widget_bg',
		'lds_widget_header_bg',
		'lds_widget_header_txt',
		'lds_widget_txt',
		'lds_open_css',
		'lds_widget_wrapper',
		'lds_widget_title',
		'ldvc_add_method',
		'lds_table_heading_font_size',
		'lds_table_row_font_size',
		'lds_table_sub_row_font_size',
		'lds_widget_heading_font_size',
		'lds_widget_text_font_size',
		'lds_dequeue_styles',
		'lds_listing_style',
		'lds_quiz_bg',
		'lds_quiz_txt',
		'lds_quiz_border_color',
		'lds_quiz_correct_txt',
		'lds_quiz_correct_bg',
        'lds_quiz_incorrect_bg',
		'lds_show_leaderboard',
		'lds_page_template',
		'lds_fontawesome_ver',
        'lds_grid_columns'
	);

	foreach($lds_settings as $setting) {
		register_setting('lds_customizer', $setting);
	}

}


function lds_options_saved() {
	if( get_option( 'ldvc_add_method' ) == 'generated' ) {
		lds_build_stylesheet();
	}
}


function lds_sanitize_license( $new ) {

	$old = get_option( 'lds_skins_license_key' );

	if( $old && $old != $new ) {

		delete_option( 'lds_skins_license_status' ); // new license has been entered, so must reactivate

	}

	return $new;
}

function lds_skins_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['lds_license_activate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'lds_nonce', 'lds_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'lds_skins_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( EDD_LEARNDASH_SKINS ), // the name of our product in EDD
		    'url'   => home_url()
        );

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, LDS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "active" or "inactive"

		update_option( 'lds_skins_license_status', $license_data->license );

	}

}
add_action('admin_init', 'lds_skins_activate_license',1);

function lds_check_activation_response() {

    // retrieve the license from the database
    $license = trim( get_option( 'lds_skins_license_key' ) );


    // data to send in our API request
    $api_params = array(
        'edd_action'=> 'activate_license',
        'license' 	=> $license,
        'item_name' => urlencode( EDD_LEARNDASH_SKINS ), // the name of our product in EDD
        'url'   => home_url()
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, LDS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

	var_dump($response);

}


/***********************************************
* Illustrates how to deactivate a license key.
* This will descrease the site count
***********************************************/

function lds_skins_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['lds_license_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'lds_nonce', 'lds_nonce' ) )
			return; // get out if we didn't click the deactivate button

		// retrieve the license from the database
		$license = trim( get_option( 'lds_skins_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( EDD_LEARNDASH_SKINS ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, LDS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'lds_skins_license_status' );

	}
}
add_action('admin_init', 'lds_skins_deactivate_license',1);


/************************************
* this illustrates how to check if
* a license key is still valid
* the updater does this for you,
* so this is only needed if you
* want to do something custom
*************************************/

function lds_skins_check_license() {

	global $wp_version;

	$license = trim( get_option( 'lds_skins_license_key' ) );

	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => urlencode( EDD_LEARNDASH_SKINS )
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, LDS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
		// this license is still valid
	} else {
		echo 'invalid'; exit;
		// this license is no longer valid
	}
}

if( !function_exists('ldvc_use_iconpicker') ) {
     function ldvc_use_iconpicker() {

         if( get_option('lds_fontawesome_ver' ) == '4' && function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
             return false;
         }

         if( get_option( 'lds_disable_fa_picker' ) == 'yes' ) {
             return false;
         }

         return true;

     }
}
