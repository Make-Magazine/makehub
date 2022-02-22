<?php
/*
 * See a variety of widgets connected to your profile
 */

function profile_tab_dashboard_info_name() {
    global $bp;
    $user_id = bp_displayed_user_id();

    //Is this the profile for the logged in user?
    if (current_user_can('administrator') || $user_id != 0 && wp_get_current_user()->ID == $user_id) {
        bp_core_new_nav_item(array(
            'name' => 'Dashboard',
            'slug' => 'dashboard',
            'screen_function' => 'dashboard_info_screen',
            'position' => 40,
            'parent_url' => bp_loggedin_user_domain() . '/dashboard/',
            'parent_slug' => $bp->profile->slug,
            'default_subnav_slug' => 'dashboard'
        ));
    }
}

add_action('bp_setup_nav', 'profile_tab_dashboard_info_name');

function dashboard_info_screen() {
    // Add title and content here - last is to call the members plugin.php template.
    add_action('bp_template_content', 'dashboard_info_content');
    bp_core_load_template('buddypress/members/single/plugins');
}

function dashboard_info_title() {
    //echo 'Maker Faire Information';
}

function dashboard_info_content() {
    global $wpdb;
    global $bp;
    $user_id = bp_displayed_user_id();
    $user = get_user_by('id', $user_id);

    $user_email = (string) $user->user_email;
    $user_slug = $user->user_nicename;

    $type = bp_get_member_type(bp_displayed_user_id());
    ?>
    <div class="dashboard-wrapper">
        <?php
        echo return_membership_widget($user);
        echo return_makershed_widget();
        //echo return_makerfaire_widget($user);
        echo return_makerspace_widget($user);
        echo return_ee_events_widget();
        echo return_ee_tickets_widget($user);
        echo return_makercamp_widget($user);
        ?>
    </div><!-- end .dashboard-wrapper -->
    <?php
}
