<?php

/**
 * Required styles for the plugin.
 *
 * @link       http://www.webfactoryltd.com
 * @since      1.0
 */

echo '<style>' . "\r\n";

// Background cover
if (!empty($options['bg_cover'])) {
    echo '
	.content_img_wrap{
		background-image: url("' . $options['bg_cover'] . '");
		width: 100%;
		height: 100%;
		position: fixed;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;';
    if (!empty($options['background_size_opt'])) {
        echo 'background-size: ' . $options['background_size_opt'] . ';';
    }
    if (!empty($options['background_position'])) {
        echo 'background-position: ' . $options['background_position'] . ';';
    }
    echo '
	}' . "\r\n";
}

// Background color
if (!empty($options['bg_color'])) {
    echo 'body { background-color:#' . $options['bg_color'] . ';}' . "\r\n";
}

// Link color
if (!empty($options['link_color']) || !empty($options['link_hover_color'])) {
    // link color
    if (!empty($options['link_color'])) {
        echo 'a { color:#' . $options['link_color'] . ';}' . "\r\n";
    }

    // link hover color
    if (!empty($options['link_hover_color'])) {
        echo 'a:hover { color:#' . $options['link_hover_color'] . ';}' . "\r\n";
    }
}


// Header: font, size, and color
if (!empty($options['header_font']) || !empty($options['header_font_size']) || !empty($options['header_font_color'])) {
    echo '.header-text{';

    // header font
    if (!empty($options['header_font'])) {
        echo 'font-family:"' . $options['header_font'] . '", Arial, sans-serif;';
    }

    // header font size
    if (!empty($options['header_font_size'])) {
        echo 'font-size:' . $options['header_font_size'] . 'px;';
    }

    // header font color
    if (!empty($options['header_font_color'])) {
        echo 'color:#' . $options['header_font_color'] . ';';
    }

    echo '}' . "\r\n";
}


// Secondary: font, size, and color
if (!empty($options['secondary_font']) || !empty($options['secondary_font_size']) || !empty($options['secondary_font_color'])) {
    echo '.gdpr_consent, .secondary-container {';

    // secondary font
    if (!empty($options['secondary_font'])) {
        echo 'font-family:"' . $options['secondary_font'] . '", Arial, sans-serif;';
    }

    // secondary font size
    if (!empty($options['secondary_font_size'])) {
        echo 'font-size:' . $options['secondary_font_size'] . 'px;';
    }

    // secondary font color
    if (!empty($options['secondary_font_color'])) {
        echo 'color:#' . $options['secondary_font_color'] . ';';
    }

    echo '}' . "\r\n";
}

// 1 Column: font, size, and color
if (!empty($options['content_1col_font']) || !empty($options['content_1col_font_size']) || !empty($options['content_1col_font_color'])) {
    echo '.secondary-container {';

    // secondary font
    if (!empty($options['content_1col_font'])) {
        echo 'font-family:"' . $options['content_1col_font'] . '", Arial, sans-serif;';
    }

    // secondary font size
    if (!empty($options['content_1col_font_size'])) {
        echo 'font-size:' . $options['content_1col_font_size'] . 'px;';
    }

    // secondary font color
    if (!empty($options['content_1col_font_color'])) {
        echo 'color:#' . $options['content_1col_font_color'] . ';';
    }

    echo '}' . "\r\n";
}

// 2 Column: font, size, and color
if (!empty($options['content_2col_font']) || !empty($options['content_2col_font_size']) || !empty($options['content_2col_font_color'])) {
    echo '.content-2col-container {';

    // secondary font
    if (!empty($options['content_2col_font'])) {
        echo 'font-family:"' . $options['content_2col_font'] . '", Arial, sans-serif;';
    }

    // secondary font size
    if (!empty($options['content_2col_font_size'])) {
        echo 'font-size:' . $options['content_2col_font_size'] . 'px;';
    }

    // secondary font color
    if (!empty($options['content_2col_font_color'])) {
        echo 'color:#' . $options['content_2col_font_color'] . ';';
    }

    echo '}' . "\r\n";


    // vertical divider width
    if (!empty($options['content_2col_divider_width'])) {
        echo '.content-2col-container .content-2col-container-column:first-child{';
        echo 'border-right:' . $options['content_2col_divider_width'] . 'px solid #' . $options['content_2col_divider_color'] . ';';
        echo '}' . "\r\n";
    }

    // column padding
    if (!empty($options['content_2col_padding'])) {
        echo '.content-2col-container .content-2col-container-column{';
        echo 'padding:' . $options['content_2col_padding'] . 'px;';
        echo '}' . "\r\n";
    }
}


