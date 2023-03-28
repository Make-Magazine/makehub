<?php
// Localization Support
load_theme_textdomain( 'onecommunity', get_template_directory().'/lang' );
$locale = get_locale();
$locale_file = get_template_directory()."/lang/$locale.php";
if ( is_readable($locale_file) )
    require_once($locale_file);

setlocale(LC_NUMERIC, 'en_US.UTF-8');

if ( ! function_exists( 'onecommunity_setup' ) ) :

function onecommunity_setup() {
	load_theme_textdomain( 'onecommunity' );
	add_theme_support('editor-styles');
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support('buddypress-use-nouveau');


	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Enqueue editor styles.
	add_editor_style( 'editor-style.css' );

	add_theme_support( 'post-thumbnails' );

	if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'medium', 300, 300, true );
	add_image_size( 'post-thumbnail', 500, 375, true );
	add_image_size( 'post-thumbnail-2', 960, 540, true );
	add_image_size( 'post-thumbnail-vertical', 500, 700, true );
}

/*
* Switch default core markup for search form, comment form, and comments
* to output valid HTML5.
*/
add_theme_support( 'html5', array(
	'search-form',
	'comment-form',
	'comment-list',
	'gallery',
	'caption',
) );


// Indicate widget sidebars can use selective refresh in the Customizer.
add_theme_support( 'customize-selective-refresh-widgets' );

}
endif; // onecommunity_setup
add_action( 'after_setup_theme', 'onecommunity_setup' );


// Add custom styles to TinyMCE editor
if ( ! function_exists('tdav_css') ) {
    function tdav_css($wp) {
        $wp .= ',' . get_stylesheet_directory_uri() . '/editor-style.css';
        return $wp;
    }
}
add_filter( 'mce_css', 'tdav_css' );


function secure_clean($string) {
   return preg_replace('/[^\w]/', '', $string); // Removes everything except letters, numbers and _.
}

/** Check is dark mode is enabled **/ 
if ( get_theme_mod( 'onecommunity_dark_mode_enable', true ) == true ) {
function add_inline_head_script() { ?>
<script type="text/javascript">
    window.addEventListener("DOMContentLoaded", function(event) {
	if (document.cookie.indexOf("dark_mode=") >= 0) {
  		document.body.classList.add("dark-mode");
	}
});
</script>
<?php }
add_action( 'wp_head', 'add_inline_head_script', 1 );
}


/**
 * Sets the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 *
 * @since OneCommunity 1.0
 */
function onecommunity_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'onecommunity_content_width', 840 );
}
add_action( 'after_setup_theme', 'onecommunity_content_width', 0 );


function remove_default_stylesheet() {
    wp_dequeue_style( 'bp-nouveau-css' );
    wp_deregister_style( 'bp-nouveau-css' );
}
add_action( 'wp_enqueue_scripts', 'remove_default_stylesheet', 200 );



