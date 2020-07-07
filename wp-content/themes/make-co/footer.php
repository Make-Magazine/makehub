<?php
// first close the genesis site-inner wrapper...
// if we don't want these wrappers, we can strip it out of here and the header.php
genesis_structural_wrap( 'site-inner', 'close' );
genesis_markup(
	array(
		'close'   => '</div>',
		'context' => 'site-inner',
	)
);
   echo file_get_contents(content_url() . '/universal-assets/v1/page-elements/universal-footer.html');
?>

<div id="nav-overlay"></div>
<?php wp_footer(); ?>

<div class="fancybox-thx" style="display:none;">
  <div class="nl-modal-cont nl-thx-p2">
    <div class="col-sm-4 hidden-xs nl-modal">
      <span class="fa-stack fa-4x">
        <i class="fa fa-circle-thin fa-stack-2x"></i>
        <i class="fa fa-thumbs-o-up fa-stack-1x"></i>
      </span>
    </div>
    <div class="col-sm-8 col-xs-12 nl-modal">
      <h3 style="text-align:center;">Awesome!</h3>
      <p style="color:#333;text-align:center;margin-top:20px;">Thanks for signing up.</p>
    </div>
    <div class="clearfix"></div>
  </div>
</div>

<div class="nl-modal-error" style="display:none;">
  <div class="col-xs-12 nl-modal padtop">
    <p class="lead">The reCAPTCHA box was not checked. Please try again.</p>
  </div>
  <div class="clearfix"></div>
</div>

<a href="mailto:feedback@make.co" class="btn feedback-btn">FEEDBACK</a>

<?php
    // Tracking pixels users can turn off through the cookie law checkbox -- defaults to yes
	if(!isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) || $_COOKIE['cookielawinfo-checkbox-non-necessary'] == "yes" ) {
?>
		<!-- Start Active Campaign Pixel -->
		<script type="text/javascript">
			(function(e,t,o,n,p,r,i){e.visitorGlobalObjectAlias=n;e[e.visitorGlobalObjectAlias]=e[e.visitorGlobalObjectAlias]  function(){(e[e.visitorGlobalObjectAlias].q=e[e.visitorGlobalObjectAlias].q  []).push(arguments)};e[e.visitorGlobalObjectAlias].l=(new Date).getTime();r=t.createElement("script");r.src=o;r.async=true;i=t.getElementsByTagName("script")[0];i.parentNode.insertBefore(r,i)})(window,document,"https://diffuser-cdn.app-us1.com/diffuser/diffuser.js","vgo");
			vgo('setAccount', '1000801328');
			vgo('setTrackByDefault', true);
			vgo('process');
		</script>
		<!-- Start Active Campaign Pixel -->
<?php } ?>
</body>
</html>