// Divider: color, height, margin
if (!empty($options['divider_height']) || !empty($options['divider_color']) || !empty($options['divider_margin_top']) || !empty($options['divider_margin_bottom'])) {
    echo '.mm-module.divider {';

    // color
    if (!empty($options['divider_color'])) {
        echo 'background-color:#' . $options['divider_color'] . ';';
    }

    // height
    if (!empty($options['divider_height'])) {
        echo 'height:' . $options['divider_height'] . 'px;';
    }

    // margin top
    if (!empty($options['divider_margin_top'])) {
        echo 'margin-top:' . $options['divider_margin_top'] . 'px;';
    }

    // margin bottom
    if (!empty($options['divider_margin_bottom'])) {
        echo 'margin-bottom:' . $options['divider_margin_bottom'] . 'px;';
    }

    echo '}' . "\r\n";
}


// Antispam: font, size, and color
// We apply secondary font family to antispam as well
if (!empty($options['secondary_font']) || !empty($options['antispam_font_size']) || !empty($options['antispam_font_color'])) {
    echo '.anti-spam{';

    // secondary font
    if (!empty($options['secondary_font'])) {
        echo 'font-family:"' . $options['secondary_font'] . '", Arial, sans-serif;';
    }

    // antispam font size
    if (!empty($options['antispam_font_size'])) {
        echo 'font-size:' . $options['antispam_font_size'] . 'px;';
    }

    // antispam font color
    if (!empty($options['antispam_font_color'])) {
        echo 'color:#' . $options['antispam_font_color'] . ';';
    }

    echo '}' . "\r\n";
}


// Content: width, position, and alignment
if (!empty($options['content_overlay']) || !empty($options['content_width']) || !empty($options['content_position'])) {


    // content overlay for background images
    if ('1' == $options['content_overlay']) {
        if ('1' == $options['content_overlay_mobile']) {
            echo '@media only screen and (max-width: 992px) {';
        }
        echo '.content{';
        $hex = "#" . $options['overlay_color'];
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        $opacity = (int)$options['transparency_level'] / 100;

        echo 'padding:30px;border-radius:10px;box-shadow:0 0 10px 0 rgba(0, 0, 0, 0.33); background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $opacity . '); ';


        $hex = "#" . $options['overlay_color'];
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        echo '}';
        if ('1' == $options['content_overlay_mobile']) {
            echo '}';
        }
    }


    echo '.content{';

    // content width
    if (!empty($options['content_width'])) {
        // Making sure the width is not < 100 and not > 1170
        if ($options['content_width'] < 100 || $options['content_width'] > 1170) {
            $options['content_width'] = '1170';
        }

        echo 'max-width:' . $options['content_width'] . 'px;';
    }

    // content position
    if (!empty($options['content_position'])) {
        if ('center' == $options['content_position']) {
            echo 'margin: 0 auto 0 auto;';
        } elseif ('right' == $options['content_position']) {
            echo 'margin: 0 0 0 auto;';
        } elseif ('left' == $options['content_position']) {
            echo 'margin: 0 auto 0 0;';
        }
    }
    echo '}' . "\r\n";
}
if ($options['content_position'] == 'middle') {
    echo '.middle { max-width: ' . $options['content_width'] . 'px }';
}

// logo
echo '.logo { max-height: ' . $options['logo_max_height'] . 'px; }';

// submit button
echo '.submit-wrapper { text-align: ' . $options['submit_align'] . ';}';