if (function_exists('register_sidebar')) {

add_action( 'widgets_init', 'onecommunity_widgets_init' );
function onecommunity_widgets_init() {

	register_sidebar(array(
		'name' => 'Sidebar - Blog',
		'id'   => 'sidebar-blog',
		'description'   => 'This is a widgetized area visible on the blog pages.',
		'before_widget' => '<div class="sidebar-box %2$s widget"  id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Sidebar - Single Post',
		'id'   => 'sidebar-single',
		'description'   => 'This is a widgetized area visible on the single post.',
		'before_widget' => '<div class="sidebar-box %2$s widget"  id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Sidebar - Contact',
		'id'   => 'sidebar-contact',
		'description'   => 'This is a widgetized area visible on the contact form page.',
		'before_widget' => '<div class="sidebar-box %2$s widget"  id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Sidebar - Pages',
		'id'   => 'sidebar-pages',
		'description'   => 'This is a widgetized area visible on the pages.',
		'before_widget' => '<div class="sidebar-box %2$s widget" id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Left Sidebar',
		'id'   => 'sidebar-left',
		'description'   => 'This is a widgetized area visible on the left sidebar.',
		'before_widget' => '<div class="sidebar-box %2$s widget" id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Left Sidebar - BP Groups',
		'id'   => 'sidebar-groups-left',
		'description'   => 'This is a widgetized area visible on the buddypress groups.',
		'before_widget' => '<div class="sidebar-box %2$s widget" id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Sidebar - BP Groups',
		'id'   => 'sidebar-groups',
		'description'   => 'This is a widgetized area visible on the buddypress groups.',
		'before_widget' => '<div class="sidebar-box %2$s widget" id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));


	register_sidebar(array(
		'name' => 'Sidebar - BP Groups (Cached)',
		'id'   => 'sidebar-groups-cached',
		'description'   => 'This is a cached widgetized area visible on the Buddypress groups. If some widgets shows the same content on the all Buddypress groups then you can put these widgets here.',
		'before_widget' => '<div class="sidebar-box %2$s widget" id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Left Sidebar - BP User',
		'id'   => 'sidebar-user-left',
		'description'   => 'This is a widgetized area visible on the user profiles.',
		'before_widget' => '<div class="sidebar-box %2$s widget" id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Sidebar - BP User',
		'id'   => 'sidebar-profiles',
		'description'   => 'This is a widgetized area visible on the user profiles.',
		'before_widget' => '<div class="sidebar-box %2$s widget" id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Sidebar - BP User (Cached)',
		'id'   => 'sidebar-profiles-cached',
		'description'   => 'This is a cached widgetized area visible on the Buddypress profiles. If some widgets shows the same content on the all user profiles then you can put these widgets here.',
		'before_widget' => '<div class="sidebar-box %2$s widget" id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Sidebar - Shop',
		'id'   => 'sidebar-shop',
		'description'   => 'This is a widgetized area visible on the shop.',
		'before_widget' => '<div class="sidebar-box %2$s widget" id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

	register_sidebar(array(
		'name' => 'Sidebar - LearnPress',
		'id'   => 'sidebar-learnpress',
		'description'   => 'This is a widgetized area visible on the LearnPress pages.',
		'before_widget' => '<div class="sidebar-box %2$s widget" id="%1$s">',
		'after_widget'  => '</div><!--sidebar-box ends--><div class="clear"></div>',
		'before_title'  => '<div class="sidebar-title">',
		'after_title'   => '</div>'
	));

}

}


/**
 * Handles JavaScript detection.
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 */
function onecommunity_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'onecommunity_javascript_detection', 0 );


function onecommunity_scripts() {

	// Theme stylesheet.
	wp_enqueue_style( 'onecommunity-style', get_stylesheet_uri(), array(), rand(111,9999), 'all' );

	wp_register_style( 'responsive', get_template_directory_uri() . '/css/responsive.css', array(), rand(111,9999), 'all' );
	wp_enqueue_style( 'responsive' );

	wp_register_style( 'onecommunity-animations', get_template_directory_uri() . '/css/animations.css', array() );
	wp_enqueue_style( 'onecommunity-animations' );

	wp_register_style( 'bp-responsive', get_template_directory_uri() . '/buddypress/css/responsive.css', array() );
	wp_enqueue_style( 'bp-responsive' );

	if ( class_exists( 'RTMedia' ) ) {
	wp_register_style( 'rtmedia', get_template_directory_uri() . '/buddypress/css/rtmedia.css', array() );
	wp_enqueue_style( 'rtmedia' );
	}

	wp_enqueue_script('masonry');

	// Load the html5 shiv.
	wp_enqueue_script( 'onecommunity-html5', get_template_directory_uri() . '/js/html5.js', array(), '3.7.3' );
	wp_script_add_data( 'onecommunity-html5', 'conditional', 'lt IE 9' );


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_enqueue_script( 'onecommunity-script', get_template_directory_uri() . '/js/functions.js', true, null, 'in_footer' );

	wp_deregister_style( 'wp-ulike' );
}
add_action( 'wp_enqueue_scripts', 'onecommunity_scripts' );


function onecommunity_admin_scripts() {
	wp_register_style( 'customizer-style', get_template_directory_uri() . '/css/customizer.css', array() );
	wp_enqueue_style( 'customizer-style' );
}
add_action( 'admin_enqueue_scripts', 'onecommunity_admin_scripts' );


function add_inline_script() { ?>
<script type="text/javascript">
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>
<?php }
add_action( 'wp_footer', 'add_inline_script', 100 );


function onecommunity_fonts_url() {

    wp_enqueue_style(get_theme_mod( 'onecommunity_font_name', 'Nunito' ), get_theme_mod( 'onecommunity_font_url', '//fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900&display=swap' ));

    $onecommunity_font_2_enable = get_theme_mod( 'onecommunity_font_2_enable', false );
	if ( $onecommunity_font_2_enable == true ) {
    	wp_enqueue_style(get_theme_mod( 'onecommunity_font_name_2', '' ), get_theme_mod( 'onecommunity_font_url_2', '' ));
    }

}
add_action('wp_print_styles', 'onecommunity_fonts_url');
add_action('login_enqueue_scripts', 'onecommunity_fonts_url');


function onecommunity_login_enqueue_style() {
	wp_enqueue_style( 'wp-login', get_template_directory_uri() . '/css/wp-login.css', false );
}
add_action( 'login_enqueue_scripts', 'onecommunity_login_enqueue_style', 10 );



/**
 * Adds custom classes to the array of body classes.
 *
 * @since OneCommunity 1.0
 *
 * @param array $classes Classes for the body element.
 * @return array (Maybe) filtered body classes.
 */
function onecommunity_body_classes( $classes ) {
	// Adds a class of custom-background-image to sites with a custom background image.

	// Adds a class of group-blog to sites with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if (! ( is_user_logged_in() ) ) {
        $classes[] = 'logged-out';
    }

	return $classes;
}
add_filter( 'body_class', 'onecommunity_body_classes' );

/**
 * Converts a HEX value to RGB.
 *
 * @since OneCommunity 1.0
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function onecommunity_hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) === 3 ) {
		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
	} else if ( strlen( $color ) === 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/customizer-style.php';



/**
 * Modifies tag cloud widget arguments to have all tags in the widget same font size.
 * @param array $args Arguments for tag cloud widget.
 * @return array A new modified arguments.
 */
function onecommunity_widget_tag_cloud_args( $args ) {
	$args['largest'] = 1;
	$args['smallest'] = 1;
	$args['unit'] = 'em';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'onecommunity_widget_tag_cloud_args' );



add_action( 'init', 'register_menus' );
     function register_menus() {

           register_nav_menus(
                array(
                     'header-menu' => 'Header menu'
                 )
            );


if ( has_nav_menu( 'header-menu' ) ) {

		class Child_Wrap extends Walker_Nav_Menu
		{
		    function start_lvl(&$output, $depth = 0, $args = array())
		    {
		        $indent = str_repeat("\t", $depth);
		        $output .= "\n$indent<div class=\"nav-ul-container fadein\"><ul class=\"sub-menu\">\n";
		    }
		    function end_lvl(&$output, $depth = 0, $args = array())
		    {
		        $indent = str_repeat("\t", $depth);
		        $output .= "$indent</ul></div>\n";
		    }
		}

}

register_nav_menus(
    array(
        'mini-footer-menu' => 'Mini footer menu'
    )
);

register_nav_menus(
    array(
        'mini-footer-menu-2' => 'Mini footer menu 2'
    )
);

}





add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

add_theme_support( 'wc-product-gallery-lightbox' );
add_theme_support( 'wc-product-gallery-zoom' );


add_action('wp_enqueue_scripts','rt_remove_default_style',999);  
function rt_remove_default_style(){  
    wp_dequeue_style( 'rtmedia-main' );
} 


function mytheme_comment($comment, $args, $depth) {

	global $counter; // Make counter variable global so we can use it inside this function.
	$counter++;
	$GLOBALS['comment'] = $comment;

    if ( 'div' === $args['style'] ) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    ?>
    <<?php echo esc_html( $tag ) ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
    <?php if ( 'div' != $args['style'] ) : ?>

        <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
    <?php endif; ?>


    <div class="comment-avatar">
        <?php echo get_avatar( $comment, 90 ); ?>
        <?php comment_author(); ?>
    </div>

	<div class="comment-content">

	<div class="comment-content-time">
	<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
        <?php
        /* translators: 1: date, 2: time */
        printf( esc_attr__('%1$s at %2$s', 'onecommunity'), get_comment_date(), get_comment_time() ); ?></a><?php edit_comment_link( esc_attr__( '(Edit)', 'onecommunity' ), '  ', '' );
    ?>
	</div>

    <?php comment_text(); ?>

    <div class="reply">
        <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
    </div>

	<div class="comment-counter" id="comment-counter-<?php echo esc_html( $counter ); ?>"><a href="#comment-<?php comment_ID(); ?>"><?php echo esc_html( $counter ); ?></a></div>

    </div>
    <?php if ( $comment->comment_approved == '0' ) : ?>
         <em class="comment-awaiting-moderation"><?php esc_attr_e( 'Your comment is awaiting moderation.', 'onecommunity' ); ?></em>
          <br />
    <?php endif; ?>


    <?php if ( 'div' != $args['style'] ) : ?>
    </div>
    <?php endif; ?>
    <?php
    }




function custom_excerpt_length( $length ) {
	return 50;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );


// Replaces the excerpt "Read More" text by a link
function new_excerpt_more($more) {
       global $post;
	return ' ... ';
}
add_filter('excerpt_more', 'new_excerpt_more');



function short_freshness_time($output) {
$output = preg_replace( '/, .*[^ago]/', ' ', $output );
return $output;
}
add_filter( 'bbp_get_time_since', 'short_freshness_time' );
add_filter('bp_core_time_since', 'short_freshness_time');

function dd_6239eaa266eb2e87e55c14c3b756b02c() {}


function dd_the_post_thumbnail_caption() {
  global $post;

  $thumbnail_id = get_post_thumbnail_id($post->ID);
  $thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));

  if ($thumbnail_image && isset($thumbnail_image[0])) {
    echo '<span>'.$thumbnail_image[0]->post_excerpt.'</span>';
  }
}



