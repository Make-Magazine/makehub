<?php
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
      <h3>Awesome!</h3>
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

<!-- Usersnap feedback and bug tracker -->
<script>
  	window.onUsersnapLoad = function(api) {
	api.on('open', function(event) {
		/*event.api.setValue('customData', JSON.stringify({
		  Company: 'MakerMedia',
		  User: 'Rio'
		}));*/
	});
	api.init();
	window.Usersnap = api;
  	}
  	var script = document.createElement('script');
	script.async = 1;
	script.src = 'https://api.usersnap.com/load/ab520b96-6180-4bf7-bd3e-11786a368bce.js?onload=onUsersnapLoad';
	document.getElementsByTagName('head')[0].appendChild(script);
</script>

<?php if(is_front_page()){ ?>
	<script async>(function(s,u,m,o,j,v){j=u.createElement(m);v=u.getElementsByTagName(m)[0];j.async=1;j.src=o;j.dataset.sumoSiteId='6d09a6b9f0dfb10b9fbcebf5702dcbe99280f2a4fedcc1e8d64c47156312fd1a';v.parentNode.insertBefore(j,v)})(window,document,'script','//load.sumo.com/');</script>
<?php } ?>

</body>
</html>