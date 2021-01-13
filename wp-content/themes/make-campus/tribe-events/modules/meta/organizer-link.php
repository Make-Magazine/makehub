<?php
/**
 * Single Event Meta (Organizer) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta/organizer.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.19
 */
$organizer_ids = tribe_get_organizer_ids();
$multiple = count($organizer_ids) > 1;

?>

<div class="tribe-events-meta-group tribe-events-meta-group-organizer-link">
    <?php // <h2 class="tribe-events-single-section-title"><?php echo tribe_get_organizer_label( ! $multiple ); end </h2>  ?>
    <dl>
        <?php
        do_action('tribe_events_single_meta_organizer_section_start');

        foreach ($organizer_ids as $organizer) {
            if (!$organizer) {
                continue;
            }
            ?>
            <dt class="tribe-events-event-organizer-label">Organizer:</dt>
            <dd class="tribe-organizer">
                <?php echo tribe_get_organizer_link( $organizer ) ?>
            </dd>
			<a class="btn universal-btn" href="<?php echo get_permalink($organizer); ?>">See More Events</a>
            <?php
        }

        do_action('tribe_events_single_meta_organizer_section_end');
        ?>
    </dl>
</div>
