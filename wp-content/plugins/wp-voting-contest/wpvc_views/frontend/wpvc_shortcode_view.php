<?php
if(!function_exists('wpvc_common_div_to_get')){
    function wpvc_common_div_to_get($shortcode){
	?>
        <div id="wpvc-root-shortcode" data-rootcode="<?php echo $shortcode; ?>" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Shortcode view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_showcontestants_view')){
    function wpvc_showcontestants_view($show_cont_args){
       $term_id = $show_cont_args['id'];
       $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
	?>
        <div id="wpvc-showcontestants-page-<?php echo $term_id; ?>" class="wpvc_show_contestants" data-shortcode="showcontestants" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Showcontestants view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_addcontestants_view')){
    function wpvc_addcontestants_view($show_cont_args){
        $term_id = $show_cont_args['id'];
        $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
	?>
        <div id="wpvc-addcontestant-page-<?php echo $term_id; ?>" class="wpvc_show_contestants" data-shortcode="addcontestants" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Addcontestants view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_addcontest_view')){
    function wpvc_addcontest_view($show_cont_args){
        $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
	?>
        <div id="wpvc-addcontest-page" class="wpvc_show_contestants" data-shortcode="addcontest" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Addcontest view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_showallcontestants_view')){
    function wpvc_showallcontestants_view($show_cont_args){
        $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
	?>
        <div id="wpvc-showallcontest-page" class="wpvc_show_contestants" data-shortcode="addcontest" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Addcontest view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_upcoming_showcontestants_view')){
    function wpvc_upcoming_showcontestants_view($show_cont_args){
        $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
	?>
        <div id="wpvc-upcomingcontestants-page" class="wpvc_show_contestants" data-shortcode="showcontestants" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Upcoming Contestants view','voting-contest')."</h2>");
}


if(!function_exists('wpvc_endcontest_showcontestants_view')){
    function wpvc_endcontest_showcontestants_view($show_cont_args){
        $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
	?>
        <div id="wpvc-endcontestants-page" class="wpvc_show_contestants" data-shortcode="showcontestants" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting End Contestants view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_profilecontest_showcontestants_view')){
    function wpvc_profilecontest_showcontestants_view($show_cont_args){
        $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
	?>
        <div id="wpvc-profilecontestants-page" class="wpvc_show_contestants" data-shortcode="showcontestants" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Profile Contestants view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_topcontest_showcontestants_view')){
    function wpvc_topcontest_showcontestants_view($show_cont_args){
        $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
	?>
        <div id="wpvc-topcontestants-page" class="wpvc_show_contestants" data-shortcode="showcontestants" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Top Contestants view','voting-contest')."</h2>");
}


if(!function_exists('wpvc_login_contestants_view')){
    function wpvc_login_contestants_view($show_cont_args){
        $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
	?>
        <div id="wpvc-logincontest-page" class="wpvc_show_contestants" data-shortcode="showcontestants" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' ></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Login Contestants view','voting-contest')."</h2>");
}


?>


