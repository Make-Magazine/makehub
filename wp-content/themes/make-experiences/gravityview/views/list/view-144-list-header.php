<?php
/**
 * The header for the output list.
 *
 * @global \GV\Template_Context $gravityview
 */
if (!isset($gravityview) || empty($gravityview->template)) {
    gravityview()->log->error('{file} template loaded without context', array('file' => __FILE__));
    return;
}
?>
<script>
    var myModal = document.getElementById('myModal')
    var myInput = document.getElementById('myInput')

    myModal.addEventListener('shown.bs.modal', function () {
      myInput.focus()
    })
</script>    
<a class="universal-btn" style="float:right" href="/submit-event/" target="_blank">Submit a New Event</a>

<?php
global $current_user;
$current_user = wp_get_current_user();
$userEmail = (string) $current_user->user_email;

$person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);
if(!empty($person)) {
?>
<div class='single-espresso_people'>
    <h2>My Information</h2>
    
    <?php
	$user_website = $user_website = $userFname = $userLname = $userBio = $person_img = '';
    $user_social = array();
    if ($person) {
        $userFname = $person->fname();
        $userLname = $person->lname();

        $post_id = $person->ID();
        $user_website = get_field("website", $post_id);
        $user_social = get_field("social_links", $post_id);
		$user_social_links = [];

        $userBio = get_field("facilitator_info", $post_id);
    }
    ?>
	<div id="flip-card" class="host">
		<div class="host-wrapper front">        
			<div class="host-photo">
				<?php get_the_post_thumbnail($post_id); ?>
			</div>
			<div class="host-meta">
				<h1 class="host-title"><?php echo $userFname . ' ' . $userLname; ?></h1>
				<?php if($user_website) { ?>
					<div class="host-email">
						<i class="fas fa-link" aria-hidden="true"></i>
						<a href="<?php $user_website; ?>" target="_blank"><?php echo $user_website; ?>/</a>
					</div>            
				<?php
				}
				if ($user_social) { ?>
					<span class="social-links">
						<b>See more of <?php echo $userFname . ' ' . $userLname; ?> at:</b>
						<?php
						foreach ($user_social as $link) {
							if ($link['social_link'] != '') {
								echo '<a href="' . $link['social_link'] . ' target="_blank">*</a>';
								$user_social_links[] = $link['social_link'];
							}
						}
						?>
					</span>
				<?php } ?>
				<div class="host-bio">                
					<?php
					$postwithbreaks = wpautop($userBio, true);
					echo $postwithbreaks;
					?>
				</div>
			</div>
			<i class="fas fa-user-edit fa-2x flip-toggle" aria-hidden="true"></i>
		</div> 
		<div class="host-wrapper back smaller">
			<div class="host-form-wrapper">
				<h4>Edit Facilitator Profile</h4>
				<form id="facilitator-edit">
					<label for="host-first-name">First Name</label>
					<input type="text" name="host-first-name" id="host-first-name" value="<?php echo $userFname; ?>">
					<label for="host-last-name">Last Name</label>
					<input type="text" name="host-last-name" id="host-last-name" value="<?php echo $userLname; ?>">
					<label for="host-bio">Bio</label>
					<textarea name="host-bio" id="host-bio" rows="4"><?php echo $postwithbreaks; ?></textarea>
					<label for="host-website">Website</label>
					<input type="text" name="host-website" id="host-website" value="<?php echo $user_website; ?>">
					<label for="host-social">Social</label>
					<textarea name="host-social" id="host-social" rows="4"><?php echo implode(PHP_EOL, $user_social_links); ?></textarea>
					<p>Enter new social links on separate lines</p>
					<input type="submit" value="Submit Changes">
				</form>
			</div>
			<i class="fas fa-undo-alt fa-2x flip-toggle" aria-hidden="true"></i>
		</div>
		
	</div>
	
</div>

<?php } ?>

<?php gravityview_before($gravityview); ?>
<div class="<?php gv_container_class('gv-list-container gv-list-view gv-list-multiple-container', true, $gravityview); ?>">    
    <?php gravityview_header($gravityview); ?>
