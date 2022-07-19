<?php
if(!function_exists('wpvc_contestant_topbar')){
    function wpvc_contestant_topbar(){
        remove_action( 'admin_notices', 'update_nag', 3 );
	?>
        <div id="wpvc-topbar-contestant" data-url="<?php echo site_url();?>" data-version="<?php echo WPVC_VOTE_VERSION;?>" data-adminurl="<?php echo admin_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Contestant Top Bar','voting-contest')."</h2>");
}


?>


