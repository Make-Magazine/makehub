<?php
namespace Indeed\Ihc;

/*
How to send a notification:
$notification = new \Indeed\Ihc\Notifications();
$notification->setUid( $uid )
             ->setLid( $lid )
             ->setType( 'register' )
             ->setMessageVariables( [] )
             ->send();
*/
class Notifications
{
    /**
     * @var int
     */
    private $uid                = 0;
    /**
     * @var int
     */
    private $lid                = 0;
    /**
     * @var string
     */
    private $type               = '';
    /**
     * @var array
     */
    private $messageVariables   = [];
    /**
     * @var string
     */
    private $subject            = '';
    /**
     * @var string
     */
    private $message            = '';
    /**
     * @var array
     */
    private $adminCases         = [
                          'admin_user_register',
                          'admin_before_user_expire_level',
                          'admin_second_before_user_expire_level',
                          'admin_third_before_user_expire_level',
                          'admin_user_expire_level',
                          'admin_user_payment',
                          'admin_user_profile_update',
                          'ihc_cancel_subscription_notification-admin',
                          'ihc_delete_subscription_notification-admin',
                          'ihc_order_placed_notification-admin',
                          'ihc_new_subscription_assign_notification-admin',
                          'admin_user_enter_grace_period',
                          'admin_user_subscription_renew',
                          'admin_user_login',
                          'admin_before_subscription_payment_due',
                          'admin_user_subscription_first_time_activation',
                          'admin_user_subscription_trial_expired',
    ];


    public function __construct(){}

    public function getAllNotificationNames()
    {
        return [
          //----------Admin Notifications----------

          //Register Process
          'admin_user_register' => __('New Customer Registered', 'ihc'), // - Admin Notification

          //Subscriptions
          'ihc_new_subscription_assign_notification-admin' => __('New Subscription Assigned to Customer', 'ihc'),
          'admin_user_subscription_first_time_activation'		=> __( 'Activation Subscription - First Time', 'ihc' ),
          'admin_user_subscription_renew'			=> __( 'Activation Subscription - Renewed', 'ihc' ),
          'admin_user_subscription_trial_expired'			=> __( 'Customer Trial Subscription has expired', 'ihc' ),
          'admin_before_user_expire_level' => __('First Alert Before Subscription Expire', 'ihc'),
          'admin_second_before_user_expire_level' => __('Second Alert Before Subscription Expire', 'ihc'),
          'admin_third_before_user_expire_level' => __('Third Alert Before Subscription Expire', 'ihc'),
          'admin_user_enter_grace_period'			=> __( 'Customer enter in Grace Period', 'ihc' ),
          'admin_user_expire_level' => __('After Subscription Expired', 'ihc'),

          //Payments
          'ihc_order_placed_notification-admin' => __('New Order Created', 'ihc'),
          'admin_user_payment' => __('Payment confirmation received - Order Completed', 'ihc'),
          'admin_before_subscription_payment_due'				=> __( 'Before Subscription Payment due', 'ihc' ),

          //Customer Actions
          'admin_user_profile_update' => __('Customer has Updated his Profile information', 'ihc'),
          'admin_user_login'						=> __( 'Customer Login Alert', 'ihc' ),
          'ihc_cancel_subscription_notification-admin' => __('Customer has Cancelled Subscription', 'ihc'),
          'ihc_delete_subscription_notification-admin' => __('Customer Deleted Subscription', 'ihc'),


          //----------Users Notifications----------

          //Register Process
          'register' => __('New Active Account Registered', 'ihc'), //Register
          'review_request' => __('New Pending Account Registered- Approve Request', 'ihc'), //register with pending

          //Register Lite
          'register_lite_send_pass_to_user' => __('Send auto-generated Password to Customer', 'ihc'),

          //Double Email Verification
          'email_check' => __('Double E-mail Verification Request', 'ihc'),
          'email_check_success' => __('Double E-mail Verification Completed', 'ihc'),

          //Reset Password Process
          'reset_password_process' => __('Reset Password - Step 1: Confirmation Request', 'ihc'),
          'reset_password' => __('Reset Password - Step 2: Send Generated Password', 'ihc'),
          'change_password' => __('Reset Password - Step 3: Password Changed notification', 'ihc'),

          //Customer Account
          'approve_account' => __('Account have been Approved by Admin'),
          'delete_account' => __('Account has been Deleted', 'ihc'),

          //Custom Actions
          'user_update' => __('Customer has Updated his Profile information', 'ihc'),
          'ihc_cancel_subscription_notification-user' => __('Customer has Cancelled Subscription', 'ihc'),
          'ihc_delete_subscription_notification-user' => __('Customer Deleted Subscription', 'ihc'),

          //Subscriptions
          'ihc_new_subscription_assign_notification' => __('New Subscription Assigned to Customer', 'ihc'),
          'ihc_subscription_activated_notification' => __('Activation Subscription - First Time', 'ihc'),
          'user_subscription_renew'			=> __( 'Activation Subscription - Renewed', 'ihc' ),
          'user_subscription_trial_expired'			=> __( 'Customer Trial Subscription has expired', 'ihc' ),
          'before_expire' => __('First Alert Before Subscription Expire', 'ihc'),
          'second_before_expire' => __('Second Alert Before Subscription Expire', 'ihc'),
          'third_before_expire' => __('Third Alert Before Subscription Expire', 'ihc'),
          'user_enter_grace_period'			=> __( 'Customer enter in Grace Period', 'ihc' ),
          'expire' => __('After Subscription Expired', 'ihc'),

          //Payments
          'ihc_order_placed_notification-user' => __('New Order Created', 'ihc'),
          'payment' => __('Payment confirmation received - Order Completed', 'ihc'),
          'bank_transfer' => __('Bank Transfer Payment Details', 'ihc'),
          'user_before_subscription_payment_due'				=> __( 'Before Subscription Payment due', 'ihc' ),

          //Drip Content
          'drip_content-user' => __('When Post become Available', 'ihc'),
        ];
    }