function avatar_thumbnail_size(){
 define ( 'BP_AVATAR_THUMB_WIDTH', 100 );
 define ( 'BP_AVATAR_THUMB_HEIGHT', 100 );
 define( 'BP_AVATAR_FULL_WIDTH', '400');
 define( 'BP_AVATAR_FULL_HEIGHT', '400' );
 define ( 'BP_AVATAR_ORIGINAL_MAX_FILESIZE', 6000000 );
}
add_action('bp_init', 'avatar_thumbnail_size', 2);


function onecommmunity_xprofile_cover_image( $settings = array() ) {
	$settings['default_cover'] = '';
    $settings['width']  = 850;
    $settings['height'] = 450;

    return $settings;
}
add_filter( 'bp_before_members_cover_image_settings_parse_args', 'onecommmunity_xprofile_cover_image', 10, 1 );

function onecommmunity_groups_cover_image( $settings = array() ) {
	$settings['default_cover'] = '';
    $settings['width']  = 850;
    $settings['height'] = 450;

    return $settings;
}
add_filter( 'bp_before_groups_cover_image_settings_parse_args', 'onecommmunity_groups_cover_image', 10, 1 );



function onecommunity_get_the_archive_title() {
    if ( is_category() ) {
        $title = sprintf( esc_attr__( '%s', 'onecommunity' ), single_cat_title( '', false ) );
    } elseif ( is_tag() ) {
        $title = sprintf( esc_attr__( 'Tag: %s', 'onecommunity' ), single_tag_title( '', false ) );
    } elseif ( is_author() ) {
        $title = sprintf( esc_attr__( 'Author: %s', 'onecommunity' ), '<span class="vcard">' . get_the_author() . '</span>' );
    } elseif ( is_year() ) {
        $title = sprintf( esc_attr__( 'Year: %s', 'onecommunity' ), get_the_date( _x( 'Y', 'yearly archives date format', 'onecommunity' ) ) );
    } elseif ( is_month() ) {
        $title = sprintf( esc_attr__( 'Month: %s', 'onecommunity' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'onecommunity' ) ) );
    } elseif ( is_day() ) {
        $title = sprintf( esc_attr__( 'Day: %s', 'onecommunity' ), get_the_date( _x( 'F j, Y', 'daily archives date format', 'onecommunity' ) ) );
    } elseif ( is_tax( 'post_format' ) ) {
        if ( is_tax( 'post_format', 'post-format-aside' ) ) {
            $title = _x( 'Asides', 'post format archive title', 'onecommunity' );
        } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
            $title = _x( 'Galleries', 'post format archive title', 'onecommunity' );
        } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
            $title = _x( 'Images', 'post format archive title', 'onecommunity' );
        } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
            $title = _x( 'Videos', 'post format archive title', 'onecommunity' );
        } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
            $title = _x( 'Quotes', 'post format archive title', 'onecommunity' );
        } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
            $title = _x( 'Links', 'post format archive title', 'onecommunity' );
        } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
            $title = _x( 'Statuses', 'post format archive title', 'onecommunity' );
        } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
            $title = _x( 'Audio', 'post format archive title', 'onecommunity' );
        } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
            $title = _x( 'Chats', 'post format archive title', 'onecommunity' );
        }
    } elseif ( is_post_type_archive() ) {
        $title = sprintf( esc_attr__( 'Archives: %s', 'onecommunity' ), post_type_archive_title( '', false ) );
    } elseif ( is_tax() ) {
        $tax = get_taxonomy( get_queried_object()->taxonomy );
        /* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
        $title = sprintf( esc_attr__( '%1$s: %2$s', 'onecommunity' ), $tax->labels->singular_name, single_term_title( '', false ) );
    } else {
        $title = esc_attr__( 'Archives', 'onecommunity' );
    }

    return apply_filters( 'onecommunity_get_the_archive_title', $title );
}






/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme Xphoria for publication on ThemeForest
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 *
 * Depending on your implementation, you may want to change the include call:
 *
 * Parent Theme:
 * require_once get_template_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Child Theme:
 * require_once get_stylesheet_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Plugin:
 * require_once dirname( __FILE__ ) . '/path/to/class-tgm-plugin-activation.php';
 */
require_once get_template_directory() . '/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'onecommunity_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function onecommunity_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		// This is an example of how to include a plugin bundled with a theme.


		array(
			'name'               => 'OneCommunity Shortcodes', // The plugin name.
			'slug'               => 'onecommunity-shortcodes', // The plugin slug (typically the folder name).
			'source'   	     => 'http://diaboliquedesign.com/bundled-plugins/onecommunity-shortcodes.zip',
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be checked for availability to determine if a plugin is active.
		),


		array(
			'name'               => 'Ajax Search Pro', // The plugin name.
			'slug'               => 'ajax-search-pro', // The plugin slug (typically the folder name).
			'source'   	     => 'http://diaboliquedesign.com/bundled-plugins/ajax-search-pro.zip',
			'required'           => false, // If false, the plugin is only 'recommended' instead of required.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be checked for availability to determine if a plugin is active.
		),


		array(
			'name'      => 'BuddyPress',
			'slug'      => 'buddypress',
			'required'  => true,
			'force_activation'  => false
		),

		array(
			'name'      => 'bbPress',
			'slug'      => 'bbpress',
			'required'  => false,
			'force_activation'  => false
		),


		array(
			'name'      => 'Contact Form 7',
			'slug'      => 'contact-form-7',
			'required'  => false,
			'force_activation'  => false
		),


		array(
			'name'      => 'BuddyPress Forum Editor',
			'slug'      => 'bp-forum-editor',
			'required'  => false,
			'force_activation'  => false
		),

		array(
			'name'      => 'GamiPress',
			'slug'      => 'gamipress',
			'required'  => false,
			'force_activation'  => false
		),

		array(
			'name'      => 'GamiPress - BuddyPress integration',
			'slug'      => 'gamipress-buddypress-integration',
			'required'  => false,
			'force_activation'  => false
		),

		array(
			'name'      => 'GamiPress - WP Ulike integration',
			'slug'      => 'gamipress-wp-ulike-integration',
			'required'  => false,
			'force_activation'  => false
		),

		array(
			'name'      => 'GamiPress - bbPress integration',
			'slug'      => 'gamipress-bbpress-integration',
			'required'  => false,
			'force_activation'  => false
		),

		array(
			'name'      => 'rtMedia for WordPress, BuddyPress and bbPress',
			'slug'      => 'buddypress-media',
			'required'  => false,
			'force_activation'  => false
		),

		array(
			'name'      => 'WP ULike',
			'slug'      => 'wp-ulike',
			'required'  => false,
			'force_activation'  => false
		),


		array(
			'name'      => 'Elementor',
			'slug'      => 'elementor',
			'required'  => false,
			'force_activation'  => false
		),

		array(
			'name'               => 'DD Hide Admin Bar', // The plugin name.
			'slug'               => 'dd-hide-admin-bar', // The plugin slug (typically the folder name).
			'source'   	     => 'http://diaboliquedesign.com/bundled-plugins/dd-hide-admin-bar.zip',
			'required'           => false, // If false, the plugin is only 'recommended' instead of required.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be checked for availability to determine if a plugin is active.
		),

	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'onecommunity',             // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.

		/*
		'strings'      => array(
			'page_title'                      => esc_attr__( 'Install Required Plugins', 'onecommunity' ),
			'menu_title'                      => esc_attr__( 'Install Plugins', 'onecommunity' ),
			/* translators: %s: plugin name. * /
			'installing'                      => esc_attr__( 'Installing Plugin: %s', 'onecommunity' ),
			/* translators: %s: plugin name. * /
			'updating'                        => esc_attr__( 'Updating Plugin: %s', 'onecommunity' ),
			'oops'                            => esc_attr__( 'Something went wrong with the plugin API.', 'onecommunity' ),
			'notice_can_install_required'     => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme requires the following plugin: %1$s.',
				'This theme requires the following plugins: %1$s.',
				'onecommunity'
			),
			'notice_can_install_recommended'  => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				'onecommunity'
			),
			'notice_ask_to_update'            => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				'onecommunity'
			),
			'notice_ask_to_update_maybe'      => _n_noop(
				/* translators: 1: plugin name(s). * /
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'onecommunity'
			),
			'notice_can_activate_required'    => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'onecommunity'
			),
			'notice_can_activate_recommended' => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'onecommunity'
			),
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'onecommunity'
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'onecommunity'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'onecommunity'
			),
			'return'                          => esc_attr__( 'Return to Required Plugins Installer', 'onecommunity' ),
			'plugin_activated'                => esc_attr__( 'Plugin activated successfully.', 'onecommunity' ),
			'activated_successfully'          => esc_attr__( 'The following plugin was activated successfully:', 'onecommunity' ),
			/* translators: 1: plugin name. * /
			'plugin_already_active'           => esc_attr__( 'No action taken. Plugin %1$s was already active.', 'onecommunity' ),
			/* translators: 1: plugin name. * /
			'plugin_needs_higher_version'     => esc_attr__( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'onecommunity' ),
			/* translators: 1: dashboard link. * /
			'complete'                        => esc_attr__( 'All plugins installed and activated successfully. %1$s', 'onecommunity' ),
			'dismiss'                         => esc_attr__( 'Dismiss this notice', 'onecommunity' ),
			'notice_cannot_install_activate'  => esc_attr__( 'There are one or more required or recommended plugins to install, update or activate.', 'onecommunity' ),
			'contact_admin'                   => esc_attr__( 'Please contact the administrator of this site for help.', 'onecommunity' ),

			'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
		),
		*/
	);

	tgmpa( $plugins, $config );
}



