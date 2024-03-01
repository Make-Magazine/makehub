<div class="lms-header-instructor">
    <?php
    if (class_exists('BuddyPress')) {


        $bb_single_meta_pfx = 'bb_single_meta_off';

        ?>

        <div class="bb-about-instructor <?php echo $bb_single_meta_pfx; ?>">
            <div class="flex">
                <div class="bb-avatar-wrap">
                    <?php
                    $user_id = apply_filters(
                            'user_id',
                            ( ( isset($user_id) ) && (!empty($user_id) ) ? $user_id : get_the_author_meta('ID'))
                    );
                    $avatar = bp_core_fetch_avatar(array('html'=>false,'item_id' => $user_id, 'email' => get_the_author_meta('email', $user_id), 
                        'width' => 15, 'height' => 15, 'avatar_dir'=>'avatars'));
                    
                    if (!empty($avatar)) :
                        if (class_exists('BuddyPress')) {
                            ?>
                            <a href="<?php echo bp_members_get_user_url($user_id); ?>">
                                <?php
                            } else {
                                ?>
                                <a href="<?php echo get_author_posts_url($user_id, get_the_author_meta('user_nicename', $user_id)); ?>">
                                    <?php }
                                ?>
                                <img class="round avatar" src="<?php echo $avatar; ?>"/>
                            </a>
                            <?php
                        endif;
                        ?>
                </div>
                <div class="bb-content-wrap">
                    <h5>
                        <?php
                            if (class_exists('BuddyPress')) {
                                ?>
                                <a href="<?php echo bp_members_get_user_url($user_id); ?>">
                                    <?php
                                } else {
                                    ?>
                                    <a href="<?php echo get_author_posts_url($user_id, get_the_author_meta('user_nicename', $user_id)); ?>">
                                <?php } ?>
                                    <?php echo get_the_author_meta('display_name', $user_id); ?>
                                </a>
                                <span class="bb-about-instructor-date"><?php bp_core_get_format_date(get_the_date()); ?></span>
                    </h5>
					<div class="author-description">
						<?php
						if ( class_exists( 'BuddyPress' ) ) {
							$user_data_args = array(
								'field'   => 'description',
								'user_id' => $user_id,
							);
							echo bp_profile_field_data($user_data_args);
						}
						?>
					</div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>