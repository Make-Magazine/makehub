<?php
/**
 * Settings Needed
 *
 * License key
 *
 * Message indicator Location
 * Message indocator position
 * Message listing page (dropdown) -- set to automatically created page to start
 * E-mail notification message
 *
 */

 function ldms_register_settings_page() {

    add_submenu_page( 'edit.php?post_type=sfwd-courses',__('LearnDash Private Sessions','ldmessenger'), __('LearnDash Private Sessions','ldmessenger'), 'manage_options', 'admin.php?page=learndash-private-sessions', 'ldms_settings_page' );
    add_submenu_page( 'learndash-lms-non-existant',__('LearnDash Private Sessions','ldmessenger'), __('LearnDash Private Sessions','ldmessenger'), 'manage_options', 'learndash-private-sessions', 'ldms_settings_page' );

 }
add_action( 'admin_menu', 'ldms_register_settings_page', 2500 );

add_filter( 'learndash_admin_tabs', 'ldms_settings_tab' );
function ldms_settings_tab( $admin_tabs ) {

    $admin_tabs['private_sessions'] = array(
        'link'  =>  'admin.php?page=learndash-private-sessions',
        'name'  =>  __( 'Private Sessions', 'ldmessenger' ),
        'id'    =>  'admin_page_learndash-private-sessions',
        'menu_link' =>  'edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses'
    );

    return $admin_tabs;

}

add_filter( 'learndash_admin_tabs_on_page', 'learndash_private_session_admin_tabs_on_page', 3, 3 );
function learndash_private_session_admin_tabs_on_page( $admin_tabs_on_page, $admin_tabs, $current_page_id ) {

    $admin_tabs_on_page['admin_page_learndash-private-sessions'] = array_merge($admin_tabs_on_page['sfwd-courses_page_sfwd-lms_sfwd_lms_post_type_sfwd-courses'], (array) $admin_tabs_on_page['admin_page_learndash-private-sessions']);

	foreach ($admin_tabs as $key => $value) {
		if($value['id'] == $current_page_id && $value['menu_link'] == 'edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses')
		{
			$admin_tabs_on_page[$current_page_id][] = 'private_sessions';
			return $admin_tabs_on_page;
		}
	}

	return $admin_tabs_on_page;


}

function ldms_settings_page() { ?>

    <div class="wrap">

        <h1>Private Sessions</h1>

        <?php settings_fields( 'ldms_options' ); ?>

        <form method="post" action="options.php">

            <?php do_settings_sections( 'ldms_options' ); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="ldms_indicator_location"><?php esc_html_e( 'Indicator Location' ); ?></label></th>
                    <td>
                        <select name="ldms_indicator_location" id="ldms_indicator_location">
                            <?php
                            $current = get_option('ldms_indicator_location' );
                            $options = array(
                                'top'   =>  'Attached to Top',
                                'bottom'    =>  'Attached to Bottom',
                                'right'     =>  'Attached to the right of the screen'
                            );
                            foreach( $options as $option => $label ): ?>
                                <option value="<?php echo esc_attr($option); ?>" <?php if( $current == $option ) echo 'checked'; ?>><?php echo esc_html($label); ?></option>
                            <?php
                            endforeach;  ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="ldms_indicator_position"><?php esc_html_e( 'How far from the edge do you want the tab to appear?', 'ldmessenger' ); ?></label>
                    </th>
                    <td>
                        <input type="text" value="<?php echo esc_attr(get_option('ldms_indicator_position')); ?>" name="ldms_indicator_position" id="ldms_indicator_position">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="ldma_sessions_page"><?php esc_html_e( 'Private Sessions Page' ); ?></label></th>
                    <td>
                        <select name="ldma_sessions_page" id="ldma_sessions_page">
                            <?php
                            $current = get_option('ldma_sessions_page' );
                            $args   = array(
                                'post_type'         =>  'page',
                                'posts_per_page'    =>  -1,
                            );
                            $pages = new WP_Query($args);
                            while( $pages->have_posts() ): $pages->the_post(); ?>
                                <option value="<?php echo esc_attr(get_the_ID()); ?>" <?php if( $current == get_the_ID() ) echo 'checked'; ?>><?php echo esc_html(get_the_title(get_the_ID())); ?></option>
                            <?php
                            endwhile; ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="ldms_email_message"><?php esc_html_e( 'E-mail Message' ); ?></label></th>
                    <td>
                        <?php
                        $content = ( get_option( 'ldms_email_message' ) ? ldms_email_message() : ldms_get_default_email_message() );
                        wp_editor( $content, 'ldms_email_message' ); ?>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>

        </form>


    </div>

    <?php
}

//add_action( 'admin_init', 'ldms_register_settings' );
function ldms_register_settings() {

    $settings = array(
        'ldms_indicator_location',
        'ldms_indicator_position',
        'ldma_sessions_page',
        'ldms_email_message'
    );

    foreach( $settings as $setting ) register_setting( 'ldms_options', $setting );

}