function onecommunity_ajax_login_init(){
    wp_enqueue_script( 'login-js', get_template_directory_uri() . '/js/login.js', true, '1.0', 'in_footer' );
    wp_localize_script( 'login-js', 'login_js', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => home_url(),
        'loadingmessage' => esc_attr__('Sending user info, please wait...', 'onecommunity')
    ));
    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
}
// Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
    add_action('init', 'onecommunity_ajax_login_init');
}
if (!function_exists('ajax_login')) {
    function ajax_login(){
        // Get the POST data and sign user on
        $info = array();
        $info['user_login'] = $_POST['username'];
        $info['user_password'] = $_POST['password'];
        $info['remember'] = $_POST['rememberme'];
        $user_signon = wp_signon( $info, false );
        if ( is_wp_error($user_signon) ){
            echo json_encode(array('loggedin'=>false, 'message'=> esc_attr__('Wrong username or password.', 'onecommunity')));
        } else {
            echo json_encode(array('loggedin'=>true, 'message'=> esc_attr__('Login successful, redirecting...', 'onecommunity')));
        }
        wp_die();
    }
}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_blog_classic() {
$blog_posts_type = esc_html( $_POST['blog_posts_type'] );
$page = esc_html( $_POST['page'] );

if ( $blog_posts_type == 1 ) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('posts_per_page=3&post_status=publish'.'&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

	get_template_part( 'template-parts/blog', 'classic' );

endwhile;

} elseif ($blog_posts_type == 2) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=meta_value_num&meta_key=_liked&posts_per_page=3&post_status=publish'.'&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

	get_template_part( 'template-parts/blog', 'classic' );

endwhile;

} elseif ($blog_posts_type == 3) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=comment_count&posts_per_page=3&post_status=publish'.'&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

	get_template_part( 'template-parts/blog', 'classic' );

