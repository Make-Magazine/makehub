<?php

/**
 * Support view for the plugin
 *
 * @link       http://www.webfactoryltd.com
 * @since      1.0
 */

?>

<div class="csmm-tile" id="support">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title"><?php _e('SUPPORT', 'signals'); ?></div>
        <?php
        if(csmm_get_rebranding() !== false){
            echo '<p>' . csmm_get_rebranding('support_content') . '</p>';
        } else {
            echo '<p>Please don\'t hesitate to get in touch if you need any help. Try to be as detailed as possible so we can provide the best answer in the first reply. If you\'re unable to use our support widget in the lower right corner - <a target="_blank" href="mailto:csmm@webfactoryltd.com">email us</a>.</p>';
            echo '<p><a href="#" class="button button-primary open-beacon">Contact Support</a></p>';
        }
        ?>
    </div>
</div><!-- #support -->