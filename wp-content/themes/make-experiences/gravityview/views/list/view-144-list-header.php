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
	jQuery(document).ready(function(){
		jQuery("#flip-card").css("min-height", jQuery(".host-wrapper.front").outerHeight() + 40);
		renderPage();
	});
	function renderPage() {
	  // initialize the acf script
	  acf.do_action('ready', $('body'));

	  // will be used to check if a form submit is for validation or for saving
	  let isValidating = false;

	  acf.add_action('validation_begin', () => {
		  isValidating = true;
	  });

	  acf.add_action('submit', ($form) => {
		  isValidating = false;
	  });

	  jQuery('#acf_edit_facilitator').on('submit', (e) => {
		let $form = jQuery(e.target);
		e.preventDefault();
		// if we are not validating, save the form data with our custom code.
		if( !isValidating ) {
			// lock the form
			acf.validation.lockForm($form);
			jQuery.ajax({
				url: window.location.href,
				method: 'post',
				data: $form.serialize(),
				success: () => {
					location.reload();
				}
			});
		}
	  });
	}
</script>
<a class="universal-btn" style="float:right" href="/submit-event/" target="_blank">Submit a New Event</a>

<?php
global $current_user;
$current_user = wp_get_current_user();
$userEmail = (string) $current_user->user_email;
if (class_exists(EEM_Person::class)) {
	$person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);
}

if ($person) {
    $personID = $person->ID();
    $user_website = $userFname = $userLname = $userBio = $person_img = '';
    $user_social = array();

    $userFname = $person->fname();
    $userLname = $person->lname();

    $post_id = $person->ID();

    $user_website = get_field("website", $post_id);
    $user_social = get_field("social_links", $post_id);
    $user_social_links = [];

    $userBio = get_field("facilitator_info", $post_id);
    $facilitator_img = get_the_post_thumbnail_url($post_id);
    ?>
    <div class='single-espresso_people'>
        <h2><a href="<?php echo get_permalink($post_id); ?>" target="_blank">Public Facilitator Information</a></h2>

        <div id="flip-card" class="host">
            <div class="host-wrapper front">
                <div class="host-photo">
                    <?php echo get_the_post_thumbnail($post_id); ?>
                </div>
                <div class="host-meta">
                    <h1 class="host-title"><?php echo $userFname . ' ' . $userLname; ?></h1>
                    <?php if ($user_website) { ?>
                        <div class="host-email">
                            <i class="fas fa-link" aria-hidden="true"></i>
                            <a href="<?php $user_website; ?>" target="_blank"><?php echo $user_website; ?>/</a>
                        </div>
                        <?php
                    }
                    if ($user_social) {
                        ?>
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
                        <?php
                    }
                    ?>
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
                    <?php
                    acf_form_head();
                    acf_form(array(
                        'post_id' => $personID,
                        'post_title' => false,
                        'post_content' => false,
						'form_attributes' => array('id' => 'acf_edit_facilitator'),
                        'submit_value' => __('Update Information'),
                    ));
                    ?>
                </div>
                <i class="fas fa-undo-alt fa-2x flip-toggle" aria-hidden="true"></i>
            </div>
        </div>
    </div>

<?php } ?>

<?php gravityview_before($gravityview); ?>
<div class="<?php gv_container_class('gv-list-container gv-list-view gv-list-multiple-container', true, $gravityview); ?>">
    <?php gravityview_header($gravityview); ?>
