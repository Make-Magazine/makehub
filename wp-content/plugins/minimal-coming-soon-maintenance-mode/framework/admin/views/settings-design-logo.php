<div class="csmm-tile" id="design-logo">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Logo Module</div>
        <p>Branding is important from day zero, so make sure you display your logo properly.</p>

        <div class="csmm-section-content">
            <div class="csmm-upload-group csmm-clearfix">
                <div class="csmm-form-group border-fix">
                    <div class="csmm-upload-element">
                        <label class="csmm-strong">Logo</label>

                        <?php if (!empty($signals_csmm_options['logo'])) : // If the image url is present, show the image. Else, show the default upload text 
                        ?>
                            <span class="csmm-preview-area"><img src="<?php echo esc_attr($signals_csmm_options['logo']); ?>" /></span>
                        <?php else : ?>
                            <span class="csmm-preview-area"><?php _e('Select an image or upload a new one', 'signals'); ?></span>
                        <?php endif; ?>

                        <input type="hidden" name="signals_csmm_logo" id="signals_csmm_logo" class="mm_upload_image_input" value="<?php esc_attr_e($signals_csmm_options['logo']); ?>">
                        <button type="button" name="signals_logo_upload" id="signals_logo_upload" class="csmm-btn csmm-upload" style="margin-top: 4px">Choose an image</button>

                        <span class="csmm-upload-append">
                            <?php if (!empty($signals_csmm_options['logo'])) : ?>
                                &nbsp;<a href="javascript: void(0);" class="csmm-remove-image"><?php _e('Remove', 'signals'); ?></a>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="logo_max_height" class="csmm-strong">Maximum Logo Height</label>
                    <input type="hidden" name="logo_max_height" value="<?php esc_attr_e($signals_csmm_options['logo_max_height']); ?>" data-min="10" data-max="300" data-step="5" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">Maximum logo height in pixels. The width is calculated automatically. Default: 150px.</p>
                </div>
                <div class="csmm-form-group">
                    <label for="logo_title" class="csmm-strong">Logo Title</label>
                    <input type="text" name="logo_title" id="logo_title" value="<?php esc_attr_e($signals_csmm_options['logo_title']); ?>" placeholder="Our new site is coming soon" class="csmm-form-control">
                    <p class="csmm-form-help-block">Use the built-in <a href="#seo" class="csmm-change-tab">SEO analyzer</a> to check the quality of your title.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="csmm_logo_link_url" class="csmm-strong">Logo Link URL</label>
                    <input type="text" name="csmm_logo_link_url" id="csmm_logo_link_url" value="<?php esc_attr_e($signals_csmm_options['logo_link_url']); ?>" placeholder="" class="csmm-form-control">
                    <p class="csmm-form-help-block">If you want your logo to link somewhere, enter the URL here</p>
                </div>
            </div>

        </div>
    </div>
</div><!-- #logo -->