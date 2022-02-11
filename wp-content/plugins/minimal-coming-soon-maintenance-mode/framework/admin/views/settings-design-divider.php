<div class="csmm-tile" id="design-divider">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Divider</div>
        <div class="csmm-section-content">
            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_divider_height" class="csmm-strong">Divider Height</label>
                    <input type="hidden" name="signals_csmm_divider_height" value="<?php esc_attr_e($signals_csmm_options['divider_height']); ?>" data-min="0" data-max="30" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">This is the visible height/thickness of the divider. Default: 1px;</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_divider_color" class="csmm-strong">Divider Color</label>
                    <input type="text" name="signals_csmm_divider_color" id="signals_csmm_divider_color" value="<?php echo csmm_hex2rgba(!empty($signals_csmm_options['divider_color']) ? $signals_csmm_options['divider_color'] : '000000'); ?>" class="csmm-color csmm-form-control color {required:false}">
                    <p class="csmm-form-help-block">Make it stand out!</p>
                </div>
            </div>


            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_divider_margin_top" class="csmm-strong">Divider Margin Top</label>
                    <input type="hidden" name="signals_csmm_divider_margin_top" value="<?php esc_attr_e($signals_csmm_options['divider_margin_top']); ?>" data-min="0" data-max="300" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">The space above the divider. Default: 10px;</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_divider_margin_bottom" class="csmm-strong">Divider Margin Bottom</label>
                    <input type="hidden" name="signals_csmm_divider_margin_bottom" value="<?php esc_attr_e($signals_csmm_options['divider_margin_bottom']); ?>" data-min="0" data-max="300" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">The space below the divider. Default: 10px;</p>
                </div>
            </div>
            
        </div>
    </div>
</div><!-- #content -->