<?php
$filters = array(
    array('val' => '', 'label' => 'No Filter'),
    array('label' => '1977', 'val' => ' _1977'),
    array('label' => 'Aden', 'val' => ' aden'),
    array('label' => 'Black & White', 'val' => ' blackwhite'),
    array('label' => 'Brannan', 'val' => ' brannan'),
    array('label' => 'Brooklyn', 'val' => ' brooklyn'),
    array('label' => 'Clarendon', 'val' => ' clarendon'),
    array('label' => 'Earlybird', 'val' => ' earlybird'),
    array('label' => 'Gingham', 'val' => ' gingham'),
    array('label' => 'Hudson', 'val' => ' hudson'),
    array('label' => 'Inkwell', 'val' => ' inkwell'),
    array('label' => 'Kelvin', 'val' => ' kelvin'),
    array('label' => 'Lark', 'val' => ' lark'),
    array('label' => 'Lo-Fi', 'val' => ' lofi'),
    array('label' => 'Maven', 'val' => ' maven'),
    array('label' => 'Mayfair', 'val' => ' mayfair'),
    array('label' => 'Moon', 'val' => ' moon'),
    array('label' => 'Nashville', 'val' => ' nashville'),
    array('label' => 'Perpetua', 'val' => ' perpetua'),
    array('label' => 'Reyes', 'val' => ' reyes'),
    array('label' => 'Rise', 'val' => ' rise'),
    array('label' => 'Slumber', 'val' => ' slumber'),
    array('label' => 'Stinson', 'val' => ' stinson'),
    array('label' => 'Toaster', 'val' => ' toaster'),
    array('label' => 'Valencia', 'val' => ' valencia'),
    array('label' => 'Walden', 'val' => ' walden'),
    array('label' => 'Willow', 'val' => ' willow'),
    array('label' => 'X-pro II', 'val' => ' xpro2')
);
?>

