<div class="csmm-tile" id="design-progress">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Progress Bar Module</div>
        <p>A great way to convey to visitors how much of your project is done. Don't forget to update the percentage as you move forward with the project.</p>

        <div class="csmm-section-content">

            <div class="_csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="progress_percentage" class="csmm-strong">Percentage of Completion</label>

                    <input type="hidden" name="progress_percentage" value="<?php esc_attr_e($signals_csmm_options['progress_percentage']); ?>" data-min="0" data-max="100" data-step="1" data-label="%val%%" class="csmm-slide-input">
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="progress_height" class="csmm-strong">Bar Height</label>
                    <input type="hidden" name="progress_height" value="<?php esc_attr_e($signals_csmm_options['progress_height']); ?>" data-min="10" data-max="70" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">Freely adjust to your page design.</p>
                </div>
                <div class="csmm-form-group">
                    <label for="progress_color" class="csmm-strong">Bar Color</label>
                    <input type="text" name="progress_color" id="progress_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['progress_color']); ?>" class="csmm-color csmm-form-control color {required:false}">

                    <p class="csmm-form-help-block">Use your primary site color, or something close to it.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="progress_label_size" class="csmm-strong">Label Size</label>
                    <input type="hidden" name="progress_label_size" value="<?php esc_attr_e($signals_csmm_options['progress_label_size']); ?>" data-min="4" data-max="30" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">The label displays the percentage number in the middle of the bar.</p>
                </div>
                <div class="csmm-form-group">
                    <label for="progress_label_color" class="csmm-strong">Label Color</label>
                    <input type="text" name="progress_label_color" id="progress_label_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['progress_label_color']); ?>" class="csmm-color csmm-form-control color {required:false}">

                    <p class="csmm-form-help-block">Make sure it's complementary to the bar color.</p>
                </div>
            </div>

        </div>
    </div>
</div><!-- #progress -->