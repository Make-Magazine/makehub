<?php

namespace Indeed\Ihc;

class GeneralActions
{

    public function __construct()
    {
        add_action( 'ihc_before_user_save_custom_field', array( $this, 'changeAvatar' ), 999, 3 );

        add_action( 'ihc_action_general_init', [ $this, 'restrictEntireWebsiteWithoutLogin' ], 999, 1 );
    }

    /**
     * @param string
     * @param string
     * @param string
     * @return none
     */
    public function changeAvatar( $uid='', $metaKey='', $metaValue='' )
    {
        if ( $metaKey != 'ihc_avatar' || $metaValue == '' ){
            return;
        }
        $oldValue = get_user_meta( $uid, 'ihc_avatar', true );
        if ( !$oldValue || $oldValue == $metaValue ){
            return;
        }
        if ( strpos( $oldValue, "http" ) === 0 ){
            return;
        }
        /// delete old avatar
        wp_delete_attachment( $oldValue, true );

    }

    /**
     * @param int
     * @return none
     */
    public function restrictEntireWebsiteWithoutLogin( $postId=0 )
    {
        global $current_user;
        if ( isset( $current_user->ID ) &&  $current_user->ID > 0 ){
            return;
        }
        $restrictionOn = get_option( 'ihc_security_restrict_everything' );
        if ( !$restrictionOn ){
            return ;
        }
        $login = get_option( 'ihc_general_login_default_page' );
        if ( $login == $postId ){
            return;
        }
        if ( $login === false || $login == '' || $login == -1 ){
            return;
        }
        $loginPermalink = get_permalink( $login );
        if ( $loginPermalink === false || $loginPermalink == '' ){
            return;
        }
        $except = get_option( 'ihc_security_restrict_everything_except' );
        $allow = explode( ',', preg_replace('/\s+/', '', $except ) );

        if ( count( $allow ) > 0 && in_array( $postId, $allow ) ){
            return;
        }
        wp_redirect( $loginPermalink );
        exit;
    }


}
