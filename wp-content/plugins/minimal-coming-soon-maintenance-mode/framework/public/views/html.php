<?php

/**
 * Renders the html template for the plugin.
 *
 * @link       http://www.webfactoryltd.com
 * @since      1.0
 */




$fonts = '';
$baseFonts = array('Arial', 'Helvetica', 'Georgia', 'Times New Roman', 'Tahoma', 'Verdana', 'Geneva');
if (!in_array($options["header_font"], $baseFonts) || !in_array($options["secondary_font"], $baseFonts)) {
    $fonts .= '<script src="//ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js"></script>';
    $fonts .= '<script type="text/javascript">  WebFont.load( {  google: {  families: [';
    $fonts .= "'Karla',";

    if (!in_array($options["header_font"], $baseFonts)) {
        $fonts .= "'{$options["header_font"]}',";
    }
    if (!in_array($options["secondary_font"], $baseFonts)) {
        $fonts .= "'{$options["secondary_font"]}',";
    }
    if (!in_array($options["content_1col_font"], $baseFonts)) {
        $fonts .= "'{$options["content_1col_font"]}',";
    }
    if (!in_array($options["content_2col_font"], $baseFonts)) {
        $fonts .= "'{$options["content_2col_font"]}',";
    }
    $fonts .= '] }  } ); </script>';
}
$signals_sections = explode(',', $options['arrange']);