<div class="csmm-tile" id="design-background">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Background</div>
        <p>The background image makes or breaks the whole page. Take your time to choose a perfect image from our gallery of 400,000+ images and then make it pop by using filters. If you don't have much content to show, then choose a video background.</p>

        <div class="csmm-section-content">

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="background_type" class="csmm-strong">Background Type</label>
                    <select name="background_type" id="background_type">
                        <?php
                        $bg_type = array(
                            array('val' => 'image', 'label' => 'Image'),
                            array('val' => 'video', 'label' => 'Video'),
                        );
                        csmm_create_select_options($bg_type, $signals_csmm_options['background_type']);  ?>
                    </select>

                    <p class="csmm-form-help-block">Video background draws attention away from the content, so use it wisely, only in situations when you don't have much content to put on the page. In all other cases, an image background is a better choice.</p>
                </div>
            </div>


            <div class="background-type background-type-video">
                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="background_video" class="csmm-strong">Background Video ID</label>
                        <input type="text" name="background_video" id="background_video" value="<?php esc_attr_e($signals_csmm_options['background_video']); ?>" placeholder="UFHQzF593ak" class="csmm-form-control">
                        <p class="csmm-form-help-block">Only YouTube videos are supported. Enter only the video ID, ie: <i>UFHQzF593ak</i>, found in the YouTube URL. Video is played muted and looped.<br>If the video ID is valid a preview will be shown below.</p>

                        <div id="video-preview" class="rise">
                            <div class="video-container"></div>
                        </div>
                    </div>

                    <div class="csmm-form-group">
                        <label for="background_video_filter" class="csmm-strong">Background Video Filter</label>
                        <select name="background_video_filter" id="background_video_filter">
                            <?php csmm_create_select_options($filters, $signals_csmm_options['background_video_filter']);  ?>
                        </select>
                        <p class="csmm-form-help-block">The filter is immediately applied to the video preview.</p>
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-upload-group csmm-clearfix">
                        <div class="csmm-form-group border-fix">
                            <div class="csmm-upload-element">
                                <label class="csmm-strong">Mobile Video Fallback Image</label>
                                <?php
                                if (stripos($signals_csmm_options['background_video_fallback'], 'undefined index') !== false) {
                                    $signals_csmm_options['background_video_fallback'] = '';
                                }
                                if (!empty($signals_csmm_options['background_video_fallback'])) : // If the image url is present, show the image. Else, show the default upload text 
                                ?>
                                    <span class="csmm-preview-area" id="video-fallback-preview"><img src="<?php echo esc_attr($signals_csmm_options['background_video_fallback']); ?>" /></span>
                                <?php else : ?>
                                    <span class="csmm-preview-area" id="video-fallback-preview">Select an image from our 400,000+ images gallery, or upload your own</span>
                                <?php endif; ?>

                                <input type="hidden" name="signals_csmm_signals_fallback" id="signals_csmm_signals_fallback" class="mm_upload_image_input" value="<?php esc_attr_e($signals_csmm_options['background_video_fallback']); ?>">
                                <button type="button" name="signals_fallback_upload" id="signals_fallback_upload" class="csmm-btn csmm-upload mm-free-images" style="margin-top: 4px">Open images gallery</button>
                                <span class="csmm-upload-append">
                                    <?php if (!empty($signals_csmm_options['background_video_fallback'])) : ?>
                                        &nbsp;<a href="javascript: void(0);" class="csmm-remove-image"><?php _e('Remove', 'signals'); ?></a>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="background-type background-type-image">
                <div class="csmm-upload-group csmm-clearfix">
                    <div class="csmm-form-group border-fix">
                        <div class="csmm-upload-element">
                            <label class="csmm-strong">Background Image</label>
                            <?php if (!empty($signals_csmm_options['bg_cover'])) : // If the image url is present, show the image. Else, show the default upload text 
                            ?>
                                <span class="csmm-preview-area" id="background-preview"><img src="<?php echo esc_attr($signals_csmm_options['bg_cover']); ?>" /></span>
                            <?php else : ?>
                                <span class="csmm-preview-area" id="background-preview">Select an image from our 400,000+ images gallery, or upload your own</span>
                            <?php endif; ?>

                            <input type="hidden" name="signals_csmm_bg" id="signals_csmm_bg" class="mm_upload_image_input" value="<?php esc_attr_e($signals_csmm_options['bg_cover']); ?>">
                            <button type="button" name="signals_bg_upload" id="signals_bg_upload" class="csmm-btn csmm-upload mm-free-images" style="margin-top: 4px">Open images gallery</button>
                            <span class="csmm-upload-append">
                                <?php if (!empty($signals_csmm_options['bg_cover'])) : ?>
                                    &nbsp;<a href="javascript: void(0);" class="csmm-remove-image"><?php _e('Remove', 'signals'); ?></a>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix  ">
                    <div class="csmm-form-group">
                        <label for="background_size_opt" class="csmm-strong">Background Image Size</label>
                        <select name="background_size_opt" id="background_size_opt">
                            <?php
                            $bkg_opt = array(
                                array('val' => 'auto', 'label' => 'Auto'),
                                array('val' => 'contain', 'label' => 'Contain'),
                                array('val' => 'cover', 'label' => 'Cover'),
                            );
                            csmm_create_select_options($bkg_opt, $signals_csmm_options['background_size_opt']);  ?>
                        </select>

                        <p class="csmm-form-help-block">Auto - display image in original size; Contain - resize the image so it's fully visible; Cover - resize the image to cover the entire screen.</p>
                    </div>

                    <div class="csmm-form-group">
                        <label for="background_image_filter" class="csmm-strong">Background Image Filter</label>
                        <select name="background_image_filter" id="background_image_filter">
                            <?php csmm_create_select_options($filters, $signals_csmm_options['background_image_filter']);  ?>
                        </select>
                        <p class="csmm-form-help-block">The filter is immediately applied to the background image thumbnail above for a preview.</p>
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="background_position" class="csmm-strong">Background Image Position</label>
                        <select name="background_position" id="background_position">
                            <?php
                            $positions = array(
                                array('val' => '', 'label' => 'Auto'),
                                array('val' => 'left top', 'label' => 'Top left'),
                                array('val' => 'center top', 'label' => 'Top center'),
                                array('val' => 'right top', 'label' => 'Top right'),
                                array('val' => 'left center', 'label' => 'Center left'),
                                array('val' => 'center center', 'label' => 'Center'),
                                array('val' => 'right center', 'label' => 'Center right'),
                                array('val' => 'left bottom', 'label' => 'Bottom left'),
                                array('val' => 'center bottom', 'label' => 'Bottom center'),
                                array('val' => 'right bottom', 'label' => 'Bottom right'),
                            );
                            csmm_create_select_options($positions, $signals_csmm_options['background_position']);  ?>
                        </select>

                        <p class="csmm-form-help-block">If defined, the position defines the screen and image corners that will be aligned. It works best with the "cover" size option.</p>
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_csmm_color" class="csmm-strong"><?php _e('Background Color', 'signals'); ?></label>
                        <input name="signals_csmm_color" id="signals_csmm_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['bg_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                        <p class="csmm-form-help-block">If the background image is set, the color will not be visible once the image is loaded.</p>
                    </div>
                </div>
            </div> <!-- #image settings -->

        </div>
    </div>
</div><!-- #background -->