// If the default form & button styles need to be ignored
if ('1' == $options['ignore_form_styles']) {
    // Input: size, color, background, border
    if (!empty($options['input_font_size']) || !empty($options['input_font_color']) || !empty($options['input_bg']) || !empty($options['input_border'])) {
        echo '.content input[type="text"], .content textarea{';

        // input font size
        if (!empty($options['input_font_size'])) {
            echo 'font-size:' . $options['input_font_size'] . 'px;';
        }

        // input color
        if (!empty($options['input_font_color'])) {
            echo 'color:#' . $options['input_font_color'] . ';';
        }

        // input background
        if (!empty($options['input_bg'])) {
            echo 'background:#' . $options['input_bg'] . ';';
        }

        // input border
        if (!empty($options['input_border'])) {
            echo 'border:1px solid #' . $options['input_border'] . ';';
        }

        echo '}' . "\r\n";
    }

    // Input: background:focus, border:focus
    if (!empty($options['input_bg_hover']) || !empty($options['input_border_hover'])) {
        echo '.content input[type="text"]:focus{';

        // input background:focus
        if (!empty($options['input_bg_hover'])) {
            echo 'background:#' . $options['input_bg_hover'] . ';';
        }

        // input border:focus
        if (!empty($options['input_border_hover'])) {
            echo 'border:1px solid #' . $options['input_border_hover'] . ';';
        }

        echo '}' . "\r\n";
    }

    // Button: size, color, background, border
    if (!empty($options['button_font_size']) || !empty($options['button_font_color']) || !empty($options['button_bg']) || !empty($options['button_border'])) {
        echo '.content input[type="submit"]{';

        // button font size
        if (!empty($options['button_font_size'])) {
            echo 'font-size:' . $options['button_font_size'] . 'px;';
        }

        // button color
        if (!empty($options['button_font_color'])) {
            echo 'color:#' . $options['button_font_color'] . ';';
        }

        // button background
        if (!empty($options['button_bg'])) {
            echo 'background:#' . $options['button_bg'] . ';';
        }

        // button border
        if (!empty($options['button_border'])) {
            echo 'border:1px solid #' . $options['button_border'] . ';';
        }

        echo '}' . "\r\n";
    }

    // Button: background:focus, border:focus
    if (!empty($options['button_bg_hover']) || !empty($options['button_border_hover'])) {
        echo '.content input[type="submit"]:hover,';
        echo '.content input[type="submit"]:focus{';

        // input background:focus
        if (!empty($options['button_bg_hover'])) {
            echo 'background:#' . $options['button_bg_hover'] . ';';
        }

        // input border:focus
        if (!empty($options['button_border_hover'])) {
            echo 'border:1px solid #' . $options['button_border_hover'] . ';';
        }

        echo '}' . "\r\n";
    }

    // Message: success
    if (!empty($options['success_background']) || !empty($options['success_color'])) {
        echo '.csmm-alert-success{';

        // success background
        if (!empty($options['success_background'])) {
            echo 'background:#' . $options['success_background'] . ';';
        }

        // success color
        if (!empty($options['success_color'])) {
            echo 'color:#' . $options['success_color'] . ';';
        }

        echo '}' . "\r\n";
    }

    // Message: error
    if (!empty($options['error_background']) || !empty($options['error_color'])) {
        echo '.csmm-alert-danger{';

        // error background
        if (!empty($options['error_background'])) {
            echo 'background:#' . $options['error_background'] . ';';
        }

        // error color
        if (!empty($options['error_color'])) {
            echo 'color:#' . $options['error_color'] . ';';
        }

        echo '}' . "\r\n";
    }

    echo '.mm-module{';
    if (!empty($options['module_margin'])) {
        echo 'margin: ' . $options['module_margin'] . 'px 0px;';
    }
    echo '}';

    echo '::-webkit-input-placeholder {
  color: #' . $options['form_placeholder_color'] . ';
}
::-moz-placeholder {
  color: #' . $options['form_placeholder_color'] . ';
}
:-ms-input-placeholder {
  color: #' . $options['form_placeholder_color'] . ';
}
:-moz-placeholder {
  color: #' . $options['form_placeholder_color'] . ';
}';
} // custom input styles







