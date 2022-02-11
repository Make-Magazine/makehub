<?php

/**
 * Renders the blank template for the plugin.
 *
 * @link       http://www.webfactoryltd.com
 * @since      1.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $options['title']; ?></title>
    <?php if (!empty($options['favicon'])) : ?>
        <link rel="shortcut icon" href="<?php echo esc_url_raw($options['favicon']); ?>" />
    <?php endif; ?>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo CSMM_URL; ?>/framework/public/css/basic.css" />
    <script src='//ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js'></script>
    <script>
        WebFont.load({
            google: {
                families: ['<?php echo $options["header_font"]; ?>', '<?php echo $options["secondary_font"]; ?>']
            }
        });
    </script>
    <?php

    // user defined css for the blank mode
    if (!empty($options['custom_css'])) {
        echo '<style>';
        echo stripslashes($options['custom_css']);
        echo '</style>';
    }

    ?>
    <?php echo stripslashes($options['custom_head_code']); ?>
</head>

<body>
    <?php

    // Custom html
    // Nothing else will be included here since we are serving a blank template
    $custom_html = stripslashes($options['custom_html_layout']);

    // form
    if (!empty($custom_html) && false !== strpos($custom_html, '{{form}}')) {
        if (!empty($options['mailchimp_api']) && !empty($options['mailchimp_list'])) {
            // Checking if the form is submitted or not
            // Checking if the form is submitted or not
            if (isset($_POST['signals_email'])) {
                // Processing begins
                $signals_email = strip_tags($_POST['signals_email']);
                $signals_name = strip_tags($_POST['signals_name']);




                if ('' === $signals_email) {
                    $code         = 'danger';
                    $response     = $options['message_noemail'];
                } elseif ('' === $signals_name && isset($_POST['signals_name'])) {
                    $code         = 'danger';
                    $response     = $options['signals_csmm_message_no_name'];
                } else {
                    $signals_email = filter_var(strtolower(trim($signals_email)), FILTER_SANITIZE_EMAIL);
                    $signals_name = sanitize_text_field(trim($signals_name));


                    // procesing email send
                    // mailchimp
                    if ($options['mail_system_to_use'] == 'mc') {
                        if (strpos($signals_email, '@')) {
                            require_once CSMM_PATH . '/framework/admin/include/classes/class-mailchimp.php';


                            if ($options['signals_double_optin'] == '1') {
                                $out_array =  [
                                    'email_address' => $signals_email,
                                    'merge_fields' => ['FNAME' => $signals_name],
                                    'status'        => 'pending',
                                ];
                            } else {
                                $out_array =  [
                                    'email_address' => $signals_email,
                                    'merge_fields' => ['FNAME' => $signals_name],
                                    'status'        => 'subscribed',
                                ];
                            }

                            $MailChimp = new MailChimp($options['mailchimp_api']);
                            $api_url  = "/lists/" . $options['mailchimp_list'] . "/members";


                            $result = $MailChimp->post($api_url, $out_array);
                            /*
						// adding to list
						if( substr_count( $options['mailchimp_list'], '|' ) > 0 ){
							$tmp = explode( '|', $options['mailchimp_list'] );
							$api_url = "/lists/".trim($tmp[0])."/segments/".trim($tmp[1])."/members";
							$result = $MailChimp->post( $api_url, $out_array );
						}
					 */

                            if ($result['status'] == 400) {
                                $code         = 'danger';
                                $response     = $result['detail'];
                            } elseif (isset($result['unique_email_id'])) {
                                $code         = 'success';
                                $response     = $options['message_done'];
                            }
                        } else {
                            $code             = 'danger';
                            $response         = $options['message_noemail'];
                        }
                    }

                    // zapier
                    if ($options['mail_system_to_use'] == 'zapier') {
                        $fields = array();
                        $fields['name'] =    sanitize_text_field($_POST['signals_name']);
                        $fields['email'] =    sanitize_email($_POST['signals_email']);

                        $res =  mcsm_zapier_send($fields);
                        if (!is_wp_error($res)) {
                            $code         = 'success';
                            $response     = $options['message_subscribed'];
                        } else {
                            $code         = 'danger';
                            $response     = $options['message_wrong'];
                        }
                    }

                    // universal autoresponder
                    if ($options['mail_system_to_use'] == 'ua') {
                        $fields = array();
                        $fields['name'] =    sanitize_text_field($_POST['signals_name']);
                        $fields['email'] =    sanitize_email($_POST['signals_email']);


                        $res =  mcsm_autoresponder_send($fields);
                        if (!is_wp_error($res)) {
                            $code         = 'success';
                            $response     = $options['message_subscribed'];
                        } else {
                            $code         = 'danger';
                            $response     = $options['message_wrong'];
                        }
                    }
                }
            } // signals_email

            // Subscription form
            // Displaying errors as well if they are set
            $subscription_form = '<div class="subscription ">';

            if (isset($code) && isset($response)) {
                $subscription_form .= '<div class="csmm-alert csmm-alert-' . $code . '">' . $response . '</div>';
            }

            $subscription_form .= '<form role="form" method="post">';

            if ($options['signals_show_name'] == '1') {
                $subscription_form .= '<input type="text" name="signals_name" placeholder="' . esc_attr($options['signals_csmm_message_noname']) . '" >';
            }


            $subscription_form .= '
					<input type="text" name="signals_email" placeholder="' . esc_attr($options['input_text']) . '">
					<input type="submit" name="submit" value="' . esc_attr($options['button_text']) . '">
				</form>';
            $subscription_form .= '</div>';

            // Replacing the form placeholder
            $custom_html = str_replace('{{form}}', $subscription_form, $custom_html);
        } // mailchimp_api && mailchimp_list
    } // custom_html

    // Output the user defined html
    echo $custom_html;

    ?>

    <?php
    if (false === csmm_get_rebranding()) {
        echo '<!-- Coming Soon plugin by WebFactory Ltd (http://www.webfactoryltd.com) -->';
    }
    ?>

    <?php echo stripslashes($options['custom_foot_code']); ?>
</body>

</html>