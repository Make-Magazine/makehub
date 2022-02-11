<div class="csmm-tile" id="design-map">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Map Module</div>
        <p>For local businesses or events, a map is a must-have.</p>

        <div class="csmm-section-content">
            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="map_address" class="csmm-strong">Address</label>
                    <input type="text" name="map_address" id="map_address" value="<?php esc_attr_e($signals_csmm_options['map_address']); ?>" placeholder="Times Square, New York, USA" class="csmm-form-control">
                    <p class="csmm-form-help-block">Please include the full address; be as specific as possible.</p>
                </div>
                <div class="csmm-form-group">
                    <label for="map_zoom" class="csmm-strong">Zoom Level</label>
                    <select id="map_zoom" name="map_zoom">
                        <?php
                        $zoom_options = array(array('val' => '16:9', 'label' => '0 - entire world'));
                        for ($tmp = 1; $tmp < 21; $tmp++) {
                            $zoom_options[] = array('val' => $tmp, 'label' => $tmp);
                        }
                        $zoom_options[] = array('val' => '21', 'label' => '21 - street level');
                        csmm_create_select_options($zoom_options, $signals_csmm_options['map_zoom']);  ?>
                    </select>
                    <p class="csmm-form-help-block"><?php _e('Default: 15', 'signals'); ?></p>
                </div>

            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="map_height" class="csmm-strong">Map Height</label>
                    <input type="hidden" name="map_height" value="<?php esc_attr_e($signals_csmm_options['map_height']); ?>" data-min="50" data-max="500" data-step="10" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">Width is always 100% and responsive. Default height: 250px.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="map_api_key" class="csmm-strong">Google Maps API Key</label>
                    <input type="text" name="map_api_key" id="map_api_key" value="<?php esc_attr_e($signals_csmm_options['map_api_key']); ?>" placeholder="123456789asdf123456789" class="csmm-form-control">
                    <p class="csmm-form-help-block">Google dictates that every site needs a unique API key in order for maps to function properly. Please follow <a href="https://www.gmapswidget.com/documentation/generate-google-maps-api-key/" target="_blank">this guide</a> to generate your key. It takes less than a minute.</p>
                </div>

            </div>

        </div>
    </div>
</div><!-- #map-->