// If the default form & button styles need to be ignored
if ('1' == $options['contact_ignore_styles']) {
    // Input: size, color, background, border
    if (!empty($options['contact_input_size']) || !empty($options['contact_input_color']) || !empty($options['contact_input_bg']) || !empty($options['contact_input_border'])) {
        echo '.content .contact-form input[type="text"], .content .contact-form textarea{';

        // input font size
        if (!empty($options['contact_input_size'])) {
            echo 'font-size:' . $options['contact_input_size'] . 'px;';
        }

        // input color
        if (!empty($options['contact_input_color'])) {
            echo 'color:#' . $options['contact_input_color'] . ';';
        }

        // input background
        if (!empty($options['contact_input_bg'])) {
            echo 'background:#' . $options['contact_input_bg'] . ';';
        }

        // input border
        if (!empty($options['contact_input_border'])) {
            echo 'border:1px solid #' . $options['contact_input_border'] . ';';
        }

        echo '}' . "\r\n";
    }

    // Input: background:focus, border:focus
    if (!empty($options['contact_input_bg_hover']) || !empty($options['contact_input_border_hover'])) {
        echo '.content .contact-form input[type="text"]:focus{';

        // input background:focus
        if (!empty($options['contact_input_bg_hover'])) {
            echo 'background:#' . $options['contact_input_bg_hover'] . ';';
        }

        // input border:focus
        if (!empty($options['contact_input_border_hover'])) {
            echo 'border:1px solid #' . $options['contact_input_border_hover'] . ';';
        }

        echo '}' . "\r\n";
    }

    // Button: size, color, background, border
    if (!empty($options['contact_button_size']) || !empty($options['contact_button_color']) || !empty($options['contact_button_bg']) || !empty($options['contact_button_border'])) {
        echo '.content .contact-form input[type="submit"]{';

        // button font size
        if (!empty($options['contact_button_size'])) {
            echo 'font-size:' . $options['contact_button_size'] . 'px;';
        }

        // button color
        if (!empty($options['contact_button_color'])) {
            echo 'color:#' . $options['contact_button_color'] . ';';
        }

        // button background
        if (!empty($options['contact_button_bg'])) {
            echo 'background:#' . $options['contact_button_bg'] . ';';
        }

        // button border
        if (!empty($options['contact_button_border'])) {
            echo 'border:1px solid #' . $options['contact_button_border'] . ';';
        }

        echo '}' . "\r\n";
    }

    // Button: background:focus, border:focus
    if (!empty($options['contact_button_bg_hover']) || !empty($options['contact_button_border_hover'])) {
        echo '.content .contact-form input[type="submit"]:hover,';
        echo '.content .contact-form input[type="submit"]:focus{';

        // input background:focus
        if (!empty($options['contact_button_bg_hover'])) {
            echo 'background:#' . $options['contact_button_bg_hover'] . ';';
        }

        // input border:focus
        if (!empty($options['contact_button_border_hover'])) {
            echo 'border:1px solid #' . $options['contact_button_border_hover'] . ';';
        }

        echo '}' . "\r\n";
    }

    // Message: success
    if (!empty($options['contact_success_bg']) || !empty($options['contact_success_color'])) {
        echo '.contact-form .csmm-alert-success{';

        // success background
        if (!empty($options['contact_success_bg'])) {
            echo 'background:#' . $options['contact_success_bg'] . ';';
        }

        // success color
        if (!empty($options['contact_success_color'])) {
            echo 'color:#' . $options['contact_success_color'] . ';';
        }

        echo '}' . "\r\n";
    }

    // Message: error
    if (!empty($options['contact_error_bg']) || !empty($options['contact_error_color'])) {
        echo '.contact-form .csmm-alert-danger{';

        // error background
        if (!empty($options['contact_error_bg'])) {
            echo 'background:#' . $options['contact_error_bg'] . ';';
        }

        // error color
        if (!empty($options['contact_error_color'])) {
            echo 'color:#' . $options['contact_error_color'] . ';';
        }

        echo '}' . "\r\n";
    }

    echo '.contact-form .mm-module{';
    if (!empty($options['module_margin'])) {
        echo 'margin: ' . $options['module_margin'] . 'px 0px;';
    }
    echo '}';

    echo '.contact-form input::-webkit-input-placeholder,
    .contact-form textarea::-webkit-input-placeholder {
        color: #' . $options['contact_placeholder_color'] . ';
    }
    .contact-form input::-moz-placeholder,
    .contact-form textarea::-moz-placeholder {
        color: #' . $options['contact_placeholder_color'] . ';
    }
    .contact-form input:-ms-input-placeholder,
    .contact-form textarea:-ms-input-placeholder {
        color: #' . $options['contact_placeholder_color'] . ';
    }
    .contact-form input:-moz-placeholder,
    .contact-form textarea:-moz-placeholder {
        color: #' . $options['contact_placeholder_color'] . ';
    }';
} // custom input styles



echo '.social-block li a { color: #' . $options['social_icons_color'] . '; }';
echo '.social-block li:hover { background-color: #' . $options['social_icons_color'] . '30; }';

echo '.map-block iframe { height: ' . $options['map_height'] . 'px; }';

echo '.countdown-block .timer span { font-size: ' . $options['countdown_size'] . 'px; color: #' . $options['countdown_color'] . '; min-width: ' . round($options['countdown_size'] * 1.6) . 'px; }';
echo '.countdown-block .timer span i { font-size: ' . $options['countdown_labels_size'] . 'px; color: #' . $options['countdown_labels_color'] . '; padding-right: ' . (int) ($options['countdown_labels_size'] * 0.4) . 'px; }';

$tmp = ($options['progress_height'] - $options['progress_label_size']) / 2;
echo '#progressbar .inner-therm span { color: #' . $options['progress_label_color'] . '; font-size: ' . $options['progress_label_size'] . 'px; line-height: ' . $options['progress_height'] . 'px; }';

// Custom CSS
if (!empty($options['custom_css'])) {
    echo stripslashes($options['custom_css']);
}

echo '</style>' . "\r\n";