if ($options['block_se']) {
    $protocol = 'HTTP/1.0';
    if ($_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
        $protocol = 'HTTP/1.1';
    }

    header($protocol . ' 503 Service Unavailable', true, 503);
    header('Retry-After: 3600');
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <?php
    if ($options['block_se']) {
        echo '<meta name="robots" content="noindex,nofollow" />';
    }
    ?>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo stripslashes($options['description']); ?>">
    <?php
    if (false === csmm_get_rebranding()) {
        echo '<meta name="generator" content="Coming Soon & Maintenance Mode plugin for WordPress - https://comingsoonwp.com/">';
    }
    ?>

    <title><?php
            $title = stripslashes($options['title']);
            if (strpos($title, '%sitetitle%') !== false) {
                $title = str_replace('%sitetitle%', get_bloginfo('name'), $title);
            }
            if (strpos($title, '%sitetagline%') !== false) {
                $title = str_replace('%sitetagline%', get_bloginfo('description'), $title);
            }
            echo $title;
            ?></title>
    <?php if (isset($options['favicon']) && !empty($options['favicon'])) : ?>
        <link rel="shortcut icon" href="<?php echo esc_url_raw($options['favicon']); ?>" />
    <?php endif; ?>
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="stylesheet" type="text/css" href="<?php echo CSMM_URL; ?>/framework/public/css/public.css?v=<?php echo csmm_get_plugin_version(); ?>" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" type="text/css" media="all" />
    <script type="text/javascript" src="<?php echo includes_url('js/jquery/jquery.js'); ?>"></script>
    <?php
    if ($options['animation']) {
        echo '<link rel="stylesheet" type="text/css" href="' . CSMM_URL . '/framework/public/css/animate.min.css" />';
    }
    ?>
    <meta property="og:locale" content="<?php echo get_bloginfo('language'); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $title; ?>" />
    <meta property="og:description" content="<?php echo $options['description']; ?>" />
    <meta property="og:site_name" content="<?php echo $title; ?>" />
    <?php
    if ($options['social_preview']) {
        echo '<meta property="og:image" content="' . $options['social_preview'] . '" />';
    }
    ?>
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:description" content="<?php echo $options['description']; ?>" />
    <meta name="twitter:title" content="<?php echo $title; ?>" />
    <?php
    if ($options['twitter_site']) {
        echo '<meta property="twitter:site" content="' . $options['twitter_site'] . '" />';
        echo '<meta property="twitter:creator" content="' . $options['twitter_site'] . '" />';
    }
    if ($options['facebook_site']) {
        echo '<meta property="article:publisher" content="' . $options['facebook_site'] . '" />';
    }

    if ($options['block_se']) {
        echo '<meta name="robots" content="none" />';
    }
    echo $fonts;
    if (in_array('progressbar', $signals_sections) || in_array('countdown', $signals_sections) || $options['animation']) {
    ?>

    <?php
    }
    ?>
    <?php require_once CSMM_PATH . '/framework/public/include/styles.php'; ?>

    <?php

    // analytics
    if (csmm_convert_ga($options['analytics'])) {
        echo "<script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', '" . csmm_convert_ga($options['analytics']) . "', 'auto');
        ga('send', 'pageview');
      </script>";
    }

    echo $options['tracking_pixel'];
    echo stripslashes($options['custom_head_code']);
    //
    ?>
</head>

<body>

    <?php
    

    if ($options['background_type'] == 'video') {
        if (stripos($options['background_video_fallback'], 'undefined index') !== false) {
            $options['background_video_fallback'] = '';
        }
        if(strpos($options['background_video'], '?') !== false){
            $bg_video = explode('?', $options['background_video'], 2);
            $options['background_video'] = $bg_video[0];
        }
        echo '<div class="video-background"><div class="video-foreground ' . (!empty($options['background_video_fallback']) ? 'mobile-fallback' : '') . $options['background_video_filter'] . '" ' . (!empty($options['background_video_fallback']) ? 'style="background-image:url(' . $options['background_video_fallback'] . ');"' : '') . '>
        <iframe src="https://www.youtube.com/embed/' . $options['background_video'] . '?controls=0&amp;showinfo=0&amp;rel=0&amp;autoplay=1&loop=1&amp;mute=1&version=3&playlist=' . $options['background_video'] . '" frameborder="0"></iframe></div></div>';
    } else {
        echo '<div class="content_img_wrap' . $options['background_image_filter'] . '"></div>';
    }


    if (!csmm_check_password_locked()) {
    ?>

        <div class="maintenance-mode <?php if ($options['content_position'] == 'middle') echo 'middle';
                                        if ($options['content_position'] == 'bottom-center') echo 'bottom-center'; ?>">
            <div class="s-container">
                <div class="content<?php if ($options['animation']) {
                                        echo ' hidden animated ' . $options['animation'];
                                    } ?>">
                    <?php
                    // Logo
                    if (!empty($options['logo'])) {
                        $signals_arrange['logo'] = '<div class="logo-container mm-module">' . "\r\n";
                        if(!empty($options['logo_link_url'])){
                            $signals_arrange['logo'] .= '<a href="' . $options['logo_link_url'] . '">';
                        }
                        $signals_arrange['logo'] .= '<img title= "' . $options['logo_title'] . '" alt= "' . $options['logo_title'] . '" src="' . $options['logo'] . '" class="logo" />' . "\r\n";
                        if(!empty($options['logo_link_url'])){
                            $signals_arrange['logo'] .= '</a>';
                        }
                        $signals_arrange['logo'] .= '</div>' . "\r\n";
                    }

                    // Header text
                    if (!empty($options['header_text'])) {
                        $signals_arrange['header'] = '<div class="header-container mm-module"><h1 class="header-text">' . stripslashes(nl2br($options['header_text'])) . '</h1></div>' . "\r\n";
                    }

                    // Secondary text
                    if (!empty($options['secondary_text'])) {
                        $signals_arrange['content'] = '<div class="secondary-container mm-module">' . stripslashes(nl2br($options['secondary_text'])) . '</div>' . "\r\n";
                    }

                    // Content 2 column text
                    if (!empty($options['content_2col_text_left']) ||  !empty($options['content_2col_text_left'])) {
                        $signals_arrange['content2col'] = '<div class="content-2col-container mm-module">
                            <div class="content-2col-container-column">' . stripslashes(nl2br($options['content_2col_text_left'])) . '</div>
                            <div class="content-2col-container-column">' . stripslashes(nl2br($options['content_2col_text_right'])) . '</div>
                        </div>' . "\r\n";
                    }

                    // Divider
                    if (!empty($options['divider_height'])) {
                        $signals_arrange['divider'] = '<div class="divider mm-module"></div>' . "\r\n";
                    }

                    //MailPoet
                    if(csmm_is_mailpoet_active()){
                        $signals_arrange['mailpoet'] = '<div class="mailpoet mm-module">';

                        $mp_wp = new MailPoet\WP\Functions;
                        $mp_renderer = new MailPoet\Config\Renderer;
                        $assetsController = new MailPoet\Form\AssetsController($mp_wp, $mp_renderer, MailPoet\Settings\SettingsController::getInstance());
                        $signals_arrange['mailpoet'] .=  '<script type="text/javascript" src="' . MailPoet\Config\Env::$assetsUrl . '/dist/js/' . $mp_renderer->getJsAsset('form_preview.js') . '"></script>';
                        $signals_arrange['mailpoet'] .=  $assetsController->setupFrontEndDependencies();
                        $signals_arrange['mailpoet'] .=  $assetsController->printScripts();
                        $signals_arrange['mailpoet'] .=  '<link rel="stylesheet" href="' . MailPoet\Config\Env::$assetsUrl . '/dist/css/' . $mp_renderer->getCssAsset('mailpoet-public.css') . '" type="text/css" media="all" />';

                        global $wp_query;
                        global $wp_filter;
                        $wp_query->in_the_loop = true;
                        $wp_query->is_page = true;
                        foreach($wp_filter['the_content']->callbacks as $priority => $callbacks){
                            foreach($callbacks as $cid => $the_){
                                if(!is_array($the_['function'])){ 
                                    unset($wp_filter['the_content']->callbacks[$priority][$cid]);
                                    continue;
                                }

                                if(gettype($the_['function'][0]) != 'object'){
                                    unset($wp_filter['the_content']->callbacks[$priority][$cid]);
                                    continue;
                                }

                                
                                if(get_class($the_['function'][0]) !== 'MailPoet\Form\DisplayFormInWPContent'){
                                    unset($wp_filter['the_content']->callbacks[$priority][$cid]);
                                    continue;
                                }
                            }
                        }

                        $signals_arrange['mailpoet'] .=  apply_filters('the_content', '');
                        $signals_arrange['mailpoet'] .= '</div>' . "\r\n";
                    }

                    // Form
                    if (!empty($options['mailchimp_api']) && !empty($options['mailchimp_list'])) {
                    } // mailchimp_api && mailchimp_list


                    // Checking if the form is submitted or not
                    if (!empty($_POST['subscribe_submit'])) {
                        $debug = '';
                        // Processing begins
                        $signals_email = strip_tags($_POST['signals_email']);
                        $signals_name = '';
                        if (isset($_POST['signals_name'])) {
                            $signals_name = strip_tags($_POST['signals_name']);
                        }

                        $captcha = false;
                        if (isset($_POST['g-recaptcha-response'])) {
                            $captcha = $_POST['g-recaptcha-response'];
                        }
                        
                        if (!$captcha) {
                            $code         = 'danger';
                            $response     = 'Please check the the captcha form.';
                        }
                        
                        if ($options['recaptcha'] != 'disabled'){
                            $secretKey = $options['recaptcha_secret_key'];
                            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
                            $response = file_get_contents($url);
                            $responseKeys = json_decode($response, true);
                        }
                        
                        if ($options['recaptcha'] != 'disabled' && !$responseKeys['success']) {
                            $code         = 'danger';
                            $response     = 'Please check captcha';
                        } elseif ('' === $signals_email || !is_email($signals_email)) {
                            $code         = 'danger';
                            $response     = $options['message_noemail'];
                        } elseif ('' === $signals_name && isset($_POST['signals_name'])) {
                            $code         = 'danger';
                            $response     = $options['message_no_name'];
                        }
                        elseif (!empty($options['gdpr_text']) && empty($_POST['gdpr_consent'])) {
                            $code     = 'danger';
                            $response = $options['gdpr_error_text'];
                        }
                        else {
                            $signals_email = filter_var(strtolower(trim($signals_email)), FILTER_SANITIZE_EMAIL);
                            $signals_name = sanitize_text_field(trim($signals_name));

                            // mailchimp
                            if ($options['mail_system_to_use'] == 'mc') {
                                $debug .= 'Using MailChimp<br>';
                                if (is_email($signals_email)) {
                                    require_once CSMM_PATH . '/framework/admin/include/classes/class-mailchimp.php';
                                    if (strlen($options['mailchimp_api']) < 10) {
                                        $code         = 'danger';
                                        $response     = 'Error connecting to MailChimp!';
                                    } else {
                                        if ($options['signals_double_optin'] == '1') {
                                            $debug .= 'Double opt-in is enabled<br>';
                                            $out_array =  array(
                                                'email_address' => $signals_email,
                                                'merge_fields' => array('FNAME' => $signals_name),
                                                'status'        => 'pending',
                                            );
                                        } else {
                                            $debug .= 'Double opt-in is disabled<br>';
                                            $out_array =  array(
                                                'email_address' => $signals_email,
                                                'merge_fields' => array('FNAME' => $signals_name),
                                                'status'        => 'subscribed',
                                            );
                                        }

                                        $MailChimp = new csmm_MailChimp($options['mailchimp_api']);
                                        $api_url  = "/lists/" . $options['mailchimp_list'] . "/members";
                                        $debug .= 'Sending to endpoint ' . $api_url . '<br>';
                                        $debug .= 'Data ' . var_export($out_array, true) . '<br>';

                                        $result = $MailChimp->post($api_url, $out_array);
                                        $debug .= 'Result: ' . var_export($result, true);
                                        if ($result['status'] == 400) {
                                            $code         = 'danger';
                                            if ($result['title'] == 'Member Exists') {
                                                $response     = $options['message_subscribed'];
                                            } else {
                                                $response     = $result['detail'];
                                            }
                                        } elseif (isset($result['unique_email_id'])) {
                                            $code         = 'success';
                                            $response     = $options['message_done'];
                                        }
                                    }
                                } else { // is email
                                    $code             = 'danger';
                                    $response         = $options['message_noemail'];
                                }
                            } // mailchimp


                            // zapier
                            if ($options['mail_system_to_use'] == 'zapier') {
                                $debug .= 'Using Zapier<br>';
                                $fields = array();
                                $fields['name'] =    sanitize_text_field($_POST['signals_name']);
                                $fields['email'] =    sanitize_email($_POST['signals_email']);
                                $fields['site_name'] =  get_bloginfo('name');
                                $fields['site_url'] =   get_bloginfo('url');
                                $fields['user_ip'] =   $_SERVER['REMOTE_ADDR'];
                                $fields['user_ua'] =   $_SERVER['HTTP_USER_AGENT'];
                                $debug .= 'Endpoint: ' . var_export($options['signal_zapier_action_url'], true) . '<br>';
                                $debug .= 'Sending data: ' . var_export($fields, true) . '<br>';


                                $res =  csmm_zapier_send($fields);
                                if (!is_wp_error($res)) {
                                    $code         = 'success';
                                    $response     = $options['message_done'];
                                } else {
                                    $code         = 'danger';
                                    $response     = $options['message_wrong'];
                                }
                                $debug .= 'Response: ' . var_export($res, true);
                            }

                            // universal autoresponder
                            if ($options['mail_system_to_use'] == 'ua') {
                                $debug .= 'Using universal autoresponder<br>';
                                $fields = array();
                                $fields['name'] =    sanitize_text_field($_POST['signals_name']);
                                $fields['email'] =    sanitize_email($_POST['signals_email']);

                                $tmp =  csmm_autoresponder_send($fields, true);
                                $debug .= 'Endpoint: ' . var_export($tmp['url'], true) . '<br>';
                                $debug .= 'Sending data: ' . var_export($tmp['data'], true) . '<br>';

                                $res =  csmm_autoresponder_send($fields);
                                $debug .= 'Response: ' . var_export($res, true);
                                if (!is_wp_error($res)) {
                                    $code         = 'success';
                                    $response     = $options['message_done'];
                                } else {
                                    $code         = 'danger';
                                    $response     = $options['message_wrong'];
                                }
                            }
                        } // email ok
                    } // form submitted


                    // Subscription module
                    $signals_arrange['form'] = '<div class="subscription mm-module">';

                    if (isset($code) && isset($response)) {
                        $signals_arrange['form'] .= '<div class="csmm-alert csmm-alert-' . $code . '">' . $response . '</div>';
                    }

                    if (!empty($options['mail_debug'])) {
                        if (!empty($debug)) {
                            $signals_arrange['form'] .= '<div id="email-debug">' . $debug . '</div>';
                        } else {
                            $signals_arrange['form'] .= '<div id="email-debug">Debugging is enabled. Submit form data to see reponse details.</div>';
                        }
                    }

                    $signals_arrange['form'] .= '<form method="post" action="" id="subscribe-form" class="subscribe-form">';
                    if ($options['signals_show_name'] == '1') {
                        $signals_arrange['form'] .= '<input value="' . strip_tags(@$_POST['signals_name']) . '" type="text" name="signals_name" placeholder="' . esc_attr($options['signals_csmm_message_noname']) . '" >';
                    }

                    $signals_arrange['form'] .= '
							<input type="text" name="signals_email" value="' . strip_tags(@$_POST['signals_email']) . '" placeholder="' . esc_attr($options['input_text']) . '">';
                    if ($options['gdpr_text']) {
                        $signals_arrange['form'] .= '<div class="gdpr_consent"><input type="checkbox" value="1" name="gdpr_consent" id="gdpr_consent"> <label for="gdpr_consent">' . str_replace('[policy_popup]', '<a href="#" class="gdpr_popup_link">', str_replace('[/policy_popup]', '</a>', $options['gdpr_text'])) . '</label></div>';
                    }

                    if ($options['recaptcha'] == 'v2') {
                        $signals_arrange['form'] .= '<div class="g-recaptcha" data-sitekey="' . $options['recaptcha_site_key'] . '"></div>';
                    }

                    $signals_arrange['form'] .= '<div class="submit-wrapper" '.($options['recaptcha'] == 'v3'?' style="padding-top:10px;"':'').'>
                    <input type="submit" name="subscribe_submit_button" ';

                    if ($options['recaptcha'] == 'v3') {
                        $signals_arrange['form'] .= 'class="g-recaptcha" data-badge="inline" data-sitekey="' . $options['recaptcha_site_key'] . '"  data-callback="csmm_submit_subscribe_form" data-action="submit" ';
                    }

                    $signals_arrange['form'] .= ' value="' . esc_attr($options['button_text']) . '" />

                    </div>';

                    $signals_arrange['form'] .= '<input type="hidden" name="subscribe_submit" value="true" />';

                    $signals_arrange['form'] .= '</form>';

                    // antispam text
                    if (!empty($options['antispam_text'])) {
                        $signals_arrange['form'] .= '<p class="anti-spam">' . stripslashes($options['antispam_text']) . '</p>';
                    }

                    $signals_arrange['form'] .= '</div>';


                    // Checking if the form is submitted or not
                    if (!empty($_POST['contact_submit'])) {
                        $debug = '';
                        // Processing begins
                        $signals_email = strip_tags($_POST['csmm_contact_email']);
                        $signals_name = '';
                        if (isset($_POST['csmm_contact_name'])) {
                            $signals_name = strip_tags($_POST['csmm_contact_name']);
                        }

                        $signals_message = '';
                        if (isset($_POST['csmm_contact_message'])) {
                            $signals_message = strip_tags($_POST['csmm_contact_message']);
                        }

                        if (isset($_POST['g-recaptcha-response'])) {
                            $captcha = $_POST['g-recaptcha-response'];
                        } else {
                            $captcha = false;
                        }
                    
                        if ($options['recaptcha'] != 'disabled'){
                            if (!$captcha) {
                                $contact_code         = 'danger';
                                $contact_response     = 'Captcha incorrect.';
                            }

                            $secretKey = $options['recaptcha_secret_key'];
                            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
                            $response = file_get_contents($url);
                            $responseKeys = json_decode($response, true);
                        }
                        
                        if ($options['recaptcha'] != 'disabled' && !$responseKeys['success']) {
                            $contact_code         = 'danger';
                            $contact_response     = 'Please check captcha';
                        } elseif ('' === $signals_email || !is_email($signals_email)) {
                            $contact_code         = 'danger';
                            $contact_response     = $options['message_noemail'];
                        } elseif ('' === $signals_name && isset($_POST['csmm_contact_name'])) {
                            $contact_code         = 'danger';
                            $contact_response     = $options['message_no_name'];
                        } elseif ('' === $signals_message) {
                            $contact_code         = 'danger';
                            $contact_response     = $options['contact_message_text'];
                        }
                        elseif (!empty($options['contact_gdpr_text']) && empty($_POST['contact_gdpr_consent'])) {
                            $contact_code     = 'danger';
                            $contact_response = $options['contact_gdpr_error_text'];
                        }
                        else {
                            $signals_email = filter_var(strtolower(trim($signals_email)), FILTER_SANITIZE_EMAIL);
                            $signals_name = sanitize_text_field(trim($signals_name));
                            
                            if (wp_mail($options['contact_admin_email'], $options['contact_email_subject'], 'New message from: ' . $signals_name . ' (' . $signals_email . ')<br /><br />' . $signals_message, array('Content-Type: text/html; charset=UTF-8'))) {
                                $contact_code         = 'success';
                                $contact_response     = $options['message_done'];
                            } else {
                                $contact_code     = 'danger';
                                $contact_response = $options['message_wrong'];
                            }
                        } // email ok
                    } // contact form submitted

                    // Contact form module
                    $signals_arrange['contactform'] = '<div class="contact-form mm-module">';

                    if (isset($contact_code) && isset($contact_response)) {
                        $signals_arrange['contactform'] .= '<div class="csmm-alert csmm-alert-' . $contact_code . '">' . $contact_response . '</div>';
                    }

                    if (!empty($options['mail_debug'])) {
                        if (!empty($contact_debug)) {
                            $signals_arrange['contactform'] .= '<div id="contact-debug">' . $contact_debug . '</div>';
                        } else {
                            $signals_arrange['contactform'] .= '<div id="contact-debug">Debugging is enabled. Submit form data to see reponse details.</div>';
                        }
                    }

                    $signals_arrange['contactform'] .= '<form role="form" method="post" id="contact-form" class="contact-form">';
                    if ($options['contact_show_name'] == '1') {
                        $signals_arrange['contactform'] .= '<input value="' . strip_tags(@$_POST['csmm_contact_name']) . '" type="text" name="csmm_contact_name" placeholder="' . esc_attr($options['contact_message_noname']) . '" >';
                    }

                    $signals_arrange['contactform'] .= '<input type="text" name="csmm_contact_email" value="' . strip_tags(@$_POST['csmm_contact_email']) . '" placeholder="' . esc_attr($options['contact_input_text']) . '">';
                    $signals_arrange['contactform'] .= '<textarea type="text" name="csmm_contact_message" placeholder="' . esc_attr($options['contact_message_text']) . '">' . strip_tags(@$_POST['csmm_contact_message']) . '</textarea>';

                    if ($options['contact_gdpr_text']) {
                        $signals_arrange['contactform'] .= '<div class="gdpr_consent"><input type="checkbox" value="1" name="contact_gdpr_consent" id="contact_gdpr_consent"> <label for="gdpr_consent">' . str_replace('[policy_popup]', '<a href="#" class="contact_gdpr_popup_link">', str_replace('[/policy_popup]', '</a>', $options['contact_gdpr_text'])) . '</label></div>';
                    }

                    if ($options['recaptcha'] == 'v2') {
                        $signals_arrange['contactform'] .= '<div class="g-recaptcha" data-sitekey="' . $options['recaptcha_site_key'] . '"></div>';
                    }

                    $signals_arrange['contactform'] .= '<div class="submit-wrapper" '.($options['recaptcha'] == 'v3'?' style="padding-top:10px;"':'').'>
                    <input type="submit" name="submit_button" ';

                    if ($options['recaptcha'] == 'v3') {
                        $signals_arrange['contactform'] .= ' data-badge="inline" class="g-recaptcha" data-sitekey="' . $options['recaptcha_site_key'] . '"  data-callback="csmm_submit_contact_form"';
                    }

                    $signals_arrange['contactform'] .= 'data-action="submit" value="' . esc_attr($options['contact_button_text']) . '">

                    </div>';

                    $signals_arrange['contactform'] .= '<input type="hidden" name="contact_submit" value="true" />';

                    $signals_arrange['contactform'] .= '</form>';

                    // antispam text
                    

                    if ($options)

                        $signals_arrange['contactform'] .= '</div>';

                    // social icons
                    $tmp = '';
                    if (sizeof($options['social_list_url'])) {
                        $tmp .= '<div class="social-block mm-module"><ul>';
                        for ($i = 1; $i < count($options['social_list_url']); $i++) {
                            if (empty($options['social_list_url'][$i]) || empty($options['social_list_icon'][$i])) {
                                continue;
                            }
                            $tmp .= '<li class="icon-size-' . $options['icon_size'] . '"><a ' . (strpos($options['social_list_url'][$i], 'mailto') !== false ? '' : 'target="_blank"') . ' href="' . $options['social_list_url'][$i] . '">';
                            $tmp .= '<i data-icomoon="&#x' . dechex($options['social_list_icon'][$i]) . ';"></i>';
                            if (!empty($options['social_list_text'][$i])) {
                                $tmp .= '<span>' . $options['social_list_text'][$i] . '</span>';
                            }
                            $tmp .= '</a></li>';
                        }
                        $tmp .= '</ul></div>';
                    }
                    $signals_arrange['social'] = $tmp;


                    // map
                    $tmp = '';
                    $tmp .= '<div class="map-block mm-module">';
                    $tmp .= '<iframe frameborder="0" src="//www.google.com/maps/embed/v1/place?q=' . urlencode($options['map_address']) . '&maptype=roadmap&zoom=' . $options['map_zoom'] . '&key=' . $options['map_api_key'] . '">';
                    $tmp .= '</iframe></div>';
                    $signals_arrange['map'] = $tmp;

                    // video
                    $tmp = '';
                    $tmp .= '<div class="video-block mm-module"><div class="video-container">';
                    if ($options['video_type'] == 'youtube') {
                        $tmp .= '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $options['video_id'] . ($options['video_autoplay'] ? '?autoplay=1&' : '?') . ($options['video_minimal'] ? 'rel=0&controls=0&showinfo=0' : '') . ($options['video_mute'] ? '&mute=1' : '') . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>';
                    } elseif ($options['video_type'] == 'vimeo') {
                        $tmp .= '<iframe width="640" height="360" src="https://player.vimeo.com/video/' . $options['video_id'] . ($options['video_autoplay'] ? '?autoplay=1&' : '?') . ($options['video_minimal'] ? 'title=0&byline=0&portrait=0' : '') . ($options['video_mute'] ? '&mute=1' : '') . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe>';
                    }
                    else {
                        $tmp .= $options['video_embed_code'];
                    }
                    $tmp .= '</div></div>';
                    $signals_arrange['video'] = $tmp;

                    // progressbar
                    $tmp = '';
                    $tmp .= '<div class="progressbar-block mm-module">';
                    $tmp .= '<div id="progressbar">';
                    $tmp .= '</div></div>';
                    $signals_arrange['progressbar'] = $tmp;

                    // countdown
                    $tmp = '';
                    $tmp .= '<div class="countdown-block mm-module">';
                    $tmp .= '<div class="timer" id="countdown">';
                    $tmp .= '</div></div>';
                    $signals_arrange['countdown'] = $tmp;

                    // Custom HTML
                    $signals_arrange['html'] = '<div class="html-container mm-module">' . "\r\n";
                    $signals_arrange['html'] .= stripslashes($options['custom_html']);
                    $signals_arrange['html'] .= '</div>' . "\r\n";

                    foreach ($signals_sections as $signals_section) {
                        if (isset($signals_arrange[$signals_section])) {
                            echo $signals_arrange[$signals_section];
                        }
                    }

                    ?>

                </div><!-- .content -->
            </div><!-- .s-container -->
        </div><!-- .maintenance-mode -->
        <?php

        if ($options['recaptcha'] == 'v2' || $options['recaptcha'] == 'v3') {
            echo '<script src="https://www.google.com/recaptcha/api.js"></script>
                <script>
                function csmm_submit_contact_form(token) {
                    jQuery("#contact-form").submit();
                }

                function csmm_submit_subscribe_form(token) {
                    jQuery("#subscribe-form").submit();
                }';
            echo '</script>';
        }


        echo stripslashes($options['custom_foot_code']);
        if (in_array('progressbar', $signals_sections)) {
            echo '<script src="' . CSMM_URL . '/framework/public/js/progress.js" type="text/javascript"></script>';
            echo '<script type="text/javascript">jQuery(window).on("load", function(){ ';
            echo "jQuery('#progressbar').jQMeter({goal:'100',raised:'" . $options['progress_percentage'] . "',height:'" . $options['progress_height'] . "px',bgColor:'#" . $options['progress_color'] . "30',barColor:'#" . $options['progress_color'] . "',animationSpeed:1500}); ";
            echo '});</script>';
        }
        if (in_array('countdown', $signals_sections)) {
            $date = date('F j, Y H:G', strtotime($options['countdown_date']));
            echo '<script src="' . CSMM_URL . '/framework/public/js/timezz.js" type="text/javascript"></script>';
            echo '<script type="text/javascript">jQuery(document).ready(function(){ ';
            echo "new TimezZ('#countdown', { stop: false, tagNumber: 'span', tagLetter: 'i', text:{days: '{$options['countdown_days']}', hours: '{$options['countdown_hours']}', minutes: '{$options['countdown_minutes']}', seconds: '{$options['countdown_seconds']}'}, date: '{$date}', daysName: '{$options['countdown_days']}', hoursName: '{$options['countdown_hours']}', minutesName: '{$options['countdown_minutes']}', secondsName: '{$options['countdown_seconds']}' }); ";
            echo '});</script>';
        }
        if ($options['animation']) {
            echo '<script type="text/javascript">jQuery(window).on("load", function(){ jQuery(".content").removeClass("hidden"); ';
            echo '});</script>';
        }
        if (strlen($options['gdpr_policy_text']) > 0) {
            echo '<div class="csmm_gdpr_popup csmm_form_gdpr_popup" style="display:none;"><div class="gdpr_popup_close"><i class="icomoon-close"></i></div><div class="gdpr_popup_policy">' . $options['gdpr_policy_text'] . '</div></div>';
            echo '<script type="text/javascript">    
            jQuery(window).on("load", function(){ 
                jQuery(".gdpr_popup_link").on("click",function(e){
                    e.stopPropagation();
                    jQuery(".csmm_form_gdpr_popup").show();
                });
            });</script>';
        }



        if (strlen($options['contact_gdpr_policy_text']) > 0) {
            echo '<div class="csmm_gdpr_popup csmm_contact_gdpr_popup" style="display:none;"><div class="gdpr_popup_close"><i class="icomoon-close"></i></div><div class="gdpr_popup_policy">' . $options['contact_gdpr_policy_text'] . '</div></div>';
            echo '<script type="text/javascript">    
            jQuery(window).on("load", function(){ 
                jQuery(".contact_gdpr_popup_link").on("click",function(e){
                    e.stopPropagation();
                    jQuery(".csmm_contact_gdpr_popup").show();
                });
            });</script>';
        }

        if (strlen($options['gdpr_policy_text']) > 0 || strlen($options['contact_gdpr_policy_text']) > 0) {
            echo '<script type="text/javascript">
            jQuery(document).on("click",function(event) { 
                $target = jQuery(event.target);
                
                if(!$target.closest(".csmm_gdpr_popup").length && jQuery(".csmm_gdpr_popup").is(":visible")){
                    jQuery(".csmm_gdpr_popup").hide();
                }
            });

            jQuery(document).on("click",".gdpr_popup_close", function(event) { 
                jQuery(".csmm_gdpr_popup").hide();
            });</script>';
        }
    } // end password locked 

    global $signals_is_preview;

    if (csmm_check_password_locked() || csmm_check_direct_access_locked()) {
        ?>
        <script>
            jQuery(document).ready(function() {

                var hash = location.hash;
                var csmm_pass_form_visible = <?php echo csmm_check_password_locked() ? 'true' : 'false'; ?>;

                jQuery(document).on('hashchange', function() {
                    if (location.hash == '#access-site-form') {
                        jQuery('.content_img_wrap').addClass('csmm-blur');
                        jQuery('.video-background').addClass('csmm-blur');
                        jQuery('.maintenance-mode').addClass('csmm-blur');
                        csmm_pass_form_visible = true;
                        jQuery('#csmm-access-form').fadeIn(500);
                    }
                });


                if (<?php echo csmm_check_password_locked() ? 'true' : 'false'; ?> || hash == '#access-site-form') {
                    jQuery('.content_img_wrap').addClass('csmm-blur');
                    jQuery('.video-background').addClass('csmm-blur');
                    jQuery('.maintenance-mode').addClass('csmm-blur');

                    csmm_pass_form_visible = true;
                    jQuery('#csmm-access-form').fadeIn(500);
                }

                jQuery('#csmm-access-show-form').on('click', function(e) {
                    e.stopPropagation();
                    jQuery('.content_img_wrap').addClass('csmm-blur');
                    jQuery('.video-background').addClass('csmm-blur');
                    jQuery('.maintenance-mode').addClass('csmm-blur');
                    jQuery('#csmm-access-form').fadeIn(500);
                    csmm_pass_form_visible = true;

                });

                jQuery('#csmm-access-password').on('keypress', function(e) {
                    if (e.which == 13) {
                        check_csmm_pass();
                    }
                });

                jQuery('#csmm-access-check-password').on('click', function() {
                    check_csmm_pass();
                });

                jQuery(document).click(function(event) {
                    $target = jQuery(event.target);
                    if (!$target.closest('#csmm-access-form').length && csmm_pass_form_visible) {
                        csmm_pass_form_visible = false;
                        jQuery('#csmm-access-form').fadeOut(500);
                        jQuery('.content_img_wrap').removeClass('csmm-blur');
                        jQuery('.video-background').removeClass('csmm-blur');
                        jQuery('.maintenance-mode').removeClass('csmm-blur');
                    }
                });

                function check_csmm_pass() {
                    if (jQuery('#csmm-access-password').val().length < 4) {
                        jQuery('#csmm-access-response').hide();
                        jQuery('#csmm-access-response').html('<span><?php echo $options['login_wrong_password_text']; ?></span>');
                        jQuery('#csmm-access-response').fadeIn(500).delay(2000).fadeOut(500);
                    } else {
                        jQuery.ajax({
                            url: "<?php echo admin_url('admin-ajax.php'); ?>",
                            method: 'POST',
                            crossDomain: true,
                            dataType: 'json',
                            data: {
                                action: 'csmm_check_login',
                                pass: jQuery('#csmm-access-password').val()
                            },
                            success:function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                jQuery('#csmm-access-response').hide();
                                jQuery('#csmm-access-response').html('<span>' + response.data + '</span>');
                                jQuery('#csmm-access-response').fadeIn(500).delay(2000).fadeOut(500);
                            }
                            },
                            error:function(type) {
                                jQuery('#csmm-access-response').hide();
                                jQuery('#csmm-access-response').html('<span>An error occured! Please try again later</span>');
                                jQuery('#csmm-access-response').fadeIn(500).delay(2000).fadeOut(500);
                            }
                        });
                    }
                }
            });
        </script>

    <?php

        if ($options['password_button'] == '1') {
            echo '<div title="' . (isset($options['login_button_tooltip']) ? $options['login_button_tooltip'] : 'Direct Access login') . '" id="csmm-access-show-form"><i class="fas fa-unlock-alt"></i></div>';
        }

        echo '<div id="csmm-access-form" ' . (csmm_check_password_locked() ? '' : 'style="display:none;"') . '>';

        if ($options['login_message'] != 'Please enter the password to access the site') {
            echo $options['login_message'];
        } else {
            if (csmm_check_direct_access_locked() && csmm_check_password_locked()) {
                echo 'Please enter the password to access the site';
            } else {
                echo 'Please enter the password to access the full version of the site';
            }
        }

        echo '<br /><div id="csmm-access-response"></div><input type="password" id="csmm-access-password" /><input type="submit" id="csmm-access-check-password" value="' . $options['login_button_text'] . '" /></div>';
    } else if (isset($signals_is_preview) && $signals_is_preview === true) {
        if ($options['password_button'] == '1' || $options['login_button'] == '1') {
            echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" type="text/css" media="all" />';
        }

        if ($options['password_button'] == '1') {
            echo '<div title="' . (isset($options['login_button_tooltip']) ? $options['login_button_tooltip'] : 'Direct Access login') . '" id="csmm-access-show-form"><i class="fas fa-unlock-alt"></i></div>';
        }
    }
    if ($options['login_button'] == '1') {
        if (is_user_logged_in()) {
            echo '<a title="' . (isset($options['wplogin_button_tooltip']) ? $options['wplogin_button_tooltip'] : 'Access WordPress admin') . '" href="' . get_site_url() . '/wp-admin/" id="csmm-login-page-link"><i class="fab fa-wordpress"></i></a>';
        } else {
            $login_link = wp_login_url();

            if (!empty($options['custom_login_url'])) {
                $login_link = $options['custom_login_url'];
            }
            echo '<a title="' . (isset($options['wplogin_button_tooltip']) ? $options['wplogin_button_tooltip'] : 'Access WordPress admin') . '" title="Log in to WordPress admin" href="' . $login_link . '" id="csmm-login-page-link"><i class="fab fa-wordpress"></i></a>';
        }
    }
    ?>
</body>

</html>