endwhile;
}

wp_reset_query();
wp_die();

}
add_action( 'wp_ajax_nopriv_onecommunity_blog_classic', 'onecommunity_blog_classic' );
add_action( 'wp_ajax_onecommunity_blog_classic', 'onecommunity_blog_classic' );



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function onecommunity_blog_1() {
$blog_posts_type = esc_html( $_POST['blog_posts_type'] );
$page = esc_html( $_POST['page'] );

if ( $blog_posts_type == 1 ) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('posts_per_page=6&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop

} elseif ($blog_posts_type == 2) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=meta_value_num&meta_key=_liked&posts_per_page=6&post_status=publish'.'&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop

} elseif ($blog_posts_type == 3) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=comment_count&posts_per_page=6&post_status=publish'.'&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();


get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop
}

wp_reset_query();
wp_die();

}
add_action( 'wp_ajax_nopriv_onecommunity_blog_1', 'onecommunity_blog_1' );
add_action( 'wp_ajax_onecommunity_blog_1', 'onecommunity_blog_1' );

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_contains_array($str, array $arr) {
    foreach($arr as $a) {
        if (stripos($str,$a) !== false) return true;
    }
    return false;
}

if ( !is_user_logged_in() ) {
$onecommunity_intranet_enable = get_theme_mod( 'onecommunity_intranet_enable', false );
if ( $onecommunity_intranet_enable == true ) {

	add_action('wp_head','hook_onecommunity_intranet');

	function hook_onecommunity_intranet() {

		$safePages = explode(',', get_theme_mod( 'onecommunity_intranet_pages', 'login,register,intranet,recovery' ));
		$arr = $safePages;
		global $wp;
		$str = home_url( $wp->request );

		if ( onecommunity_contains_array($str,$arr) == false ) { ?>

	   		<script type="text/javascript">
	 	  		<!--
	 	  		window.location = "<?php echo esc_url( home_url() ); ?>/<?php echo get_theme_mod( 'onecommunity_intranet_landing', 'intranet' ) ?>"
 		  		//-->
	    	</script>
	    	
<?php
		}
	}
}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function onecommunity_blog_archive_tabs() {
$blog_posts_type = esc_html( $_POST['blog_posts_type'] );
$page = esc_html( $_POST['page'] );
$taxonomy = esc_attr( $_POST['taxonomy'] );
$term_id = esc_html( $_POST['term_id'] );


if ( $blog_posts_type == 1 ) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('posts_per_page=6&' . $taxonomy . '=' . $term_id . '&author=' . $user_id .'&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop

} elseif ($blog_posts_type == 2) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=meta_value_num&meta_key=_liked&posts_per_page=6&post_status=publish&' . $taxonomy . '=' . $term_id .'&author=' .$user_id. '&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop

} elseif ($blog_posts_type == 3) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=comment_count&posts_per_page=6&post_status=publish&' . $taxonomy . '=' . $term_id . '&author=' . $user_id . '&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop
}