    public function getAdminCases()
    {
        return $this->adminCases;
    }

    /**
     * @param int
     * @return object
     */
    public function setUid( $input=0 )
    {
        $this->uid = $input;
        return $this;
    }

    /**
     * @param int
     * @return object
     */
    public function setLid( $input=0 )
    {
      $this->lid = $input;
      return $this;
    }

    /**
     * @param int
     * @return object
     */
    public function setType( $input='' )
    {
        $this->type = $input;
        return $this;
    }

    /**
     * @param array
     * @return object
     */
    public function setMessageVariables( $input=[] )
    {
        $this->messageVariables = $input;
        return $this;
    }

    /**
     * @param string
     * @return object
     */
    public function setSubject( $input='' )
    {
        $this->subject = $input;
        return $this;
    }

    /**
     * @param string
     * @return object
     */
    public function setMessage( $input='' )
    {
        $this->message = $input;
        return $this;
    }

    /**
     * @param none
     * @return array
     */
    public function setNotificationData()
    {
          global $wpdb;
          if ( $this->lid > -1 ){
              // notification for a specific subscription
              $q = $wpdb->prepare("SELECT id,notification_type,level_id,subject,message,pushover_message,pushover_status,status FROM " . $wpdb->prefix . "ihc_notifications
                          WHERE 1=1
                          AND notification_type=%s
                          AND level_id=%d
                          ORDER BY id DESC LIMIT 1;", $this->type, $this->lid );
              $data = $wpdb->get_row($q);
              if ($data){
                  $subject = @$data->subject;
                  $message = @$data->message;

                  $domain = 'ihc';
                  $languageCode = get_user_meta( $u_id, 'ihc_locale_code', true );
                  $wmplName = $this->type . '_title_' . $l_id;
                  $this->subject = apply_filters( 'wpml_translate_single_string', $subject, $domain, $wmplName, $languageCode );
                  $wmplName = $this->type . '_message_' . $l_id;
                  $this->message = apply_filters( 'wpml_translate_single_string', $message, $domain, $wmplName, $languageCode );
              }
          }

          if ( $this->subject != '' && $this->message != '' ){
              return $this;
          }

          // general notification - no subscription
          $q = $wpdb->prepare( "SELECT id,notification_type,level_id,subject,message,pushover_message,pushover_status,status FROM " . $wpdb->prefix . "ihc_notifications
                      WHERE 1=1
                      AND notification_type=%s
                      AND level_id='-1'
                      ORDER BY id DESC LIMIT 1;", $this->type );
          $data = $wpdb->get_row($q);
          if ($data){
              $subject = @$data->subject;
              $message = @$data->message;

              $domain = 'ihc';
              $languageCode = get_user_meta( $this->uid, 'ihc_locale_code', true );
              $wmplName = $this->type . '_title_-1';
              $this->subject = apply_filters( 'wpml_translate_single_string', $subject, $domain, $wmplName, $languageCode );
              $wmplName = $this->type . '_message_-1';
              $this->message = apply_filters( 'wpml_translate_single_string', $message, $domain, $wmplName, $languageCode );
          }
    }

    /**
     * @param none
     * @return bool
     */
    public function send()
    {
          if ( !$this->uid || $this->type == '' ){
              return false;
          }

          // notification content
          if ( $this->subject == '' || $this->message == '' ){
              $this->setNotificationData();
          }

          if ( $this->message == '' ){
              return false;
          }

          $fromName = $this->getFromName();

          if ( in_array( $this->type, $this->adminCases ) ){
              // admin
              $userEmail = $this->getAdminEmail();
          } else {
              // member
              $userEmail = \Ihc_Db::user_get_email( $this->uid );
          }
          $fromEmail = $this->getFromEmail();

          $this->message = ihc_replace_constants( $this->message, $this->uid, $this->lid, $this->lid, $this->messageVariables );
          $this->subject = ihc_replace_constants( $this->subject, $this->uid, $this->lid, $this->lid, $this->messageVariables );
          $this->message = stripslashes( htmlspecialchars_decode( ihc_format_str_like_wp( $this->message ) ) );
          $this->message = apply_filters( 'ihc_send_notification_filter_message', $this->message, $this->uid, $this->lid, $this->type );

          $this->message = "<html><head></head><body>" . $this->message . "</body></html>";
          if ( $this->subject == '' && $this->message == '' && $userEmail == '' ){
              return false;
          }

          if ( !empty( $fromEmail ) && !empty( $fromName ) ){
            $headers[] = "From: $fromName <$fromEmail>";
          }
          $headers[] = 'Content-Type: text/html; charset=UTF-8';
          $sent = wp_mail( $userEmail, $this->subject, $this->message, $headers );

          if ( $sent ){
              \Indeed\Ihc\Db\NotificationLogs::save([
                            'uid'                       => $this->uid,
                            'lid'                       => $this->lid,
                            'message'                   => $this->message,
                            'subject'                   => $this->subject,
                            'notification_type'         => $this->type,
                            'email_address'             => $userEmail,
              ]);
          }

          $this->extraServices();

          return $sent;
    }

    /**
     * @param none
     * @return string
     */
    private function getFromEmail()
    {
        $fromEmail = get_option( 'ihc_notification_email_from' );
        if ( $fromEmail == '' ){
            $fromEmail = get_option( 'admin_email' );
        }
        return $fromEmail;
    }

    /**
     * @param none
     * @return string
     */
    private function getFromName()
    {
        $fromName = get_option( 'ihc_notification_name' );
        if ( $fromName == '' ){
            $fromName = get_option( 'blogname' );
        }
        return $fromName;
    }

    /**
     * @param none
     * @return string
     */
    private function getAdminEmail()
    {
        $adminEmail = get_option( 'ihc_notification_email_addresses' );
        if ( $adminEmail == '' ){
          $adminEmail = get_option( 'admin_email' );
        }
        return $adminEmail;
    }

    /**
     * @param none
     * @return none
     */
    private function extraServices()
    {
        if ( ihc_is_magic_feat_active( 'pushover' ) ){
            $this->pushover();
        }
    }

    /**
     * @param none
     * @return none
     */
    private function pushover()
    {
        $toAdmin = in_array( $this->type, $this->adminCases ) ? true : false;
        require_once IHC_PATH . 'classes/services/Ihc_Pushover.class.php';
        $pushover = new \Ihc_Pushover();
        $pushover->send_notification( $this->uid, $this->lid, $this->type, $toAdmin );
    }

    /**
     * @param string
     * @return array
     */
    public function getNotificationTemplate( $type='' )
    {
      $template = [
                    'subject'				=> '',
                    'content'				=> '',
                    'explanation' 		=> '',
      ];

       switch ($type){
         case 'register':
   $template['subject'] = '{blogname}: Welcome to {blogname}';
   $template['content'] = '<p>Hi {username},</p><br/>

   <p>Thanks for registering on {blogname}. Your account is now active.</p><br/>

   <p>To login please fill out your credentials on:<br/>
   {login_page}</p><br/>

   <p>Your Username: {username}</p><br/><br/>


   <p>Have a nice day!</p>';
   $template['explanation'] = __( 'Notification is sent first time when member Register on website and account does not requires to be Approved.', 'ihc' );
           break;

         case 'review_request':
   $template['subject'] = '{blogname}: Welcome to {blogname}';
   $template['content'] = '<p>Hi {username},</p><br/>

   <p>Thanks for registering on {blogname}. Your account is waiting to be approved.</p><br/>

   <p>Once your Account is approved you can login using your credentials on:<br/>
   <a href="{login_page}">{login_page}</a></p><br/>

   <p>Your Username: {username}</p><br/><br/>


   <p>Have a nice day!</p>';
   $template['explanation'] = __( 'Notification is sent first time when member Register on website and account requires to be Approved.', 'ihc' );
           break;

         case 'payment':
           $template['subject'] = '{blogname}: Payment proceed';
           $template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

   <p>You have proceed a new Payment into your account on {blogname}.</p><br/><br/>


   <p>Thanks for your payment!</p>';
   $template['explanation'] = __( 'Every time when a Payment confirmation is received and Order becomes Completed', 'ihc' );
           break;

         case 'user_update':
           $template['subject'] = '{blogname}: Your Account has been Updated';
           $template['content'] = '<p>Hi {username},</p><br/>

   <p>Your Account has been Updated.</p><br/>

   <p>To visit your Profile page follow the next link:<br/>
   <a href="{account_page}">{account_page}</a></p><br/>

   <p>Have a nice day!</p>';
   $template['explanation'] = __( 'Notification is sent everytime member update his Account details via My Account page', 'ihc' );
           break;

         case 'before_expire':
           $template['subject'] = '{blogname}: Your Subscription Expire';
           $template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

   <p>Your Subscription {current_level} is going to expire on {current_level_expire_date}.</p><br/>

   <p>To update your Subscriptions, please, visit your Profile page on:<br/>
   <a href="{account_page}">{account_page}</a></p><br/>

   <p>Have a nice day!</p>';
   $template['explanation'] = __( 'Reminder notification sent before member membership is going to expire. You can choose how many days before notification should be triggered. This process is handled by a WP Cron running daily.', 'ihc' );
           break;
         case 'second_before_expire':
           $template['subject'] = '{blogname}: Your Subscription Expire';
           $template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

   <p>Your Subscription {current_level} is going to expire on {current_level_expire_date}.</p><br/>

   <p>To update your Subscriptions, please, visit your Profile page on:<br/>
   <a href="{account_page}">{account_page}</a></p><br/>

   <p>Have a nice day!</p>';
   $template['explanation'] = __( 'Second Reminder notification sent before member membership is going to expire. You can choose how many days before notification should be triggered. This process is handled by a WP Cron running daily.', 'ihc' );
           break;
         case 'third_before_expire':
           $template['subject'] = '{blogname}: Your Subscription Expire';
           $template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

   <p>Your Subscription {current_level} is going to expire on {current_level_expire_date}.</p><br/>

   <p>To update your Subscriptions, please, visit your Profile page on:<br/>
   <a href="{account_page}">{account_page}</a></p><br/>

   <p>Have a nice day!</p>';
   $template['explanation'] = __( 'Third Reminder notification sent before member membership is going to expire. You can choose how many days before notification should be triggered. This process is handled by a WP Cron running daily.', 'ihc' );
           break;
         case 'expire':
           $template['subject'] = '{blogname}: Your Subscription has Expired';
           $template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

   <p>Your Subscription {current_level} has expired on {current_level_expire_date}.</p><br/>

   <p>To update your Subscriptions, please, visit your Profile page on:<br/>
   <a href="{account_page}">{account_page}</a></p><br/>

   <p>Have a nice day!</p>';
   $template['explanation'] = __( 'Notification is sent after member membership has expired. This process is handled by a WP Cron running daily.', 'ihc' );
           break;

         case 'email_check':
           $template['subject'] = '{blogname}: Email Verification';
           $template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

   <p>You must confirm/validate your Email Account before logging in.</p><br/>

   <p>Please click on the following link to successfully activate your account:<br/>
   <a href="{verify_email_address_link}">click here</a></p><br/>

   <p>Have a nice day!</p><br/>';
   $template['explanation'] = __( 'When Double Email Verification setting is turned on, after member registration this first email is sent with a generated confirmation link inside that will allow member to confirm that his email is real.', 'ihc' );
           break;

         case 'email_check_success':
           $template['subject'] = '{blogname}: Email Verification Successfully';
           $template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

   <p>Your account is now verified at {blogname}.</p><br/>

   <p>Have a nice day!</p><br/>';
   $template['explanation'] = __( 'When Double Email Verification setting is turned on, after member confirmed his email address a successful confirmation notification is sent.', 'ihc' );
           break;

         case 'reset_password_process':
           $template['subject'] = '{blogname}: Reset Password request';
           $template['content'] = '<p>Hi {first_name} {last_name},</p></br>

   <p>You or someone else has requested to change password for your account: {username}</p></br>

   <p>To confirm this request click <a href="{password_reset_link}">here</a></p></br>

   <p>A new generated Password will be sent via Email next after the request was confirmed.</p>

   <p>If you did not request for a new password, please ignore this Email notification.</p>';
   $template['explanation'] = __( 'When member wants to reset his password, this notification is sent first time in order user to confirm this request. A generated link for request confirmation will be sent. This will guarantee that request was something by current member and not someone else. ', 'ihc' );
           break;

         case 'reset_password':
           $template['subject'] = '{blogname}: New Password generated';
           $template['content'] = '<p>Hi {first_name} {last_name},</p></br>

   <p>You or someone else has requested to change password for your account: {username}</p></br>

   <p>Your new Password is: <strong>{NEW_PASSWORD}</strong></p></br>

   <p>To update your Password once you are logged from your Profile Page:
   <a href="{account_page}">{account_page}</a></p></br>

   <p>If you did not request for a new password, please ignore this Email notification.</p>';
   $template['explanation'] = __( 'After member confirms his reset passwrod request a random password will be generated for him and sent via this second Notification.', 'ihc' );
           break;

         case 'change_password':
           $template['subject'] = '{blogname}: Your Password has been changed';
           $template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

   <p>Your Password has been changed.</p><br/>

   <p>To login please fill out your credentials on:<br/>
   <a href="{login_page}">{login_page}</a></p><br/>

   <p>Your Username: {username}</p><br/>

   <p>Have a nice day!</p>';
   $template['explanation'] = __( 'After WordPress user password have been successfully changed this notification will be sent.', 'ihc' );
           break;

         case 'delete_account':
           $template['subject'] = '{blogname}: Your Account has been deleted';
           $template['content'] = '<p>Hi {username},</p><br/>

   <p>Your account has been deleted from {blogname}.</p><br/>

   <p>Have a nice day!</p>';
   $template['explanation'] = __( 'When member WordPress account have been deleted his is notified via email.', 'ihc' );
           break;

         case 'bank_transfer':
             $template['subject'] = '{blogname}: Payment Inform';
             $template['content'] = 'Hi {username},

   Please proceed the bank transfer payment for: {currency}{amount}

   <strong>Payment Details:</strong> Subscription {level_name} for {username} with Identification: {user_id}_{level_id}

   &nbsp;

   <strong>Bank Details:</strong>

   IBAN:xxxxxxxxxxxxxxxxxxxx

   Bank NAME';
   $template['explanation'] = __( 'If member choose to pay via Bank Transfer he will receives a notification with all details that needs to be followed in order to complete the payment.', 'ihc' );
           break;

         case 'approve_account':
             $template['subject'] = '{blogname}: Your Account has been activated';
             $template['content'] = '<p>Hi {username},</p><br/>

   <p>Your Account has been activated!</p><br/>

   <p>Have a nice day!</p>';
   $template['explanation'] = __( 'If new registered account needs to be approved, after Administrator approve member account he will be notified via Email.', 'ihc' );
           break;

         case 'admin_user_register':
           /// ADMIN - USER REGISTER
             $template['subject'] = '{blogname}: New Membership User registration';
             $template['content'] = '<html><head></head><body><p>New Membership User registration on: <strong> {blogname} </strong></p>

   <p><strong> Username:</strong> {username}</p>

   <p><strong> Email:</strong> {user_email}</p>

   <p><strong> Membership:</strong> {level_name}</p>

   <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'Notification is sent to Administrator first time when member Register on website.', 'ihc' );
           break;

         case 'admin_before_user_expire_level':
           /// ADMIN - Before Membership Expire
             $template['subject'] = '{blogname}: User Membership Expire';
             $template['content'] = '<html><head></head><body>
   <p>Subscription {current_level} for <strong> Username: {username}</strong> is going to expire on {current_level_expire_date}.</p><br/>

   <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'Reminder notification sent to Administator before member membership is going to expire. You can choose how many days before notification should be triggered. This process is handled by a WP Cron running daily.', 'ihc' );
           break;

         case 'admin_second_before_user_expire_level':
           /// ADMIN - Before Membership Expire
             $template['subject'] = '{blogname}: User Membership Expire';
             $template['content'] = '<html><head></head><body>
   <p>Subscription {current_level} for <strong> Username: {username}</strong> is going to expire on {current_level_expire_date}.</p><br/>

   <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'Second Reminder notification sent to Administator before member membership is going to expire. You can choose how many days before notification should be triggered. This process is handled by a WP Cron running daily.', 'ihc' );
           break;

         case 'admin_third_before_user_expire_level':
           /// ADMIN - Before Membership Expire
             $template['subject'] = '{blogname}: User Membership Expire';
             $template['content'] = '<html><head></head><body>
   <p>Subscription {current_level} for <strong> Username: {username}</strong> is going to expire on {current_level_expire_date}.</p><br/>

   <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'Third Reminder notification sent to Administator before member membership is going to expire. You can choose how many days before notification should be triggered. This process is handled by a WP Cron running daily.', 'ihc' );
           break;

