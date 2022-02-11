<div class="csmm-tile" id="design-video">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Video Module</div>
        <p>Nothing captures visitors' attention like a great video! Although popular these days, be careful with the autoplay feature.</p>

        <div class="csmm-section-content">

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="video_type" class="csmm-strong">Video Type</label>
                    <select id="video_type" name="video_type">
                        <?php
                        $aspect_options = array(
                            array('val' => 'youtube', 'label' => __('YouTube', 'signals')),
                            array('val' => 'vimeo', 'label' => __('Vimeo', 'signals')),
                            array('val' => 'other', 'label' => __('Other', 'signals'))
                        );
                        csmm_create_select_options($aspect_options, $signals_csmm_options['video_type']);  ?>
                    </select>
                    <p class="csmm-form-help-block">For YouTube and Vimeo videos choose the corresponding type to make the most of our embed features. All other hosted videos should use "other".</p>
                </div>
            </div>

            <div class="video-container-youtube video-container-vimeo video-type-container">
                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="video_id" class="csmm-strong">Video ID</label>
                        <input type="text" name="video_id" id="video_id" value="<?php esc_attr_e($signals_csmm_options['video_id']); ?>" placeholder="ScMzIvxBSi4" class="csmm-form-control">
                        <p class="csmm-form-help-block">Write ONLY the video ID. For YouTube, it's alphanumeric (ie: ScMzIvxBSi4) and for Vimeo numeric (ie: 254733356).</p>
                    </div>
                </div>
                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="video_autoplay" class="csmm-strong"><?php _e('Autoplay Video', 'signals'); ?></label>
                        <input type="checkbox" class="csmm-form-ios" id="video_autoplay" name="video_autoplay" value="1" <?php checked('1', $signals_csmm_options['video_autoplay']); ?>>
                        <p class="csmm-form-help-block">If enabled, the video will automatically play on page load. Some visitors find this feature annoying, so mind your page type when enabling it.</p>
                    </div>

                    <div class="csmm-form-group">
                        <label for="video_minimal" class="csmm-strong"><?php _e('Clean/Minimal Interface', 'signals'); ?></label>
                        <input type="checkbox" class="csmm-form-ios" id="video_minimal" name="video_minimal" value="1" <?php checked('1', $signals_csmm_options['video_minimal']); ?>>
                        <p class="csmm-form-help-block">The clean interface looks great, very minimalistic, but removes most of the video controls. Especially on YouTube videos. </p>
                    </div>

                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="video_mute" class="csmm-strong"><?php _e('Mute Video', 'signals'); ?></label>
                        <input type="checkbox" class="csmm-form-ios" id="video_mute" name="video_mute" value="1" <?php checked('1', $signals_csmm_options['video_mute']); ?>>
                        <p class="csmm-form-help-block">If you're using the autoplay feature initially, muting the video is a good idea.</p>
                    </div>

                </div>

            </div>

            <div class="video-container-other video-type-container">
                <div class="csmm-form-group">
                    <label for="video_embed_code" class="csmm-strong">Video Embed Code</label>
                    <textarea name="video_embed_code" id="video_embed_code" placeholder="Video embed HTML code, including <iframe> tags"><?php echo esc_textarea($signals_csmm_options['video_embed_code']); ?></textarea>
                    <p class="csmm-form-help-block">Copy&amp;paste the complete embed HTML code provided by your video hosting service. It will most probably begin and end with &lt;iframe&gt; tags.</p>
                </div>
            </div>
        </div>
    </div>
</div><!-- #video-->