<?php
use EventEspresso\core\domain\Domain;
?>
<p><b><?php esc_html_e('Event', 'event_espresso');?></b> <?php echo esc_html($event instanceof EE_Event ? $event->name() : esc_html__('None', 'event_espresso'));?></p>
<p><b><?php esc_html_e('Ticket', 'event_espresso');?></b> <?php echo esc_html($ticket instanceof EE_Ticket ? $ticket->name() : esc_html__('None', 'event_espresso'));?></p>

<table class="ee-responsive-table">
    <thead>
    <tr>
    <th><?php printf(
        esc_html__('%s Data', 'event_espresso'),
        Domain::brandName()
    );?></th>
    <th><?php esc_html_e('CSV Data', 'event_espresso');?></th>
    <th><?php esc_html_e('Sample #1', 'event_espresso');?></th>
    <th><?php esc_html_e('Sample #2', 'event_espresso');?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($table_rows as $table_row) {
    ?>
    <tr>
        <?php
        if (is_string($table_row)) {
            ?>
            <td colspan="4">
                <h2><?php echo esc_html($table_row);?></h2>
            </td>
            <?php
        } else {
            foreach ($table_row as $cell) {
                ?>
                <td>
                <?php
                echo esc_html($cell);
                ?>
                </td><?php
            }
        }
        ?>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
