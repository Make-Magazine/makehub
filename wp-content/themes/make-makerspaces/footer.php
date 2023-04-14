<?php
// first close the genesis site-inner wrapper...
// if we don't want these wrappers, we can strip it out of here and the header.php
genesis_structural_wrap('site-inner', 'close');
genesis_markup(
        array(
            'close' => '</div>',
            'context' => 'site-inner',
        )
);
require_once(WP_CONTENT_DIR.'/universal-assets/v2/page-elements/universal-footer.html');
?>

<div id="nav-overlay"></div>
<?php wp_footer(); ?>

<?php
    // Tracking pixels users can turn off through the cookie law checkbox -- defaults to yes
	if(!isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) || $_COOKIE['cookielawinfo-checkbox-non-necessary'] == "yes" ) {
?>
		<!-- Start Active Campaign Pixel -->
		<script type="text/javascript">
			(function(e,t,o,n,p,r,i){e.visitorGlobalObjectAlias=n;e[e.visitorGlobalObjectAlias]=e[e.visitorGlobalObjectAlias]||function(){(e[e.visitorGlobalObjectAlias].q=e[e.visitorGlobalObjectAlias].q||[]).push(arguments)};e[e.visitorGlobalObjectAlias].l=(new Date).getTime();r=t.createElement("script");r.src=o;r.async=true;i=t.getElementsByTagName("script")[0];i.parentNode.insertBefore(r,i)})(window,document,"https://diffuser-cdn.app-us1.com/diffuser/diffuser.js","vgo");
			vgo('setAccount', '1000801328');
			vgo('setTrackByDefault', true);
			vgo('process');
		</script>
		<!-- Start Active Campaign Pixel -->
<?php } ?>

</body>
</html>
