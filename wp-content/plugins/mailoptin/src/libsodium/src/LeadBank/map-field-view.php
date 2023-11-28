<?php

use MailOptin\Core\Admin\SettingsPage\ConversionImport;

?>
<div class="mo_csv_import-content">
    <div class="multi-step">
        <ul class="multi-step-list">
            <a class="multi-step-item active <?php if ( ! isset($_GET['step'])) echo 'current' ?>" href="<?php if (isset($_GET['step']) && $_GET['step'] != '') echo MAILOPTIN_LEAD_IMPORT_CSV_SETTINGS_PAGE; else echo 'javascript:void(0)' ?>">
                <div class="item-wrap">
                    <p class="item-title"><?php _e('Step One', 'mailoptin') ?></p>
                    <p class="item-subtitle"><?php _e('Upload a csv file', 'mailoptin') ?></p>
                </div>
            </a>
            <a class="multi-step-item active <?php if (isset($_GET['step']) && $_GET['step'] === '2') echo 'current'; ?>" href="<?php if (isset($_GET['step']) && $_GET['step'] === '3') echo add_query_arg('step', '2', MAILOPTIN_LEAD_IMPORT_CSV_SETTINGS_PAGE); else echo '#' ?>">
                <div class="item-wrap">
                    <p class="item-title"><?php _e('Step Two', 'mailoptin') ?></p>
                    <p class="item-subtitle"><?php _e('Map Fields', 'mailoptin') ?></p>
                </div>
            </a>
            <a class="multi-step-item active <?php if (isset($_GET['step']) && $_GET['step'] === '3') echo 'current'; ?>">
                <div class="item-wrap">
                    <p class="item-title"><?php _e('Step Three', 'mailoptin') ?></p>
                    <p class="item-subtitle"><?php _e('Import Completed', 'mailoptin') ?></p>
                </div>
            </a>
        </ul>
    </div>

    <?php
    if (isset($_GET['step']) && $_GET['step'] === '2') {
        //get the csv file path, read and render
        $read_csv_headers = ConversionImport::get_instance()->read_csv_headers();
        $headers          = $this->get_columns();
        ?>
        <div class="mo-map-leads">
            <p><strong><?php _e('Map Fields', 'mailoptin'); ?></strong></p>
            <table>
                <thead>
                <tr>
                    <td><?php _e('MailOptin Fields', 'mailoptin') ?></td>
                    <td><?php _e('CSV Meta Fields', 'mailoptin') ?></td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($headers as $key => $value) {
                    ?>
                    <tr>
                        <td><?php echo $value; ?></td>
                        <td>
                            <select name="mo_leads[<?php echo $key ?>]">
                                <option value=""><?php _e('Select Field', 'mailoptin') ?></option>
                                <?php
                                foreach ($read_csv_headers as $header) {
                                    ?>
                                    <option value="<?php echo esc_attr($header) ?>" <?php if (isset($_POST['mo_leads'][$key]) && $key === $_POST['mo_leads'][$key]) echo 'selected' ?>><?php echo esc_attr($header) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php wp_nonce_field('mo_map_leads', 'mo_map_leads'); ?>
            <?php echo '<div>' . get_submit_button(__('Start Importing', 'mailoptin'), 'primary', 'map_leads', false) . '</div>'; ?>
        </div>
        <?php
    } else if (isset($_GET['step']) && $_GET['step'] === '3') {
        ?>
        <div>
            <p><strong><?php _e('Import was Successful.', 'mailoptin'); ?></strong></p>
            <a href="<?php echo MAILOPTIN_LEAD_BANK_SETTINGS_PAGE ?>" class="button button-secondary">
                <?php _e('Back to Leads', 'mailoptin'); ?>
            </a>
        </div>
        <?php
    } else {
    ?>
    <div class="mo_csv_import-content">
        <div>
            <p><strong><?php _e('Upload a CSV File', 'mailoptin'); ?></strong></p>
            <input type="file" id="mo_csv_file" name="mo_csv_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required/>
            <?php wp_nonce_field('mo_upload_leads', 'mo_upload_leads'); ?>
            <?php echo '<div>' . get_submit_button(__('Upload Leads', 'mailoptin'), 'primary', 'upload_leads', false) . '</div>'; ?>
        </div>
        <?php
        }
        ?>
    </div>