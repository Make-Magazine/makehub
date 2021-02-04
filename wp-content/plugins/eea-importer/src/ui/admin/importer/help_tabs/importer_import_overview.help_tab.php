<?php
use EventEspresso\core\domain\Domain;
?>
<p>
    <strong><?php printf(
        esc_html__('Before Using the %s Attendee Importer', 'event_espresso'),
        Domain::brandName()
); ?></strong>
</p>
<p>
    <strong><?php esc_html_e('Backup', 'event_espresso');?></strong>
    <?php esc_html_e("Always make sure you have a complete database backup before performing an import.", 'event_espresso'); ?>
</p>
<p>
    <strong><?php printf(
        esc_html__('Setup The Event in %s', 'event_espresso'),
        Domain::brandName()
    );?></strong>
</p>
<p>
    <?php esc_html_e("You must first create the event and ticket you wish to import the attendees to. Currently, only importing to one event ticket at a time is supported.", 'event_espresso'); ?>
</p>
<p>
    <strong><?php esc_html_e('Prepare CSV File', 'event_espresso');?></strong>
</p>
<p>
    <?php esc_html_e('Please see the help tab about uploading the file for info on how to prepare the CSV file.', 'event_espresso'); ?>
</p>
<p>
    <strong><?php esc_html_e('Run the Importer', 'event_espresso'); ?></strong>
</p>
<p>
    <?php esc_html_e('Complete the import configuration steps. You will have a chance to verify your configuration of the import before the data is actually imported. Before the verification step, use your browser’s back button if you like.', 'event_espresso'); ?>
</p>
<p>
    <?php esc_html_e('If you import data improperly, or there is an error, the best way to undo the import is to restore to a backup. So please verify you have a database backup before running the import.', 'event_espresso'); ?>
</p>