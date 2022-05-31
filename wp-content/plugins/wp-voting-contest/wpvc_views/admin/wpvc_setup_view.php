<?php
wp_register_style('OW_SETUP_STYLES', WPVC_ASSETS_SETUP_CSS_PATH);
wp_enqueue_style('OW_SETUP_STYLES');

wp_register_script( 'ow_vote_validate',  WPVC_ASSETS_JS_PATH . 'ow_vote_validate.js',  array( 'jquery' ), false, true );

if(!function_exists('wpvc_setup_view')){
	function wpvc_setup_view($option, $shortcode){
		ob_start();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
			<head>
				<meta name="viewport" content="width=device-width" />
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title><?php esc_html_e( 'WP Voting Contest &rsaquo; Setup Wizard', 'WP Voting Contest' ); ?></title>
				<?php //do_action( 'admin_enqueue_scripts' ); ?>
				<?php wp_print_scripts( 'ow-setup' ); ?>
				<?php wp_print_scripts( 'ow_vote_validate' ); ?>
				<?php do_action( 'admin_print_styles' ); ?>
				<?php //do_action( 'admin_head' ); ?>
				<style type="text/css">
					<?php if($shortcode != '') { ?>
					body.ow-setup fieldset:not(:last-of-type) {
						display: none;
					}
					<?php } else { ?>
					body.ow-setup fieldset:not(:first-of-type) {
						display: none;
					}
					<?php } ?>
				</style>
			</head>
			<body class="ow-setup wp-core-ui">
				<h1 class="ow-logo"><a href="https://demo.ohiowebtech.com/" target="_blank">
					<p>WP Voting Contest</p>
				</a></h1>
				<ol class="ow-setup-steps">
					<li class="active">Category</li>
					<li class="">Options</li>
					<li class="">Ready!</li>
				</ol>
				<div class="ow-setup-content">
					<form id="myform" action="" method="post">
						<fieldset class="tab">
							<h2>CONTEST CATEGORY</h2>
							<table class="form-table">

								<tr valign="top">
									<th  scope="row"><label for="contest_name"><?php _e('Name','voting-contest'); ?> </label></th>
									<td colspan="2">
										<input type="text" name="contest_name" id="contest_name" value="" size="40" required>
										<span class="description"><?php _e('The name is how it appears on your site.','voting-contest'); ?></span>
									</td>
								</tr>
								<tr valign="top">
									<th  scope="row">

										<label for="imgcontest"><?php _e('Type of Contest: ','voting-contest'); ?> </label></th>
									<td colspan="2">
										<select name="imgcontest" id="imgcontest">
									   <?php $selecteds = (($values['options']['imgcontest'] == 'photo') || ($values['options']['imgcontest'] == ''))?'selected':''; ?>
									   <option value="photo" <?php echo $selecteds; ?>><?php _e('Photo','voting-contest'); ?></option>

									   <?php $selecteds = (($values['options']['imgcontest'] == 'video') || ($values['options']['imgcontest'] == 'on'))?'selected':''; ?>
									   <option value="video" <?php echo $selecteds; ?>><?php _e('Video','voting-contest'); ?></option>

									   <?php $selecteds = ($values['options']['imgcontest'] == 'music')?'selected':''; ?>
									   <option value="music" <?php echo $selecteds; ?>><?php _e('Music','voting-contest'); ?></option>

									   <?php $selecteds = ($values['options']['imgcontest'] == 'essay')?'selected':''; ?>
									   <option value="essay" <?php echo $selecteds; ?>><?php _e('Essay','voting-contest'); ?></option>

									   </select>
									   <span class="description"><?php _e('Image Field will not be shown in Front end Add Contestant if it is Video Or Music Contest (Submit Entries).','voting-contest'); ?></span>
								   </td>
								</tr>

								<tr valign="top" class="show_music_man" style="display:none;">
									<th scope="row"><label for="musicfileenable"><?php _e('Enable Music Upload: ','voting-contest'); ?></label></th>
									<td>
										<label class="switch switch-slide">
											<input class="switch-input" type="checkbox" id="musicfileenable" name="musicfileenable" />
											<span class="switch-label" data-on="Yes" data-off="No"></span>
										</label>

										<span class="description"> <?php _e('Enable Music Upload In Submit Entry Form for Music Contest Category.','voting-contest'); ?></span>
									</td>
								</tr>

								
								<tr valign="top">
									<th scope="row"><label for="imgdisplay"><?php _e('Vote Count Per Click: ','voting-contest'); ?></label></th>
									<td>
										<?php for($i=1;$i<=5;$i++){ ?>
											<input type="radio" value="<?php echo $i; ?>" id="vote_count_per_cat" name="vote_count_per_cat" <?php echo ($i==1)?"checked":""; ?>>
											<label for="vote_votingtype_0"><?php echo $i; ?></label>
										<?php } ?>
										
										<span class="description"> <?php _e('Per click cast vote count.','voting-contest'); ?></span>
									</td>
								</tr>
								

								<tr valign="top">
									<th scope="row"><label for="total_vote_count"><?php _e('Show Total Vote Count: ','voting-contest'); ?></label></th>
									<td>
										<label class="switch switch-slide">
											<input class="switch-input" type="checkbox" id="total_vote_count" name="total_vote_count" />
											<span class="switch-label" data-on="Yes" data-off="No"></span>
										</label>
										<span class="description"> <?php _e('Shows Total voted count .','voting-contest'); ?></span>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row"><label for="top_ten_count"><?php _e('Show Top 10 Contestants: ','voting-contest'); ?></label></th>
									<td>
										<label class="switch switch-slide">
											<input class="switch-input" type="checkbox" id="top_ten_count" name="top_ten_count" />
											<span class="switch-label" data-on="Yes" data-off="No"></span>
										</label>
										<span class="description"> <?php _e('Show Top 10 Contestants .','voting-contest'); ?></span>
									</td>
								</tr>


								<tr valign="top">
									<th scope="row"><label for="authordisplay"><?php _e('Show Author Name: ','voting-contest'); ?></label></th>
									<td>
										<label class="switch switch-slide">
											<input class="switch-input" type="checkbox" id="authordisplay" name="authordisplay"/>
											<span class="switch-label" data-on="Yes" data-off="No"></span>
										</label>
										<span class="description"> <?php _e('Displays Author Name On Contestant .','voting-contest'); ?></span>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row"><label for="authornamedisplay"><?php _e('Show Author Email: ','voting-contest'); ?></label></th>
									<td>
										<label class="switch switch-slide">
											<input class="switch-input" type="checkbox" id="authornamedisplay" name="authornamedisplay"/>
											<span class="switch-label" data-on="Yes" data-off="No"></span>
										</label>
									</td>
								</tr>
							</table>
							<input type="button" class="next button-primary" value="Continue" />
						</fieldset>
						<fieldset class="tab">
							<h2 class="">VOTING OPTIONS</h2>
							<table class="form-table">

								<tr valign="top">
									<th  scope="row"><label for="onlyloggedinuser"><?php _e('Must be logged in to Vote?','voting-contest'); ?></label>
									</th>
									<td colspan="2">
										<label class="switch switch-slide">
											<input class="switch-input" type="checkbox" id="onlyloggedinuser" name="onlyloggedinuser" <?php checked('on', $option['contest']['onlyloggedinuser']); ?>/>
											<span class="switch-label" data-on="Yes" data-off="No"></span>
											<span class="switch-handle"></span>
										</label>
										<span class="description"><?php _e('Only logged in Users can register their Vote ','voting-contest'); ?></span>
									</td>
								</tr>

								<tr valign="top">
									<th  scope="row"><label for="vote_onlyloggedcansubmit"><?php _e('Must be logged in to submit entries?','voting-contest'); ?>
									</label></th>
									<td colspan="2">
										<label class="switch switch-slide">
											<input class="switch-input" type="checkbox" id="vote_onlyloggedcansubmit" name="vote_onlyloggedcansubmit" <?php checked('on', $option['contest']['vote_onlyloggedcansubmit']); ?>/>
											<span class="switch-label" data-on="Yes" data-off="No"></span>
											<span class="switch-handle"></span>
										</label>
										<span class="description"><?php _e('Only logged in Users can submit their entries for the contest ','voting-contest'); ?></span>
									</td>
								</tr>


								<tr  valign="top">
									<th  scope="row"><label for="vote_tracking_method"><?php _e('Vote Tracking','voting-contest'); ?> </label>
									</th>
										<?php $vote_tracking_method = array('ip_traced'=>'IP Traced','cookie_traced'=>'Cookie Traced','email_verify' => 'Email Verification');
										?>
									<td colspan="2">
										<select id="vote_tracking_method" name="vote_tracking_method">
											<?php foreach($vote_tracking_method as $key => $method): ?>
												<?php $selected = ($key == $option['contest']['vote_tracking_method'])?'selected':''; ?>
												<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $method; ?></option>
											<?php endforeach; ?>
										</select>
										<span class="description"><?php _e('Select how Votes will be Tracked when a User is not required to log in. IP Traced is the most secure!','voting-contest'); ?></span>
									</td>
								</tr>

								<?php
										//Hide the Email Grab Button for the Email Verify and LoggedIn user
										if($option['contest']['onlyloggedinuser'] == "on"){
											$email_grab_class = 'style="display:none"';
										}
										else{
											if($option['contest']['vote_tracking_method'] == "email_verify"){
												$email_grab_class = 'style="display:none"';
											}
											else{
												$email_grab_class = 'style="display:table-row"';
											}
										}
								?>
								<tr valign="top" class="vote_grab_email_address" <?php echo $email_grab_class; ?>>
									<th  scope="row"><label for="vote_grab_email_address"><?php _e('Grab Email Addresss Before Voting','voting-contest'); ?> </label>
									</th>
									<td colspan="2">
										<label class="switch switch-slide">
											<input class="switch-input" type="checkbox" id="vote_grab_email_address" name="vote_grab_email_address" <?php checked('on', $option['contest']['vote_grab_email_address']); ?>/>
											<span class="switch-label" data-on="Yes" data-off="No"></span>
											<span class="switch-handle"></span>
										</label>
										<span class="description"><?php _e('This option will only work for <br />IP & COOKIE vote tracking','voting-contest'); ?></span>
									</td>
								</tr>

								<tr  valign="top">
									<th  scope="row"><label for="vote_frequency"><?php _e('Voting Frequency','voting-contest'); ?> </label></th>
									<td colspan="2">
										<?php $vote_frequency_count = ($option['contest']['vote_frequency_count'] == null)?1:$option['contest']['vote_frequency_count']; ?>
										<input type="text" name="vote_frequency_count" id="vote_frequency_count" value="<?php echo $vote_frequency_count; ?>" />

										<?php
											if($option['contest']['frequency'] != 0 && $option['contest']['frequency'] != 1 && $option['contest']['frequency'] != 11){
											if($option['contest']['frequency'] == 12 && $option['contest']['frequency'] == 24){
												$vote_frequency_hours  = $option['contest']['frequency'];
												$option_value = __('Every _____ Hours','voting-contest');
											}
											else{
												$vote_frequency_hours  = $option['contest']['vote_frequency_hours'];
												$option_value = __('Every ','voting_contest').$vote_frequency_hours. __(' Hours','voting-contest');
											}
											$display_class = "style='visibility:visible'";
											}
											else{
												$vote_frequency_hours  = 24;
												$display_class = "style='visibility:hidden'";
												$option_value = __('Every _____ Hours','voting-contest');
											}
										?>

										<select id="vote_frequency" name="vote_frequency" >
											<option value="0" <?php selected($option['contest']['frequency'], '0'); ?>><?php _e('No Limit','voting-contest'); ?></option>
											<option value="2" <?php selected($option['contest']['frequency'], '2'); ?>><?php echo $option_value; ?></option>
											<option value="1" <?php selected($option['contest']['frequency'], '1'); ?>><?php _e('per Calendar Day','voting-contest'); ?></option>
											<option value="11" <?php selected($option['contest']['frequency'], '11'); ?>><?php _e('per Category','voting-contest'); ?></option>
										</select>

										<input type="text" required maxlength="3" name="vote_frequency_hours" id="vote_frequency_hours" value="<?php echo $vote_frequency_hours; ?>" <?php echo $display_class; ?> />

										<span class="description"><?php _e('Allows to change the Voting Frequency.','voting-contest'); ?></span>
									</td>
								</tr>

								<tr valign="top">
									<th  scope="row"><label for="vote_votingtype"><?php _e('User Can Vote For','voting-contest'); ?> </label></th>
									<td colspan="2">
										<input type="radio" value="0" id="vote_votingtype_0" name="vote_votingtype"  <?php echo ($option['contest']['vote_votingtype'] == 'on' || $option['contest']['vote_votingtype'] == 0)?'checked':''; ?>/>
										<label for="vote_votingtype_0"><?php _e('Single','voting-contest'); ?></label>

										<input type="radio" value="1" id="vote_votingtype_1" name="vote_votingtype"  <?php echo ($option['contest']['vote_votingtype'] == 1)?'checked':''; ?>/>
										<label for="vote_votingtype_1"><?php _e('Multiple (Exclusive)','voting-contest'); ?></label>

										<input type="radio" value="2" id="vote_votingtype_2" name="vote_votingtype"  <?php echo ($option['contest']['vote_votingtype'] == '' || $option['contest']['vote_votingtype'] == 2)?'checked':''; ?>/>
										<label for="vote_votingtype_2"><?php _e('Multiple (Split)','voting-contest'); ?></label>
									</td>
								</tr>
							</table>
							<input type="submit" class="next button-primary" value="Continue" />
						</fieldset>
						<?php if($shortcode != '') { ?>
						<fieldset class="tab">
							<h2>READY!</h2>
							<table class="form-table">
								<tr valign="top">
									<th  scope="row"></th>
									<td colspan="2">
										<input type="text" required maxlength="3" name="vote_shortcode" id="vote_shortcode" value="<?php echo $shortcode; ?>" />
										<button onclick="copyShortcode();" class="button-primary">Copy shortcode</button>
									</td>
								</tr>
							</table>
							<input type="button" class="button-primary new_post" value="Create Page" />
							<input type="button" class="button-primary overview" value="Plugin Overview" />
							<input type="button" class="button-primary goto_dash" value="I'm Finished Here!" />
						</fieldset>
						<?php } ?>
					</form>
				</div>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						var current_fs, next_fs, previous_fs; //fieldsets

						jQuery(".next").click(function(){
							var form = jQuery("#myform");
							form.ow_vote_validate({
								errorElement: 'span',
								errorClass: 'help-block',
								highlight: function(element, errorClass, validClass) {
									jQuery(element).addClass("has-error");
								},
								unhighlight: function(element, errorClass, validClass) {
									jQuery(element).removeClass("has-error");
								},
								errorPlacement: function(error, element) {
										// Don't show error
								},
								rules: {
									contest_name: {
										required: true,
									},
								},
								messages: {
									contest_name: {
										required: "",
									},
								}
							});
							if (form.ow_vote_valid() === true){
								current_fs = jQuery(this).parent();
								next_fs = jQuery(this).parent().next();

								//activate next step on progressbar using the index of next_fs
								jQuery(".ow-setup-steps li").eq(jQuery("fieldset").index(next_fs)).addClass("active");
								jQuery(".ow-setup-steps li").eq(jQuery("fieldset").index(current_fs)).addClass("done");

								//show the next fieldset
								next_fs.show();
								//hide the current fieldset with style
								current_fs.hide();
							}
						});

						jQuery('#vote_frequency').change(function() {
							if(this.value == 2){
								jQuery('#vote_frequency_hours').css("visibility","visible");
							}
							else{
								jQuery('#vote_frequency_hours').css("visibility","hidden");
							}
						});

						jQuery('#vote_frequency_hours').keyup(function(){
							var val = jQuery('#vote_frequency_hours').val();
							if (val != "" && val != 0 ) {
							   jQuery('#vote_frequency option[value="2"]').text('Every '+val+' Hours');
							}
							else{
								jQuery('#vote_frequency option[value="2"]').text('Every _____ Hours');
							}
						});

						jQuery('.show_music_man').hide();
						var imgcontest = jQuery('#imgcontest').val();
						if(imgcontest == 'music'){
							jQuery('.show_music_man').show();
						}
						else{
							jQuery('.show_music_man').hide();
						}

						jQuery('#imgcontest').change(function() {

							if(jQuery(this).val() == 'music'){
								jQuery('.show_music_man').show();
							}
							else{
								jQuery('.show_music_man').hide();
							}
						});

						<?php if($shortcode != '') { ?>
						jQuery(".ow-setup-steps li").last().addClass("active");
						jQuery(".ow-setup-steps li:not(:last-child)").addClass("done");

						var new_post = "<?php echo admin_url('post-new.php?post_type=page'); ?>";
						var contestants = "<?php echo admin_url('admin.php?page=contestants'); ?>";
						var dash = "<?php echo admin_url(); ?>";

						jQuery('.new_post').on('click', function(){
							window.location = new_post;
						});

						jQuery('.overview').on('click', function(){
							window.location = contestants;
						});

						jQuery('.goto_dash').on('click', function(){
							window.location = dash;
						});
						<?php } ?>
					});

					function copyShortcode() {
						var copyText = document.getElementById("vote_shortcode");
						copyText.select();
						document.execCommand("copy");
						alert('Shortcode copied to clipboard');
					}
				</script>
			</body>
		</html>
		<?php
	}
}else
die("<h2>".__('Failed to load Voting admin contest settings view','voting-contest')."</h2>");

?>
