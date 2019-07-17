<?php
namespace Indeed\Ihc;

class Ajax
{

    public function __construct()
    {
        add_action('wp_ajax_ihc_admin_send_email_popup', array($this, 'ihc_admin_send_email_popup') );
        add_action('wp_ajax_ihc_admin_do_send_email', array($this, 'ihc_admin_do_send_email') );
        add_action('wp_ajax_ihc_generate_direct_link', array($this, 'ihc_generate_direct_link') );
        add_action('wp_ajax_ihc_generate_direct_link_by_uid', array($this, 'ihc_generate_direct_link_by_uid') );
        add_action('wp_ajax_ihc_direct_login_delete_item', array($this, 'ihc_direct_login_delete_item') );
        add_action('wp_ajax_ihc_save_reason_for_cancel_delete_level', array($this, 'ihc_save_reason_for_cancel_delete_level') );
        add_action('wp_ajax_nopriv_ihc_save_reason_for_cancel_delete_level', array($this, 'ihc_save_reason_for_cancel_delete_level') );
        add_action( 'wp_ajax_ihc_close_admin_notice', array( $this, 'ihc_close_admin_notice' ) );
        add_action( 'wp_ajax_ihc_remove_media_post', array( $this, 'ihc_remove_media_post' ) );
        add_action( 'wp_ajax_nopriv_ihc_remove_media_post', array( $this, 'ihc_remove_media_post' ) );
    }


    public function ihc_admin_send_email_popup()
    {
        $uid = empty($_POST['uid']) ? 0 : esc_sql($_POST['uid']);
        if (empty($uid)){
            die;
        }
        $toEmail = \Ihc_Db::get_user_col_value($uid, 'user_email');
        if (empty($toEmail)){
            die;
        }
        $fromEmail = '';
        $fromEmail = get_option('ihc_notifications_from_email_addr');
        if (empty($fromEmail)){
            $fromEmail = get_option('admin_email');
        }
        $view = new \Indeed\Ihc\IndeedView();
        $view->setTemplate(IHC_PATH . 'admin/includes/tabs/send_email_popup.php');
        $view->setContentData([
                                'toEmail' 		=> $toEmail,
                                'fromEmail' 	=> $fromEmail,
                                'fullName'		=> \Ihc_Db::getUserFulltName($uid),
                                'website'			=> get_option('blogname')
        ], true);
        echo $view->getOutput();
        die;
    }

    public function ihc_admin_do_send_email()
    {
        $to = empty($_POST['to']) ? '' : esc_sql($_POST['to']);
        $from = empty($_POST['from']) ? '' : esc_sql($_POST['from']);
        $subject = empty($_POST['subject']) ? '' : esc_sql($_POST['subject']);
        $message = empty($_POST['message']) ? '' : stripslashes(htmlspecialchars_decode(ihc_format_str_like_wp($_POST['message'])));
        $headers = [];

        if (empty($to) || empty($from) || empty($subject) || empty($message)){
            die;
        }

        $from_name = get_option('ihc_notification_name');
        $from_name = stripslashes($from_name);
        if (!empty($from) && !empty($from_name)){
          $headers[] = "From: $from_name <$from>";
        } else if ( !empty( $from ) ){
          $headers[] = "From: <$from>";
        }
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $sent = wp_mail($to, $subject, $message, $headers);
        echo $sent;
        die;
    }

    public function ihc_generate_direct_link()
    {
        if ( empty( $_POST['username'] ) ){
            echo 'Error';
            die;
        }
        $uid = \Ihc_Db::get_wpuid_by_username( $_POST['username'] );
        if ( empty($uid) ){
            echo 'Error';
            die;
        }
        $expireTime = isset($_POST['expire_time']) ? $_POST['expire_time'] : 24;
        if ($expireTime<1){
            $expireTime = 24;
        }
        $expireTime = $expireTime * 60 * 60;
        $directLogin = new \Indeed\Ihc\Services\DirectLogin();
        echo $directLogin->getDirectLoginLinkForUser( $uid, $expireTime );
        die;
    }

    public function ihc_generate_direct_link_by_uid()
    {
        if ( empty( $_POST['uid'] ) ){
            echo 'Error';
            die;
        }
        $directLogin = new \Indeed\Ihc\Services\DirectLogin();
        echo $directLogin->getDirectLoginLinkForUser( $_POST['uid'] );
        die;
    }

    public function ihc_direct_login_delete_item()
    {
        if ( empty( $_POST['uid'] ) ){
            die;
        }
        $uid = esc_sql($_POST['uid']);
        $directLogin = new \Indeed\Ihc\Services\DirectLogin();
        $directLogin->resetTokenForUser( $uid );
        echo 1;
        die;
    }

    public function ihc_save_reason_for_cancel_delete_level()
    {
        if ( empty($_POST['lid']) || empty($_POST['reason']) || empty($_POST['action_type']) ){
           echo 0;
           die;
        }
        $uid = ihc_get_current_user();
        if ( !$uid ){
            echo 0;
            die;
        }
        $reasonDbObject = new \Indeed\Ihc\Db\ReasonsForCancelDeleteLevels();
        $made = $reasonDbObject->save(array(
            'uid'         => $uid,
            'lid'         => esc_sql($_POST['lid']),
            'reason'      => esc_sql($_POST['reason']),
            'action_type' => esc_sql($_POST['action_type']),
        ));
        if ( $made ){
            echo 1;
            die;
        }
        echo 0;
        die;
    }

    public function ihc_close_admin_notice()
    {
        update_option( 'ihc_hide_admin_license_notice', 1 );
        echo 1;
        die;
    }

    public function ihc_remove_media_post()
    {
        if ( empty( $_POST['postId'] ) ){
            return;
        }
        wp_delete_attachment( esc_sql( $_POST['postId'] ), true );
        die;
    }

}
