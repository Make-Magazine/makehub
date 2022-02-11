<div class="csmm-tile" id="design-countdown">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Countdown Module</div>
        <p>If you're doing a product launch or an event, this is a must-use module. Don't worry, you can always change the date if your plans change - no data is stored in users' cookies.</p>

        <div class="csmm-section-content">

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="countdown_date" class="csmm-strong">Countdown Date/Time</label>
                    <input type="text" name="countdown_date" id="countdown_date" value="<?php esc_attr_e($signals_csmm_options['countdown_date']); ?>" placeholder="yyyy/mm/dd" class="csmm-form-control datepicker" style="width: 50%; display: inline-block;"><span title="Open date & time picker" class="show-datepicker dashicons dashicons-calendar-alt"></span>
                    <p class="csmm-form-help-block">Time that will be counted down to - your launch date. Time is GMT+0.</p>
                </div>

            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="countdown_size" class="csmm-strong">Countdown Numbers Size</label>
                    <input type="hidden" name="countdown_size" value="<?php esc_attr_e($signals_csmm_options['countdown_size']); ?>" data-min="12" data-max="100" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">Size around 25 pixels is optimal.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="countdown_color" class="csmm-strong">Countdown Numbers Color</label>
                    <input type="text" name="countdown_color" id="countdown_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['countdown_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    <p class="csmm-form-help-block">Works best with your primary site color.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="countdown_labels_size" class="csmm-strong">Countdown Labels Size</label>
                    <input type="hidden" name="countdown_labels_size" value="<?php esc_attr_e($signals_csmm_options['countdown_labels_size']); ?>" data-min="6" data-max="100" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">Keep it small.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="countdown_labels_color" class="csmm-strong">Countdown Labels Color</label>
                    <input type="text" name="countdown_labels_color" id="countdown_labels_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['countdown_labels_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    <p class="csmm-form-help-block">A secondary, neutral color will work best.</p>
                </div>
            </div>


            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="countdown_days" class="csmm-strong">Countdown Days Label</label>
                    <input type="text" name="countdown_days" id="countdown_days" value="<?php esc_attr_e($signals_csmm_options['countdown_days']); ?>" placeholder="days" class="csmm-form-control">
                    <p class="csmm-form-help-block">The shorter, the better. Default: days.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="countdown_hours" class="csmm-strong">Countdown Hours Label</label>
                    <input type="text" name="countdown_hours" id="countdown_hours" value="<?php esc_attr_e($signals_csmm_options['countdown_hours']); ?>" placeholder="hours" class="csmm-form-control">
                    <p class="csmm-form-help-block">The shorter, the better. Default: hours.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="countdown_minutes" class="csmm-strong">Countdown Minutes Label</label>
                    <input type="text" name="countdown_minutes" id="countdown_minutes" value="<?php esc_attr_e($signals_csmm_options['countdown_minutes']); ?>" placeholder="min" class="csmm-form-control">
                    <p class="csmm-form-help-block">The shorter, the better. Default: min.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="countdown_seconds" class="csmm-strong">Countdown Seconds Label</label>
                    <input type="text" name="countdown_seconds" id="countdown_seconds" value="<?php esc_attr_e($signals_csmm_options['countdown_seconds']); ?>" placeholder="sec" class="csmm-form-control">
                    <p class="csmm-form-help-block">The shorter, the better. Default: sec.</p>
                </div>
            </div>


        </div>
    </div>
</div><!-- #countdown -->