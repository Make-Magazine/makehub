<?php
if(!function_exists('wpvc_common_widget_div_to_get')){
    function wpvc_common_widget_div_to_get($shortcode){
	?>
        <div id="wpvc-root-widget" data-rootcode="<?php echo $shortcode; ?>" ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Leader widget view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_recent_widget_div_to_get')){
    function wpvc_recent_widget_div_to_get($shortcode){
	?>
        <div id="wpvc-root-widget-recent" data-rootcode="<?php echo $shortcode; ?>" ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Recent widget view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_photo_leaders_widget_view')){
    function wpvc_photo_leaders_widget_view($show_cont_args){
        $term_id = $show_cont_args['id'];
        $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
	?>
        <div id="wpvc-photoleader-page-<?php echo $term_id; ?>" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Widgets view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_recent_contestants_widget_view')){
    function wpvc_recent_contestants_widget_view($show_cont_args){
        $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
        $term_id = $show_cont_args['id'];
	?>
        <div id="wpvc-recent-contestants-page-<?php echo $term_id; ?>" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Contestant Widgets view','voting-contest')."</h2>");
}

?>


