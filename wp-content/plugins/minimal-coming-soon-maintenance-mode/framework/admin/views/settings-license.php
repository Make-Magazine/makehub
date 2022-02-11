<?php
$meta = csmm_get_meta();
global $csmm_lc;
?>
<div class="csmm-tile" id="license">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">License</div>
        <p>Please enter your license key to activate the plugin. In case of any problems <a href="#support" class="csmm-change-tab">, contact support</a>.</p>

        <div class="csmm-section-content">

            <div class="csmm-double-group csmm-clearfix" style="border-bottom: none; padding-bottom: 0;">
                <div class="csmm-form-group">
                    <label for="signals_license_key" class="csmm-strong">License Key</label>
                    <input type="text" name="signals_license_key" id="signals_license_key" value="<?php
                    $license_key = $csmm_lc->get_license('license_key');
                    if ($license_key != 'keyless') {
                        echo $csmm_lc->get_license('license_key');
                    }
                    ?>" placeholder="12345678-12345678-12345678-12345678" class="csmm-form-control skip-save">

                    <p class="csmm-form-help-block">Your license key is located in the confirmation email you received after purchasing.
                        <?php
                        if (!$csmm_lc->is_active()) {
                            echo '<br>If you don\'t have a license - <a target="_blank" href="https://comingsoonwp.com/#pricing">purchase one now</a>; in case of problems <a href="#support" class="csmm-change-tab">contact support</a>.';
                        }
                        ?>
                    </p>
                </div>
            </div>

            <div class="csmm-group csmm-clearfix">
                <div class="csmm-form-group" style="border-bottom: none; padding-bottom: 0;">
                    <?php
                    
                    if (!empty($csmm_lc->get_license('license_key'))) {
                        echo '<label for="signals_license_key" class="csmm-strong">License Status</label>';
                        if ($csmm_lc->is_active()) {
                            $license_formatted = $csmm_lc->get_license_formatted();
                            echo '<b style="color: #66b317;">Active</b><br>
            Type: ' . $license_formatted['name_long'];

                            echo '<br>Valid ' . $license_formatted['valid_until'] . '</td>';
                        } else { // not active
                            echo '<strong style="color: #ea1919;">Inactive</strong>';
                            if (!empty($csmm_lc->get_license('error'))) {
                                echo '<br>Error: ' . $csmm_lc->get_license('error');
                            }
                        }
                    }
                    ?>

                    <br><br><br>
                    <input type="hidden" value="0" id="csmm_license_changed" name="csmm_license_changed">
                    <?php
                    if ($csmm_lc->is_active()) {
                        echo '<a href="#" class="csmm-btn js-action" id="csmm_save_license">Save &amp; Re-Validate License Key</a> ';
                        echo '&nbsp; <a href="#" class="csmm-btn csmm-btn-red js-action" id="csmm_deactivate_license">Deactivate License</a>';
                    } else {
                        echo '<a href="#" class="csmm-btn js-action" id="csmm_save_license">Save &amp; Validate License Key</a> ';
                        echo '&nbsp; <a href="#" class="csmm-btn csmm-btn-secondary js-action" id="csmm_keyless_activation">Keyless Activation</a>';
                    }
                    ?>

                    <?php if ($csmm_lc->is_active('white_label')) { ?>

                        <br><br>
                        <hr>
                        <p><a href="<?php echo admin_url('options-general.php?page=maintenance_mode_options&csmm_wl=true'); ?>" class="csmm-btn js-action csmm-btn-secondary">Enable White-Label License Mode</a></p>
                        <p>Enabling the white-label license mode hides License and Support tabs, and removes visible mentions of WebFactory Ltd.<br>
                            To disable it append <strong>&amp;csmm_wl=false</strong> to the Coming Soon settings page URL.
                            Or save this URL and open it when you want to disable the white-label license mode:<br> <?php echo '<a href="' . admin_url('options-general.php?page=maintenance_mode_options&csmm_wl=false') . '">' . admin_url('options-general.php?page=maintenance_mode_options<strong>&amp;csmm_wl=false</strong>') . '</a>'; ?></p>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div><!-- #basic -->