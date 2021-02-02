<?php
/** @var string $iframe_embed_buttons_section */
?>

<div class="wrap">
    <?php do_action('AHEE__calendar_usage_info__template__start'); ?>
    <p>
        <?php _e('The calendar shortcodes allow you to display the calendar on a WordPress page or post. Unless otherwise specified, the calendar will show all events by month and exclude expired events.', 'event_espresso'); ?>
    </p>
    <ul>
        <li><strong><?php _e('Show a calendar with all of your events', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR]</li>
        <li><strong><?php _e('Show events on the calendar and include expired events', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR show_expired=true]</li>
        <li><strong><?php _e('Show events from a specific category on the calendar', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR event_category_id=your_category_id]</li>
        <li><strong><?php _e('Show events from a specific category on the calendar and include expired events', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR event_category_id=your_category_id show_expired=true]</li>
        <li><strong><?php _e('Show events from a specific event venue on the calendar', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR event_venue_id=your_venue_id]</li>
        <li><strong><?php _e('Show events on the calendar by specific month (numeric value) and year (numeric value)', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR month=01 year=2016]</li>
        <li><strong><?php _e('Show events on the calendar by month', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR cal_view=month]</li>
        <li><strong><?php _e('Show events on the calendar by a regular week', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR cal_view=basicWeek]</li>
        <li><strong><?php _e('Show events on the calendar by a regular day', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR cal_view=basicDay]</li>
        <li><strong><?php _e('Show events on the calendar by an agenda week', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR cal_view=agendaWeek]</li>
        <li><strong><?php _e('Show events on the calendar by an agenda day', 'event_espresso'); ?></strong><br />[ESPRESSO_CALENDAR cal_view=agendaDay]</li>
    </ul>
    <?php
    echo $iframe_embed_buttons_section;
    do_action('AHEE__calendar_usage_info__template__end');
    ?>

</div>