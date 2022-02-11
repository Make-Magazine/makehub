<div class="csmm-tile" id="seo">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">SEO</div>
        <p>Carefully craft your content in order to rank your site as best as possible from day one. Use the SEO Analysis tool to improve weak areas.</p>

        <div class="csmm-double-group clearfix" style="margin-top:20px; padding-bottom:20px; display: flex;">

            <div class="mm-seo-snippet csmm-form-group">
                <div class="csmm-strong">SEO Snippet Preview</div><br />
                <div class="mm-seo-snippet-preview">
                    <h3 id="mm-seo-snippet-title"><?php echo esc_attr_e(stripslashes($signals_csmm_options['title'])); ?></h3>
                    <cite id="mm-seo-snippet-url"><?php echo home_url(); ?></cite>
                    <div id="mm-seo-snippet-description"><?php echo esc_attr_e(stripslashes($signals_csmm_options['description'])); ?></div>
                    <br />
                    <label for="signals_csmm_target_keyword" class="csmm-strong">Target Keyword</label>
                    <input type="text" name="signals_csmm_target_keyword" id="signals_csmm_target_keyword" value="<?php echo esc_attr_e(stripslashes($signals_csmm_options['target_keyword'])); ?>" placeholder="Pick the main keyword or phrase that this page focuses on" class="csmm-form-control">
                    <div id="mm-seo-gage"></div>
                </div>

            </div>
            <div class="mm-seo-analysis csmm-form-group">
                <div class="csmm-strong">SEO Analysis</div><br />
                <div id="mm-seo-results" class="clearfix"></div>
            </div>
        </div>

        <div class="csmm-double-group clearfix" id="seotitle">
            <div class="csmm-form-group">
                <label for="signals_csmm_title" class="csmm-strong">SEO Title</label>
                <input type="text" name="signals_csmm_title" id="signals_csmm_title" data-site-title="<?php echo get_bloginfo('name'); ?>" value="<?php echo esc_attr_e(stripslashes($signals_csmm_options['title'])); ?>" placeholder="%sitetitle% is coming soon" class="csmm-form-control">
                <div class="mm-seo-progress " id="mm-seo-progress-title">
                    <div class="mm-seo-progress-bar"></div>
                </div>
                <p class="csmm-form-help-block">Recommended format: <i>Primary Keyword - Secondary Keyword - Brand Name</i> with length up to 60 characters.<br>Use <i>%sitetitle%</i> and <i>%sitetagline%</i> to grab settings from WP.</p>
            </div>

            <div class="csmm-form-group">
                <label for="signals_csmm_description" class="csmm-strong">Meta Description</label>
                <textarea type="text" name="signals_csmm_description" id="signals_csmm_description" data-site-description="<?php echo get_bloginfo('description'); ?>" rows="3" class="csmm-form-control"><?php echo esc_attr_e(stripslashes($signals_csmm_options['description'])); ?></textarea>
                <div class="mm-seo-progress " id="mm-seo-progress-description">
                    <div class="mm-seo-progress-bar"></div>
                </div>
                <p class="csmm-form-help-block">Write for humans, not search engines! This text will incite people to click on your site on Google. The length should be 50 - 300 characters.</p>
            </div>
        </div>

        <div class="csmm-double-group clearfix" id="blockse">
            <div class="csmm-form-group">
                <label for="signals_csmm_excludese" class="csmm-strong">Exclude Search Engines?</label>
                <input type="checkbox" class="csmm-form-ios" name="signals_csmm_excludese" id="signals_csmm_excludese" value="1" <?php checked('1', $signals_csmm_options['exclude_se']); ?>>
                <p class="csmm-form-help-block">If enabled, search engines will always see your normal page, never the coming soon one. We do not recommend enabling this feature.</p>
            </div>

            <div class="csmm-form-group">
                <label for="signals_csmm_blockse" class="csmm-strong">Block Search Engines</label>
                <input type="checkbox" class="csmm-form-ios" name="signals_csmm_blockse" id="signals_csmm_blockse" value="1" <?php checked('1', $signals_csmm_options['block_se']); ?>>

                <p class="csmm-form-help-block">If your site is already indexed and you're just taking it down for a while, enable this option. It temporarily discourages search engines from crawling the site by telling them it's unavailable by sending a <i>503 Service Unavailable</i> response.</p>

            </div>
        </div>


        <div class="csmm-double-group csmm-clearfix">
            <div class="csmm-form-group border-fix">
                <div class="csmm-upload-element">
                    <label class="csmm-strong">Favicon Image</label>

                    <?php if (!empty($signals_csmm_options['favicon'])) : // If the image url is present, show the image. Else, show the default upload text 
                    ?>
                        <span class="csmm-preview-area"><img src="<?php echo esc_attr($signals_csmm_options['favicon']); ?>" /></span>
                    <?php else : ?>
                        <span class="csmm-preview-area">Select an image or upload a new one</span>
                    <?php endif; ?>

                    <input type="hidden" name="signals_csmm_favicon" id="signals_csmm_favicon" value="<?php esc_attr_e($signals_csmm_options['favicon']); ?>">
                    <button type="button" name="signals_favicon_upload" id="signals_favicon_upload" class="csmm-btn csmm-upload" style="margin-top: 4px">Select</button>

                    <span class="csmm-upload-append">
                        <?php if (!empty($signals_csmm_options['favicon'])) : ?>
                            &nbsp;<a href="javascript: void(0);" class="csmm-remove-image">Remove</a>
                        <?php endif; ?>
                    </span>
                    <p class="csmm-form-help-block" style="padding: 0 10px;">Make sure the image is square (1:1 ratio). PNG will do just fine, about 64x64px. Don't go over 256x256px.</p>
                </div>
            </div>

            <div class="csmm-form-group border-fix">
                <div class="csmm-upload-element">
                    <label class="csmm-strong">Social Preview Image</label>
                    <?php if (!empty($signals_csmm_options['social_preview'])) : ?>
                        <span class="csmm-preview-area"><img src="<?php echo esc_attr($signals_csmm_options['social_preview']); ?>" /></span>
                    <?php else : ?>
                        <span class="csmm-preview-area">Select an image or upload a new one</span>
                    <?php endif; ?>
                    <input type="hidden" name="signals_csmm_social_preview" id="signals_csmm_social_preview" value="<?php esc_attr_e($signals_csmm_options['social_preview']); ?>">
                    <button type="button" name="signals_social_preview_upload" id="signals_social_preview_upload" class="csmm-btn csmm-upload" style="margin-top: 4px">Select</button>

                    <span class="csmm-upload-append">
                        <?php if (!empty($signals_csmm_options['favicon'])) : ?>
                            &nbsp;<a href="javascript: void(0);" class="csmm-remove-image">Remove</a>
                        <?php endif; ?>
                    </span>
                    <p class="csmm-form-help-block" style="padding: 0 10px;">Image ratio should be 1:2. Facebook recommends 1200x630px. Minimum should be 600x315px.</p>
                </div>
            </div>
            <div class="csmm-clearfix"></div>
            <p>To refresh cached images and content you can use the <a href="<?php echo 'https://developers.facebook.com/tools/debug/?q=' . urlencode(site_url()); ?> " target="_blank">Facebook Debugger</a> and <a href="https://cards-dev.twitter.com/validator" target="_blank">Twitter Card Inspector</a>.</p>
            
        </div>

        <div class="csmm-double-group csmm-clearfix">
            <div class="csmm-form-group">
                <label for="signals_csmm_analytics" class="csmm-strong">Google Analytics Tracking ID</label>
                <input name="signals_csmm_analytics" id="signals_csmm_analytics" placeholder="UA-123456-99" value="<?php echo esc_attr(csmm_convert_ga($signals_csmm_options['analytics'])); ?>">

                <p class="csmm-form-help-block">Enter only the Google Analytics Profile ID, ie: UA-123456-99. You'll find it in the GA tracking code.</p>
            </div>

            <div class="csmm-form-group">
                <label for="tracking_pixel" class="csmm-strong">Tracking Pixel &amp; 3rd Party Analytics Code</label>
                <textarea name="tracking_pixel" id="tracking_pixel" placeholder="Tracking pixel code or any 3rd party tracking code, including <script> tags"><?php echo esc_textarea($signals_csmm_options['tracking_pixel']); ?></textarea>
                <p class="csmm-form-help-block">Copy&amp;paste the complete code, including the opening and closing <i>&lt;script&gt;</i> tags. The code is outputted in the page's header section.</p>
            </div>
        </div>

        <div class="csmm-double-group clearfix">
            <div class="csmm-form-group"><label class="csmm-strong" for="facebook_site">Facebook Page URL</label>
                <input class="csmm-form-control" placeholder="https://www.facebook.com/page-name-123/" type="text" id="facebook_site" name="facebook_site" value="<?php echo esc_attr($signals_csmm_options['facebook_site']); ?>">
                <p class="csmm-form-help-block">Full URL to your Facebook page, including the <i>https://</i> prefix.</p>
            </div>

            <div class="csmm-form-group"><label class="csmm-strong" for="twitter_site">Twitter @username</label>
                <input class="csmm-form-control" placeholder="@mytwitterhandle" type="text" id="twitter_site" name="twitter_site" value="<?php echo esc_attr($signals_csmm_options['twitter_site']); ?>">
                <p class="csmm-form-help-block">Twitter handle name including the @ sign, ie: @john.</p>
            </div>
        </div>

    </div>
</div><!-- #seo -->