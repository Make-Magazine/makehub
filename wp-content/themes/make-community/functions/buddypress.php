<?php
// Set Buddypress emails from and reply to
add_filter( 'bp_email_set_reply_to', function( $retval ) {
    return new BP_Email_Recipient( 'make@make.co' );
} );
add_filter( 'wp_mail_from', function( $email ) {
    return 'make@make.co';
}, 10, 3 );
add_filter( 'wp_mail_from_name', function( $name ) {
    return 'Make: Community';
}, 10, 3 );

function make_update_pass ($check, $password, $hash, $user_id){
    error_log('password is '.$password);
    return true;    
}
add_filter('check_password', 'make_update_pass', 20, 4);

add_action( 'widgets_init', 'parent_overrides', 11 );
function parent_overrides() {
    unregister_sidebar('sidebar-groups'); 
    unregister_sidebar('sidebar-groups-left');
    unregister_sidebar('sidebar-groups-cached');
}

// Social Media Icons based on the profile user info
function member_social_extend(){
    global $bp;
	$member_id   = $bp->displayed_user->id;

	$profiles = array(
		'Twitter',
		'Facebook',
		'Discord',
		'Youtube',
		'Vimeo',
		'LinkedIn',
		'Twitch',
		'Mastodon',
		'Instagram',
        'SnapChat',
        'Github'
	);

	$profiles_data = array();

	foreach( $profiles as $profile ) {
		$profile_content = xprofile_get_field_data( $profile, $member_id );
		if ( !empty($profile_content) && $profile_content != '<a href="" rel="nofollow"></a>' ) {
			$profiles_data[ $profile ] = $profile_content;
		} 
	}
    
	if( !( empty( $profiles_data ) ) ) {
		echo '<div class="social-icons">';
		foreach( $profiles_data as $key => $value ) {
            $value = new SimpleXMLElement($value);
            $url =  $value['href'];
            if(!empty($url[0])) {
                $profile_icon = 'https://make.co/wp-content/universal-assets/v2/images/social-icons/' . sanitize_title( $key ) . '.png';
                echo '<a href="' . $url . '" title="' . $key . '" target="_blank"><img height="25px" width="25px" src="' . $profile_icon . '" alt="' . $key . '" /></a>';
            }
		}
		echo '</div>';
	}
}
add_filter( 'bp_before_member_header_meta', 'member_social_extend' );


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////// USER PROFILE FIELDS WIDGET ///////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Register and load the widget
function makeco_load_widget_user_profile_fields() {
    register_widget( 'makeco_widget_user_profile_fields' );
}
add_action( 'widgets_init', 'makeco_load_widget_user_profile_fields' );
 
// Creating the widget 
class makeco_widget_user_profile_fields extends WP_Widget {
 
    function __construct() {
        parent::__construct(
            // Base ID of your widget
            'makeco_widget_user_profile_fields', 
            // Widget name will appear in UI
            __('Make: Community User Profile Fields', 'makeco-shortcodes'), 
            // Widget description
            array( 'description' => __( 'Displays user`s profile fields.', 'makeco-shortcodes' ), ) 
        );
    }

    // Creating widget front-end
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];
        ?>
        <div class="bp-profile-fields makeco-profile-fields">
        <?php
            $profile_field = bp_get_member_profile_data( 'field=Description' );
            if( $profile_field ) { ?>
            <div class="item biography">
                <?php echo esc_attr( $profile_field ) ?>
            </div>
        <?php } ?>
            <div class="item bg">
                <div class="label">Joined:</div>
                <div class="content">
                <?php
                global $bp;
                $currentuser = get_userdata( $bp->displayed_user->id );
                $joined = date_i18n("M, Y", strtotime($currentuser->user_registered));
                echo '' . $joined . '';
                ?>
                </div>
            </div>
        <?php
        $profile_field = bp_get_member_profile_data( 'field=Display Name' );
        if( $profile_field ) { ?>
            <div class="item bg">
                <div class="label">First Name:</div>
                <div class="content"><?php echo esc_attr( $profile_field ) ?></div>
            </div>
        <?php } ?>
        <?php
        $profile_field = bp_get_member_profile_data( 'field=Last Name' );
        if( $profile_field ) { ?>
            <div class="item bg">
                <div class="label">Last Name:</div>
                <div class="content"><?php echo esc_attr( $profile_field ) ?></div>
            </div>
        <?php } ?>
        <?php
        $profile_field = bp_get_member_profile_data( 'field=Country' );
        if( $profile_field ) { ?>
            <div class="item bg">
                <div class="label">Country:</div>
                <div class="content"><?php echo esc_attr( $profile_field ) ?></div>
            </div>
        <?php } ?>
        <?php
        $profile_field = bp_get_member_profile_data( 'field=Job Title' );
        if( $profile_field ) { ?>
            <div class="item bg">
                <div class="label">Job Title:</div>
                <div class="content"><?php echo esc_attr( $profile_field ) ?></div>
            </div>
        <?php } ?>
        <?php
        $profile_field = bp_get_member_profile_data( 'field=Website' );
        if( $profile_field ) { ?>
            <div class="item bg">
                <div class="label">Website:</div>
                <div class="content"><a href="<?php echo esc_attr( $profile_field ) ?>"><?php echo esc_attr( $profile_field ) ?></a></div>
            </div>
        <?php } ?>
        <?php
        $profile_field = bp_get_member_profile_data( 'field=Topics' );
        if( $profile_field ) { ?>
            <div class="item bg">
                <div class="label">Interests:</div>
                <div class="content"><?php echo esc_attr( $profile_field ) ?></div>
            </div>
        <?php } ?>

        </div>
        <?php
        echo $args['after_widget'];
    }
            
    // Widget Backend 
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'User Information', 'makeco-shortcodes' );
        }
        // Widget admin form
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'makeco-shortcodes' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
        <?php _e( 'It can be customized in themes/make-community/functions/buddypress.php file.', 'makeco-shortcodes' ); ?>
        </p>
        
        <?php
    }
    
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
    
}