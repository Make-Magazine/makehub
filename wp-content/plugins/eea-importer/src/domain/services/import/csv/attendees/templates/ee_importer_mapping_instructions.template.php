<?php
use EventEspresso\core\domain\Domain;
?>
<div class="ee-attendee-importer-mapping-instructions">
    <p><?php printf(
        esc_html__('For each CSV column on the left, choose what %s data it will get mapped to during the import.', 'event_espresso'),
        Domain::brandName()
    );?></p>
    <p><?php printf(
        esc_html__('The following %s data must be mapped to a column:', 'event_espresso'),
        Domain::brandName()
    );?></p>
    <ul>
        <li><?php esc_html_e('First Name', 'event_espresso');?></li>
        <li><?php esc_html_e('Email Address', 'event_espresso');?></li>
    </ul>
</div>
