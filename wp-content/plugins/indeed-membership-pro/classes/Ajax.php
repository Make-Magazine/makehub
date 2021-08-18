<?php
namespace Indeed\Ihc;

class Ajax
{
    /**
     * @param none
     * @return none
     */
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
        add_action( 'wp_ajax_nopriv_ihc_update_list_notification_constants', array( $this, 'ihc_update_list_notification_constants' ) );
        add_action( 'wp_ajax_ihc_update_list_notification_constants', array( $this, 'ihc_update_list_notification_constants' ) );
        add_action( 'wp_ajax_ihc_admin_list_users_total_spent_values', array( $this, 'usersTotalSpentValues') );
        add_action( 'wp_ajax_ihc_admin_make_order_completed', array( $this, 'adminMakeOrderCompleted') );

        add_action( 'wp_ajax_ihc_get_membership_details', [ $this, 'adminGetMembershipDetails' ] );
        add_action( 'wp_ajax_ihc_user_level_get_next_expire_time', [ $this, 'adminGetNextExpireTimeOnUserLevel' ] );
        add_action( 'wp_ajax_ihc_user_level_pause', [ $this, 'adminUserSubscriptionPause' ] );
        add_action( 'wp_ajax_ihc_user_level_reactivate', [ $this, 'adminUserSubscriptionReactivate'] );

        add_action( 'wp_ajax_ihc_user_put_subscrition_on_pause', [ $this, 'userPutSubscriptionOnPause' ] );
        add_action( 'wp_ajax_nopriv_ihc_user_put_subscrition_on_pause', [ $this, 'userPutSubscriptionOnPause' ] );