wp_reset_query();
wp_die();

}
add_action( 'wp_ajax_nopriv_onecommunity_blog_archive_tabs', 'onecommunity_blog_archive_tabs' );
add_action( 'wp_ajax_onecommunity_blog_archive_tabs', 'onecommunity_blog_archive_tabs' );


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_blog_archive_more() {
$blog_posts_type = esc_html( $_POST['blog_posts_type'] );
$page = esc_html( $_POST['page'] );
$taxonomy = esc_html( $_POST['taxonomy'] );
$term_id = esc_html( $_POST['term_id'] );

if ( $blog_posts_type == 1 ) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('posts_per_page=6&post_status=publish&' . $taxonomy . '=' . $term_id . '&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop

} elseif ($blog_posts_type == 2) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=meta_value_num&meta_key=_liked&posts_per_page=6&post_status=publish&' . $taxonomy . '=' . $term_id .'&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop

} elseif ($blog_posts_type == 3) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=comment_count&posts_per_page=6&post_status=publish&' . $taxonomy . '=' . $term_id . '&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop
}

wp_reset_query();
wp_die();

}
add_action( 'wp_ajax_nopriv_onecommunity_blog_archive_more', 'onecommunity_blog_archive_more' );
add_action( 'wp_ajax_onecommunity_blog_archive_more', 'onecommunity_blog_archive_more' );


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


