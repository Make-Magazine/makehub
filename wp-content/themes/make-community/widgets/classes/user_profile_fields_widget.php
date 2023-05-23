<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////// USER PROFILE FIELDS WIDGET ///////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Show a list of the user's profile fields
 
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
        $profile_field = bp_get_member_profile_data( 'field=Interests' );
        if( $profile_field ) { ?>
            <div class="item bg">
                <div class="label">Interests:</div>
                <div class="content"><?php echo esc_attr( $profile_field ) ?></div>
            </div>
        <?php } else if(bp_is_my_profile()) { ?>
            You don't have any Interests set! Set them <a href="/members/me/profile/edit/group/4/" style="text-decoration:underline;">here</a> for a personalized feed of Makezine articles.
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