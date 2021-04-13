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
<div class='single-espresso_people'>
    <h2>My Information</h2>
    <?php
    global $current_user;
    $current_user = wp_get_current_user();
    $userEmail = (string) $current_user->user_email;

    $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);
    $user_website = $user_website = $userFname = $userLname = $userBio = $person_img = '';
    $user_social = array();
    if ($person) {
        $userFname = $person->fname();
        $userLname = $person->lname();

        $post_id = $person->ID();
        $user_website = get_field("website", $post_id);
        $user_social = get_field("social_links", $post_id);
        $userBio = get_field("facilitator_info", $post_id);
        $person_img = get_the_post_thumbnail_url($post_id);
    }
    ?>
    <div class="host-wrapper">        
        <div class="host-photo">
            <img width="624" height="662" src="<?php echo $person_img; ?>">	
        </div>

        <div class="host-meta">
            <h1 class="host-title"><?php echo $userFname . ' ' . $userLname; ?></h1>

            <div class="host-email">
                <i class="fas fa-link" aria-hidden="true"></i>
                <a href="<?php $user_website; ?>" target="_blank"><?php echo $user_website; ?>/</a>
            </div>            
            <?php
            if ($user_social) {
                ?>
                <span class="social-links">
                    <b>See more of <?php echo $userFname . ' ' . $userLname; ?> at:</b>
                    <?php
                    foreach ($user_social as $link) {
                        if ($link['social_link'] != '') {
                            echo '<a href="' . $link['social_link'] . ' target="_blank">*</a>';
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
        <i class="fas fa-user-edit fa-2x" aria-hidden="true"></i>
    </div>    
</div>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
<h2>My Events</h2>
<?php gravityview_before($gravityview); ?>
<div class="<?php gv_container_class('gv-list-container gv-list-view gv-list-multiple-container', true, $gravityview); ?>">    
    <?php gravityview_header($gravityview); ?>