        add_action( 'wp_ajax_ihc_user_put_subscrition_resume', [ $this, 'userPutSubscriptionResume' ] );
        add_action( 'wp_ajax_nopriv_ihc_user_put_subscrition_resume', [ $this, 'userPutSubscriptionResume' ] );
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_admin_send_email_popup()
    {
        if ( !indeedIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
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

    /**
     * @param none
     * @return none
     */
    public function ihc_admin_do_send_email()
    {
        if ( !indeedIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
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

    /**
     * @param none
     * @return none
     */
    public function ihc_generate_direct_link()
    {
        if ( !indeedIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
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

    /**
     * @param none
     * @return none
     */
    public function ihc_generate_direct_link_by_uid()
    {
        if ( !indeedIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( empty( $_POST['uid'] ) ){
            echo 'Error';
            die;
        }
        $directLogin = new \Indeed\Ihc\Services\DirectLogin();
        echo $directLogin->getDirectLoginLinkForUser( $_POST['uid'] );
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_direct_login_delete_item()
    {
        if ( !indeedIsAdmin() ){
            echo 0;
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            echo 0;
            die;
        }
        if ( empty( $_POST['uid'] ) ){
            die;
        }
        $uid = esc_sql($_POST['uid']);
        $directLogin = new \Indeed\Ihc\Services\DirectLogin();
        $directLogin->resetTokenForUser( $uid );
        echo 1;
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_save_reason_for_cancel_delete_level()
    {
        if ( empty($_POST['lid']) || empty($_POST['reason']) || empty($_POST['action_type']) ){
           die;
        }
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $uid = ihc_get_current_user();
        if ( !$uid ){
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
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_close_admin_notice()
    {
        if ( !indeedIsAdmin() ){
            die;
        }
        update_option( 'ihc_hide_admin_license_notice', 1 );
        echo 1;
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_remove_media_post()
    {
        if ( empty( $_POST['postId'] ) ){
            return;
        }
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        wp_delete_attachment( esc_sql( $_POST['postId'] ), true );
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_update_list_notification_constants()
    {
        if ( !indeedIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( empty( $_POST['notificationType'] ) ){
            die;
        }
        $data = ihcNotificationConstants( esc_sql( $_POST['notificationType'] ) );
        if ( !$data ){
            die;
        }
        foreach ( $data as $constant => $value ){
            echo '<div>' . $constant . '</div>';
        }
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function usersTotalSpentValues()
    {
        global $wpdb;
        if ( !indeedIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( empty( $_POST['users'] ) ){
            die;
        }
        $ids = esc_sql( $_POST['users'] );
        $queryString = "SELECT SUM(amount_value) AS sum, uid FROM {$wpdb->prefix}ihc_orders WHERE uid IN ($ids) GROUP BY uid";
        $data = $wpdb->get_results( $queryString );
        if ( !$data ){
            die;
        }
        foreach ( $data as $object ){
            $array[$object->uid] = ihc_format_price_and_currency( '', $object->sum );
        }
        echo json_encode( $array );
        die;
    }

    public function adminMakeOrderCompleted()
    {
        if ( !indeedIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( empty( $_POST['id'] ) ){
            die;
        }
        $orderId = esc_sql( $_POST['id'] );
        $orderObject = new \Indeed\Ihc\Db\Orders();
        $orderObject->setId( $orderId )->update( 'status', 'Completed' );
        $orderData = $orderObject->fetch()->get();
        if ( !$orderData ){
            die;
        }
        $orderMeta = new \Indeed\Ihc\Db\OrderMeta();
        $paymentGateway = $orderMeta->get( $orderId, 'ihc_payment_type' );
        $levelData = \Indeed\Ihc\Db\Memberships::getOne( $orderData->lid );
        if (isset($levelData['access_trial_time_value']) && $levelData['access_trial_time_value'] > 0 && \Indeed\Ihc\UserSubscriptions::isFirstTime( $orderData->uid, $orderData->lid )){
          /// CHECK FOR TRIAL
            \Indeed\Ihc\UserSubscriptions::makeComplete( $orderData->uid, $orderData->lid, true, [ 'manual' => true, 'payment_gateway' => $paymentGateway ] );
        } else {
            \Indeed\Ihc\UserSubscriptions::makeComplete( $orderData->uid, $orderData->lid, false, [ 'manual' => true, 'payment_gateway' => $paymentGateway ] );
        }
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function adminGetMembershipDetails()
    {
        global $wpdb;
        if ( !indeedIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( !isset( $_POST['levelId'] ) || $_POST['levelId'] == -1 || !isset( $_POST['uid'] ) ){
            die;
        }
        $lid = esc_sql( $_POST['levelId'] );
        $uid = esc_sql( $_POST['uid'] );
        if ( \Indeed\Ihc\UserSubscriptions::userHasSubscription( $uid, $lid) ){
            die;
        }

        // level data
        $levelDetails = \Indeed\Ihc\Db\Memberships::getOne( $lid );

        // trial
        $isTrial = \Indeed\Ihc\Db\Memberships::isTrial( $lid );
        if ( $isTrial ){
            $trial = esc_html__( 'Yes - until ', 'ihc' ) . date( 'Y-m-d H:i:s', \Indeed\Ihc\Db\Memberships::getEndTimeForTrial( $lid, indeed_get_unixtimestamp_with_timezone() ) );
        } else {
           $trial = esc_html__( 'No', 'ihc' );
        }

        // grace period
        $gracePeriod = \Indeed\Ihc\Db\Memberships::getMembershipGracePeriod( $levelDetails['id'] );
        if ( $gracePeriod ){
            $gracePeriod = esc_html__( 'Yes - ', 'ihc') . $gracePeriod . ihcGetTimeTypeByCode( 'D', $gracePeriod ) .  esc_html__(' after expires', 'ihc' );
        } else {
            $gracePeriod = esc_html__( 'No', 'ihc' );
        }

        // start time & expire time
        $startTime = date( 'Y-m-d H:i:s', indeed_get_unixtimestamp_with_timezone() );
        $endTime = date( 'Y-m-d H:i:s', \Indeed\Ihc\Db\Memberships::getEndTime( $lid, indeed_get_unixtimestamp_with_timezone() ) ) ;
        if ( $isTrial ){
            $endTime = date( 'Y-m-d H:i:s', \Indeed\Ihc\Db\Memberships::getEndTimeForTrial( $lid, indeed_get_unixtimestamp_with_timezone() ) );
        }

        $str = "<tr class='ihc-js-user-level-row-" . $lid . "'>
                    <td class='ihc-levels-table-name'>"
                      . $levelDetails['label'] . "<input type='hidden' name='ihc_assign_user_levels[]' value='" . $lid . "' /></td>
                    <td>" . \Indeed\Ihc\Db\Memberships::getAccessTypeAsLabel( $lid ) . "</td>
                    <td>" . ihcPaymentPlanDetailsAdmin( $uid, $lid ) . "</td>
                    <td>-</td>
                    <td>" . $trial . "</td>
                    <td>" . $gracePeriod . "</td>
                    <td>-</td>
                    <td>
                      <div class='input-group'>
                      <input type='text' name='start_time_levels[" . $lid . "]' value='" . $startTime . "' placeholder='' class='start_input_text form-control' />
                      <div class='input-group-addon'><i class='fa-ihc ihc-icon-edit'></i></div>
                      </div>
                    </td>
                    <td>
                      <div class='input-group'>
                      <input type='text' name='expire_levels[" . $lid . "]' value='" . $endTime . "' placeholder='' class='expire_input_text form-control' />
                      <div class='input-group-addon'><i class='fa-ihc ihc-icon-edit'></i></div>
                      </div>
                    </td>
                    <td class='ihc-levels-table-status'>" . esc_html__( 'Active', 'ihc' ) . "</td>
                    <td>
                        <div class='ihc-js-delete-user-level ihc-pointer' data-lid='" . $lid . "' >" . esc_html__( 'Remove', 'ihc' ) . "</div>
                    </td>
        </tr>";
        echo $str;
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function adminGetNextExpireTimeOnUserLevel()
    {
        if ( !indeedIsAdmin() ){
            die;
        }
        if ( !isset( $_POST['levelId'] ) || !isset( $_POST['currentExpireTime'] ) ){
            die;
        }
        $endTime = $_POST['currentExpireTime'] == '0000-00-00 00:00:00' ? indeed_get_unixtimestamp_with_timezone() : strtotime( esc_sql( $_POST['currentExpireTime'] ) );
        $endTime = date( 'Y-m-d H:i:s', \Indeed\Ihc\Db\Memberships::getEndTime( esc_sql( $_POST['levelId'] ), $endTime ) );
        echo json_encode([
                'expire_time'   => $endTime,
                'new_status'    => esc_html__( 'Active', 'ihc' ),
        ]);
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function adminUserSubscriptionPause()
    {
        if ( !indeedIsAdmin() ){
            die;
        }
        if ( !isset( $_POST['levelId'] ) || !isset( $_POST['uid'] ) || !isset( $_POST['currentExpireTime'] ) ){
            die;
        }
        $expireTime = strtotime( $_POST['currentExpireTime'] );
        if ( indeed_get_unixtimestamp_with_timezone() > $expireTime ){
            die;
        }
        echo json_encode(
          [
                  'remain_time'       => $expireTime - indeed_get_unixtimestamp_with_timezone(),
                  'expire_time'       => date( 'Y-m-d H:i:s', indeed_get_unixtimestamp_with_timezone() ),
                  'new_status'        => esc_html__( 'Paused', 'ihc' ),
          ]
        );
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function adminUserSubscriptionReactivate()
    {
          if ( !indeedIsAdmin() ){
              die;
          }
          if ( !isset( $_POST['subscriptionId'] ) ){
              die;
          }
          $currentTime = indeed_get_unixtimestamp_with_timezone();
          $remainTime = \Indeed\Ihc\Db\UserSubscriptionsMeta::getOne( esc_sql($_POST['subscriptionId']), 'remain_time' );
          $expireTime = $currentTime + $remainTime;
          echo json_encode(
            [
                    'start_time'        => date( 'Y-m-d H:i:s', $currentTime ),
                    'expire_time'       => date( 'Y-m-d H:i:s', $expireTime ),
                    'new_status'        => esc_html__( 'Active', 'ihc' ),
            ]
          );
          die;
    }

    /**
     * @param none
     * @return string
     */
    public function userPutSubscriptionOnPause()
    {
        global $current_user;
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        if ( !isset( $current_user->ID ) ){
            die;
        }
        if ( !isset( $_POST['subscriptionId'] ) ){
            die;
        }
        if ( !isset( $_POST['lid'] ) ){
            die;
        }
        $uid = $current_user->ID;
        $subscriptionId = esc_sql($_POST['subscriptionId']);
        $lid = esc_sql($_POST['lid']);
        if ( \Indeed\Ihc\UserSubscriptions::userHasSubscription( $uid, $lid ) === false ){
            die;
        }
        $currentTime = indeed_get_unixtimestamp_with_timezone();
        $subscriptionData = \Indeed\Ihc\UserSubscriptions::getOne( $uid, $lid );
        if ( $subscriptionData === false || !isset( $subscriptionData['expire_time'] ) ){
            die;
        }
        $remainTime = strtotime( $subscriptionData['expire_time'] ) - $currentTime;
        if ( $remainTime < 0 ){
            die;
        }

        $currentTime = date( 'Y-m-d H:i:s', $currentTime );
        \Indeed\Ihc\UserSubscriptions::updateSubscriptionTime( $uid, $lid, '', $currentTime, [] );
        \Indeed\Ihc\UserSubscriptions::updateStatusBySubscriptionId( $subscriptionId, 4 );
        \Indeed\Ihc\Db\UserSubscriptionsMeta::save( $subscriptionId, 'remain_time', $remainTime );
    }

    /**
     * @param none
     * @return string
     */
    public function userPutSubscriptionResume()
    {
        global $current_user;
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        if ( !isset( $current_user->ID ) ){
            die;
        }
        if ( !isset( $_POST['subscriptionId'] ) ){
            die;
        }
        if ( !isset( $_POST['lid'] ) ){
            die;
        }
        $uid = $current_user->ID;
        $subscriptionId = esc_sql($_POST['subscriptionId']);
        $lid = esc_sql($_POST['lid']);

        $currentTime = indeed_get_unixtimestamp_with_timezone();
        $remainTime = \Indeed\Ihc\Db\UserSubscriptionsMeta::getOne( $subscriptionId, 'remain_time' );
        $expireTime = $currentTime + $remainTime;
        $expireTime = date( 'Y-m-d H:i:s', $expireTime );

        \Indeed\Ihc\UserSubscriptions::updateSubscriptionTime( $uid, $lid, '', $expireTime, [] );
        \Indeed\Ihc\UserSubscriptions::updateStatusBySubscriptionId( $subscriptionId, 1 );
        \Indeed\Ihc\Db\UserSubscriptionsMeta::save( $subscriptionId, 'remain_time', '' );
    }

}