if ( get_theme_mod( 'onecommunity_live_notifications_enable', false ) == true ) {


function onecommunity_notifications_count() {
echo bp_notifications_get_unread_notification_count( bp_loggedin_user_id() );
wp_die();
}
add_action( 'wp_ajax_nopriv_onecommunity_notifications_count', 'onecommunity_notifications_count' );
add_action( 'wp_ajax_onecommunity_notifications_count', 'onecommunity_notifications_count' );


function onecommunity_notifications_list() {

$notifications = bp_notifications_get_notifications_for_user(bp_loggedin_user_id());
if ( $notifications ) { $counter = 0; for ( $i = 0, $count = count( $notifications ); $i < $count; ++$i ) { ?><li class="notification"><?php echo wp_kses( $notifications[$i], array( 'a' => array( 'href' => array(), 'title' => array() ) ) ); ?></li><?php $counter++; } }?><li class="view-all"><a href="<?php echo bp_loggedin_user_domain() ?>notifications"><?php esc_attr_e("Manage", "onecommunity"); ?></a></li>

<?php
wp_die();
}
add_action( 'wp_ajax_nopriv_onecommunity_notifications_list', 'onecommunity_notifications_list' );
add_action( 'wp_ajax_onecommunity_notifications_list', 'onecommunity_notifications_list' );


function onecommunity_messages_count() {
echo messages_get_unread_count( bp_loggedin_user_id() );
wp_die();
}
add_action( 'wp_ajax_nopriv_onecommunity_messages_count', 'onecommunity_messages_count' );
add_action( 'wp_ajax_onecommunity_messages_count', 'onecommunity_messages_count' );


function onecommunity_messages_list() {

if ( bp_has_message_threads('max=3&user_id=' . get_current_user_id() . '') ) : ?><ul><?php while ( bp_message_threads() ) : bp_message_thread(); ?><li class="<?php if(bp_message_thread_has_unread()) { ?>unread<?php } ?>"><div class="avatar"><?php bp_message_thread_avatar() ?></div><div class="message-content"><div class="message-content-details"><?php bp_message_thread_from() ?> <?php bp_message_thread_last_post_date() ?></div><a href="<?php bp_message_thread_view_link() ?>" class="message-title"><?php bp_message_thread_subject() ?></a><p><?php bp_message_thread_excerpt() ?></p></div><div class="clear"></div></li><?php endwhile; ?><li class="view-all"><a href="<?php echo bp_loggedin_user_domain() ?>messages"><?php esc_attr_e('View all messages', 'onecommunity'); ?></a></li></ul><?php else: ?><div id="message" class="info"><p>There are no messages to display.</p></div><?php endif;?>';

<?php
wp_die();
}
add_action( 'wp_ajax_nopriv_onecommunity_messages_list', 'onecommunity_messages_list' );
add_action( 'wp_ajax_onecommunity_messages_list', 'onecommunity_messages_list' );



function heartbeat_enqueue($hook_suffix) {

        // Make sure the JS part of the Heartbeat API is loaded.
        wp_enqueue_script('heartbeat');

        // Output the test JS in admin footer.
        add_action( 'wp_footer', 'heartbeat_js', 20 );

}
add_action( 'wp_footer', 'heartbeat_enqueue' );


//Change heartbeat interval time
function dd_heartbeat_settings( $settings ) {
   $settings['interval'] = get_theme_mod( 'onecommunity_live_notifications_interval', 15 ); //only values between 15 and 120 seconds allowed
   return $settings;
}
add_filter( 'heartbeat_settings', 'dd_heartbeat_settings' );


function heartbeat_js() { ?>
<script>
	window.addEventListener("DOMContentLoaded", function(event) {
        jQuery(document).on('heartbeat-tick', function() {

			console.log('Heartbeat tick - <?php echo get_theme_mod( 'onecommunity_live_notifications_interval', 15 ) ?>');

//******************************* Notifications top bar *****************************************//

		if(document.querySelector(".top-bar-notifications")){


		// Ajax request to check out the current notifications count
		var r = new XMLHttpRequest();
        r.open('POST', ajaxurl, true);
        r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        r.onload = function () {
        if (this.status >= 200 && this.status < 400) {

        	var notificationsCountContainer = document.querySelector(".top-bar-notifications span");
        	var notificationsOldCount = notificationsCountContainer.textContent;
            var notificationsNewCount = this.response;
            var notificationsNewCount = notificationsNewCount.match(/\d+/g);

            	if(notificationsOldCount != notificationsNewCount) {
        		notificationsCountContainer.innerHTML=notificationsNewCount;
				let oldClass = notificationsCountContainer.getAttribute('class');
				notificationsCountContainer.classList.remove(oldClass);
				notificationsCountContainer.classList.add('no-' + notificationsNewCount + '');

					//*** Get a new notifications list ***//
					var r = new XMLHttpRequest();
        			r.open('POST', ajaxurl, true);
        			r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        			r.onload = function () {
        			if (this.status >= 200 && this.status < 400) {

						var notificationsNewContent = this.response;
						var notificationsListContainer = document.querySelector(".top-bar-notifications-container ul.notifications-list");
        				notificationsListContainer.innerHTML=notificationsNewContent;

        			} else {
        			// Response error
            		console.log('Response error');
        			}
        			};
        			r.onerror = function() {
        			// Connection error
        			console.log('Connection error');
        			};
        			r.send('action=onecommunity_notifications_list');
        			//*******************************//


        			var audio = new Audio('<?php echo esc_attr( get_bloginfo( 'template_directory', 'display' ) ); ?>/audio/alert.mp3');
					audio.play();
					console.log("Notifications reloaded with Heartbeat API");

				}

        } else {
        // Response error
            console.log('Response error');
        }
        };
        r.onerror = function() {
        // Connection error
        console.log('Connection error');
        };
        r.send('action=onecommunity_notifications_count');
		/**************************** End ******************************/

		}

//******************************* Messages top bar *****************************************//

		if(document.querySelector(".top-bar-messages")){

        // Ajax request to check out the current notifications count
        var r = new XMLHttpRequest();
        r.open('POST', ajaxurl, true);
        r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        r.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var messagesCountContainer = document.querySelector(".top-bar-messages span");
            var messagesOldCount = messagesCountContainer.textContent;
            var messagesNewCount = this.response;
            var messagesNewCount = messagesNewCount.match(/\d+/g);

                if(messagesOldCount != messagesNewCount) {
                messagesCountContainer.innerHTML=messagesNewCount;
                let oldClass = messagesCountContainer.getAttribute('class');
                messagesCountContainer.classList.remove(oldClass);
                messagesCountContainer.classList.add('no-' + messagesNewCount + '');

                    /*** Get a new messages list ***/
                    var r = new XMLHttpRequest();
                    r.open('POST', ajaxurl, true);
                    r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                    r.onload = function () {
                    if (this.status >= 200 && this.status < 400) {

                        var messagesNewContent = this.response;
                        var messagesListContainer = document.querySelector(".top-bar-messages-container .top-bar-messages-menu");
                        messagesListContainer.innerHTML=messagesNewContent;

                        console.log("Messages reloaded with Heartbeat API");

                    } else {
                    // Response error
                    console.log('Response error');
                    }
                    };
                    r.onerror = function() {
                    // Connection error
                    console.log('Connection error');
                    };
                    r.send('action=onecommunity_messages_list');
                    /*******************************/


                }

        } else {
        // Response error
            console.log('Response error');
        }
        };
        r.onerror = function() {
        // Connection error
        console.log('Connection error');
        };
        r.send('action=onecommunity_messages_count');
        //**************************** End ******************************//

		}

    	});
    });