         case 'admin_user_expire_level':
           /// ADMIN - After Membership Expired
             $template['subject'] = '{blogname}: User Membership Expired';
             $template['content'] = '<html><head></head><body>
   <p>Subscription {current_level} for<strong> Username: {username}</strong> has expired on {current_level_expire_date}.</p>
   <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'Notification is sent to Administrator after member membership has expired. This process is handled by a WP Cron running daily.', 'ihc' );
           break;

         case 'admin_user_payment':
           /// ADMIN - New Payment Completed
             $template['subject'] = '{blogname}: New Payment Completed';
             $template['content'] = '<html><head></head><body>
   <p><strong> User: {username}</strong> has completed a new payment.</p>
   <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'Every time when a Payment confirmation is received and Order becomes Completed sent to Administrator', 'ihc' );
           break;
         case 'admin_user_profile_update':
           /// ADMIN - User Profile Update
             $template['subject'] = '{blogname}: User Update Profile';
             $template['content'] = '<html><head></head><body>
   <p><strong> User: {username}</strong> has updated his profile.</p>
   <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'Notification is sent to Administrator everytime member update his Account details via My Account page ', 'ihc' );
           break;
         case 'register_lite_send_pass_to_user':
             $template['subject'] = '{blogname}: Your Password';
             $template['content'] = '<html><head></head><body>
   <p>Hi {username}</p>
   <p>Your password for {blogname} is {NEW_PASSWORD}</p>
             </body></html>';
             $template['explanation'] = __( 'Notification is sent first time when member Register via Register Lite form or when password is not required during register step. A generated password is sent to member via Email.', 'ihc' );
           break;

         case 'ihc_cancel_subscription_notification-admin':
             $template['subject'] = '{blogname}: Subscription Canceled';
             $template['content'] = '<html><head></head><body>
   <p>{current_level} for {username} was canceled.</p>
             </body></html>';
             $template['explanation'] = __( 'When Member cancels a Subscription Administrator is notified via Email.', 'ihc' );
           break;
         case 'ihc_delete_subscription_notification-admin':
             $template['subject'] = '{blogname}: Subscription Deleted';
             $template['content'] = '<html><head></head><body>
   <p>{current_level} for {username} was deleted.</p>
             </body></html>';
             $template['explanation'] = __( 'When Member deletes a Subscription Administrator is notified via Email.', 'ihc' );
           break;
         case 'ihc_order_placed_notification-admin':
             $template['subject'] = '{blogname}: New Order placed';
             $template['content'] = '<html><head></head><body>
   <p>{username} has placed a new order.</p>
             </body></html>';
             $template['explanation'] = __( 'When a new Order is placed into the system Administrator is notified via Email. Orders will initially have Pending status.', 'ihc' );
           break;
         case 'ihc_new_subscription_assign_notification-admin':
             $template['subject'] = '{blogname}: New Subscription assigned';
             $template['content'] = '<html><head></head><body>
   <p><strong>{username}</strong> subscribed on {current_level} Subscription.</p>
             </body></html>';
             $template['explanation'] = __( 'Everytime when a new Subscription is assigned to member Administrator is notified. Subscription is set on Hold mode.', 'ihc' );
           break;
         case 'ihc_new_subscription_assign_notification':
               $template['subject'] = '{blogname}: New Subscription assigned to your Account';
               $template['content'] = '<html><head></head><body>
               <p>Hi {first_name} {last_name},</p><br/>
               <p>You have subscribed  <strong>{current_level}</strong>.</p><br/><br/>
               <p>Have a nice day!</p>
               </body></html>';
               $template['explanation'] = __( 'Everytime when a new Subscription is assigned Member is notified. Subscription is set on Hold mode.e', 'ihc' );
           break;
         case 'ihc_order_placed_notification-user':
             $template['subject'] = '{blogname}: New Order placed';
             $template['content'] = '<html><head></head><body>
             <p>Hi {first_name} {last_name},</p><br/>
             <p>You just placed a new order on <strong> {blogname} </strong>.</p><br/><br/>
             <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'When a new Order is placed into the system Member is notified via Email. Orders will initially have Pending status.', 'ihc' );
           break;
         case 'ihc_subscription_activated_notification':
             $template['subject'] = '{blogname}: Subscription Activated';
             $template['content'] = '<html><head></head><body>
             <p>Hi {first_name} {last_name},</p><br/>
   <p>Your subscription on <strong> {blogname} </strong> just got activated.</p><br/><br/>
   <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'Once a Subscription is Activated and receives a valid Expire Time, member is notified.', 'ihc' );
           break;
       case 'admin_user_subscription_first_time_activation':
           $template['subject'] = '{blogname}: Subscription Activated';
           $template['content'] = '<html><head></head><body>
     <p>Subscription for {username} has just got activated.</p>
     <p>Have a nice day!</p>
           </body></html>';
           $template['explanation'] = __( 'Administrator is notified when member Subscription is Activated first time.', 'ihc' );
             break;
         case 'ihc_delete_subscription_notification-user':
             $template['subject'] = '{blogname}: Subscription deleted';
             $template['content'] = '<html><head></head><body>
   <p>Hello {username}! One of Your subscriptioms on <strong> {blogname} </strong> was completely deleted.</p>
   <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'When Member deletes a Subscription is notified via Email.', 'ihc' );
           break;
         case 'ihc_cancel_subscription_notification-user':
             $template['subject'] = '{blogname}: Subscription cancel';
             $template['content'] = '<html><head></head><body>
   <p>Hello {username}! One of Your subscriptioms on <strong> {blogname} </strong> was canceled.</p>
   <p>Have a nice day!</p>
             </body></html>';
             $template['explanation'] = __( 'When Member cancels a Subscription is notified via Email.', 'ihc' );
           break;
         case 'drip_content-user':
           $template['subject'] = '{blogname}: A new Post has become available';
             $template['content'] = '<html><head></head><body>
   <p>Hello {username}! A new Post has become available. Check this out: {POST_LINK}</p>
             </body></html>';
             $template['explanation'] = __( 'When Drip Content Notifications module is enabled, members will be notified everytime a post becomes available. This process is handled by a WP Cron running daily.', 'ihc' );
           break;
         case 'user_enter_grace_period':
           $template['subject'] = '{blogname}: Subscription enter in Grace Period';
           $template['content'] = '<html><head></head><body>
   <p>Hi {first_name} {last_name},</p><br/>
   <p>Your <strong>{current_level}</strong> membership has just expired.</p><br/>
   <p>But you can still access our website without any problem.</p><br/>
   <p>If you want to renew/update your membership plan you can do that from My Account page.</p><br/><br/>
   <p>Have a nice day!</p>
   </body></html>';
           $template['explanation'] = __( 'After a Subscription expired and if a Grace Period is available, Member is notified that his grace period started. This process is handled by a WP Cron running periodically.', 'ihc' );
           break;
         case 'admin_user_enter_grace_period':
             $template['subject'] = '{blogname}: Subscription enter in Grace Period for {username}';
             $template['content'] = '<html><head></head><body>
   <p>Hi,</p><br/>
   <p>Customer <strong>{username}</strong> has entered in Grace Period for <strong>{current_level}</strong> membership. </p><br/><br/>
   <p>Have a nice day!</p>
   </body></html>
   ';
             $template['explanation'] = __( 'After a Subscription expired and if a Grace Period is available, Administrator is notified that member grace period started. This process is handled by a WP Cron running periodically.', 'ihc' );
             break;
         case 'admin_user_subscription_renew':
           $template['subject'] = '{blogname}: Renewal Processed Successfully for {username}';
           $template['content'] = '<<html><head></head><body>
   <p>Hi,</p><br/>
   <p><strong>{current_level}</strong> subscription for <strong>{username}</strong> customer has been extended.</p><br/><br/>
   <p>Have a nice day!</p>
   </body></html>
   ';
           $template['explanation'] = __( 'Administrator is notified when member Subscription is Renewed.', 'ihc' );
           break;
         case 'user_subscription_renew':
           $template['subject'] = '{blogname}: Renewal Processed Successfully!';
           $template['content'] = '<html><head></head><body>
   <p>Hi {first_name} {last_name},</p><br/>
   <p>Your <strong>{current_level}</strong>  subscription has been extended.</p><br/>
   <p>This ensures you have continued access to valuable content.</p><br/><br/>
   <p>Have a nice day!</p>
   </body></html>';
           $template['explanation'] = __( 'Member is notified when his Subscription is Renewed.', 'ihc' );
           break;
         case 'admin_user_login':
           $template['subject'] = '{blogname}: User {username} Login alert';
           $template['content'] = '<html><head></head><body>
   <p>Hi,</p><br/>
   <p>Customer <strong>{username}</strong> just logged into your Website. </p><br/><br/>
   <p>Have a nice day!</p>
   </body></html>
   ';
           $template['explanation'] = __( 'Administrator is notified everytime a member logs into current Website.', 'ihc' );
           break;
         case 'admin_before_subscription_payment_due':
           $template['subject'] = '{blogname}: Payment due for {username} soon';
           $template['content'] = '<html><head></head><body>
   <p>Hi,</p><br/>
   <p>Customer <strong>{username}</strong> will be automtically charged soon.</p><br/><br/>
   <p>Have a nice day!</p>
   </body></html>';
           $template['explanation'] = __( 'Before a member is gonna be charged, Administrator is notified via Email with several days before. This process is handled by a WP Cron running daily.', 'ihc' );
           break;
         case 'user_before_subscription_payment_due':
             $template['subject'] = '{blogname}: Your Renewal payment for {current_level}';
             $template['content'] = '<html><head></head><body>
   <p>Hi {first_name} {last_name},</p><br/>
   <p>We’re emailing to let you know that your renewal payment for {current_level} will be automatically processed soon.</p><br/>
   <p>If you are no longer using {current_level} and wish to cancel your subscription, you may do so at anytime from your My Account page before your renewal is processed.</p><br/><br/>
   <p>Have a nice day!</p>
   </body></html>
   ';
             $template['explanation'] = __( 'Before a he is gonna be charged, Member is notified via Email with several days before. This process is handled by a WP Cron running daily.', 'ihc' );
           break;
         case 'user_subscription_trial_expired':
           $template['subject'] = '{blogname}: Your Trial period for  {current_level} has Finished';
           $template['content'] = '<html><head></head><body>
     <p>Hi {first_name} {last_name},</p><br/>
     <p>Trial period for <strong>{current_level}</strong> has finished. </p><br/><br/>
     <p>Have a nice day!</p>
     </body></html>';
           $template['explanation'] = __( 'If Member has any Subscription with Trial and his trial period finished he will be notified. This process is handled by a WP Cron running periodically.', 'ihc' );
           break;
         case 'admin_user_subscription_trial_expired':
           $template['subject'] = '{blogname}: Subscription Trial period for {username} has Finished';
           $template['content'] = '<html><head></head><body>
     <p>Hi,</p><br/>
     <p>Customer <strong>{username}</strong> has finished Trial Period for <strong>{current_level}</strong> Subscription. </p><br/><br/>
     <p>Have a nice day!</p>
     </body></html>';
           $template['explanation'] = __( 'If Member has any Subscription with Trial and his trial period finished ADministrator will be notified. This process is handled by a WP Cron running periodically.', 'ihc' );
           break;
       }
       $template = apply_filters( 'ihc_admin_filter_notification_template', $template, $type );
       return $template;
    }

    public function getNotificationRuntime()
    {
      return [
          //Register Process
          'admin_user_register'                               => 'Instant', // - Admin Notification

          //Subscriptions
          'ihc_new_subscription_assign_notification-admin'    => 'Instant',
          'admin_user_subscription_first_time_activation'		  => 'Instant',
          'admin_user_subscription_renew'			                => 'Instant',
          'admin_user_subscription_trial_expired'			        => 'Every 12 hours',
          'admin_before_user_expire_level'                    => 'Daily',
          'admin_second_before_user_expire_level'             => 'Daily',
          'admin_third_before_user_expire_level'              => 'Daily',
          'admin_user_enter_grace_period'			                => 'Every 12 hours',
          'admin_user_expire_level'                           => 'Daily',

          //Payments
          'ihc_order_placed_notification-admin'               => 'Instant',
          'admin_user_payment'                                => 'Instant',
          'admin_before_subscription_payment_due'				      => 'Daily',

          //Customer Actions
          'admin_user_profile_update'                         => 'Instant',
          'admin_user_login'						                      => 'Instant',
          'ihc_cancel_subscription_notification-admin'        => 'Instant',
          'ihc_delete_subscription_notification-admin'        => 'Instant',


          //----------Users Notifications----------

          //Register Process
          'register'                                          => 'Instant', //Register
          'review_request'                                    => 'Instant', //register with pending

          //Register Lite
          'register_lite_send_pass_to_user'                   => 'Instant',

          //Double Email Verification
          'email_check'                                       => 'Instant',
          'email_check_success'                               => 'Instant',

          //Reset Password Process
          'reset_password_process'                            => 'Instant',
          'reset_password'                                    => 'Instant',
          'change_password'                                   => 'Instant',

          //Customer Account
          'approve_account'                                   => 'Instant',
          'delete_account'                                    => 'Instant',

          //Custom Actions
          'user_update' => 'Instant',
          'ihc_cancel_subscription_notification-user'         => 'Instant',
          'ihc_delete_subscription_notification-user'         => 'Instant',

          //Subscriptions
          'ihc_new_subscription_assign_notification'          => 'Instant',
          'ihc_subscription_activated_notification'           => 'Instant',
          'user_subscription_renew'			                      => 'Instant',
          'user_subscription_trial_expired'			              => 'Every 12 hours',
          'before_expire'                                     => 'Daily',
          'second_before_expire'                              => 'Daily',
          'third_before_expire'                               => 'Daily',
          'user_enter_grace_period'			                      => 'Every 12 hours',
          'expire'                                            => 'Daily',

          //Payments
          'ihc_order_placed_notification-user'                => 'Instant',
          'payment'                                           => 'Instant',
          'bank_transfer'                                     => 'Instant',
          'user_before_subscription_payment_due'				      => 'Daily',

          //Drip Content
          'drip_content-user'                                 => 'Daily',
        ];


    }

    public function getAllNotifications()
    {
        global $wpdb;
        $data = $wpdb->get_results("SELECT id,notification_type,level_id,subject,message,pushover_message,pushover_status,status FROM {$wpdb->prefix}ihc_notifications;" );
        return $data;
    }

    public function deleteOne( $id=0 )
    {
        global $wpdb;
        $q = $wpdb->prepare("DELETE FROM {$wpdb->prefix}ihc_notifications WHERE id=%d ", $id);
        $wpdb->query($q);
    }

    public function save( $post_data=array() )
    {
        global $wpdb;

        if(!isset($post_data['level_id'])) $post_data['level_id'] = -1;

        if (isset($post_data['notification_id'])){
        //update
        $q = $wpdb->prepare("UPDATE {$wpdb->prefix}ihc_notifications
                SET notification_type=%s,
                level_id=%s,
                subject=%s,
                message=%s,
                pushover_message=%s,
                pushover_status=%s
                WHERE id=%d
        ", $post_data['notification_type'], $post_data['level_id'], stripslashes_deep($post_data['subject']),
        stripslashes_deep($post_data['message']), $post_data['pushover_message'], $post_data['pushover_status'],
        $post_data['notification_id']);
        $wpdb->query($q);
        } else {
        //create
        $q = $wpdb->prepare("INSERT INTO {$wpdb->prefix}ihc_notifications
                                VALUES(null, %s, %d, %s, %s, %s, %s, '1')",
                                $post_data['notification_type'], $post_data['level_id'], stripslashes_deep($post_data['subject']), stripslashes_deep($post_data['message']),
                                $post_data['pushover_message'], $post_data['pushover_status']
        );
        $wpdb->query($q);
        }
        do_action( 'ihc_save_notification_action', $post_data );
    }

    /**
     * @param string
     * @return array
     */
    public static function getOneByType( $type='' )
    {
        global $wpdb;
        if ( $type == '' ){
            return [];
        }
        $query = $wpdb->prepare( "SELECT id,notification_type,level_id,subject,message,pushover_message,pushover_status,status
                                                      FROM {$wpdb->prefix}ihc_notifications
                                                      WHERE
                                                      notification_type=%s
                                                      ORDER BY id DESC LIMIT 1;", $type );
        $data = $wpdb->get_row( $query );
        if ( !$data ){
            return [];
        }
        return (array)$data;
    }
}