</script>
<?php }

}


/**
 * Login pop up for guests
 */

if ( !is_user_logged_in() ) {

function onecommunity_activity_guest_buttons() {
   echo '<a onclick="loginPopUp()" class="acomment-reply button"><span>' . esc_attr__("Comment", "onecommunity") . '</span></a> <a onclick="loginPopUp()" class="button"><span>' . esc_attr__("Favorite", "onecommunity") . '</span></a>';
}

add_action( 'bp_activity_entry_meta', 'onecommunity_activity_guest_buttons', 1, 0 );


function onecommunity_activity_comments_guest_buttons() {
   echo '<a onclick="loginPopUp()" class="acomment-reply button"><span>' . esc_attr__("Reply", "onecommunity") . '</span></a>';
}

add_action( 'bp_activity_comment_options', 'onecommunity_activity_comments_guest_buttons', 1, 0 );

}


if ( class_exists( 'WooCommerce' ) ) {

/**
 * Change number or products per row to 2 (WooCommerce)
 */

add_filter('loop_shop_columns', 'loop_columns', 999);
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 2; // 2 products per row
	}
}

}




function wpse_footer_db_queries(){
    echo '<!-- '.get_num_queries().' queries in '.timer_stop(0).' seconds. -->'.PHP_EOL;
}
add_action('wp_footer', 'wpse_footer_db_queries');


function gamipress_custom_single_rank_template( $template ) {
    if ( is_single() ) {
    	$classes = get_body_class();
		if (in_array('single-general-rank',$classes)) {
        	$template = get_template_directory() . '/gamipress/custom-single-rank.php';
    	}
    }
    return $template;
}
add_filter( 'template_include', 'gamipress_custom_single_rank_template' );
