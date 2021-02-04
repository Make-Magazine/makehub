<p>
    <strong><?php esc_html_e('Upload the CSV File', 'event_espresso');?></strong>
</p>
<p>
    <?php esc_html_e('Choose the CSV file from your computer. It will be temporarily uploaded to the server and its rows will be imported.', 'event_espresso'); ?>
</p>
<p><strong><?php esc_html_e('About the CSV File Format', 'event_espresso'); ?></strong></p>
<p>
<?php esc_html_e("The first row should be column headers, and each subsequent row represents a registration.", 'event_espresso'); ?>
</p>
<p>
    <?php esc_html_e("You can give any name you want to the column headers. But there must be a column for:", 'event_espresso'); ?>
</p>
<ul>
    <li><?php esc_html_e('First Name', 'event_espresso');?></li>
    <li><?php esc_html_e('Email', 'event_espresso');?></li>
</ul>
<p><?php esc_html_e('You can also add columns for any of the following:', 'event_espresso');?></p>
<ul>
    <li><?php esc_html_e('Any other system questions, like Last Name, Address, City, State, Country, Zip, or Phone.', 'event_espresso'); ?></li>
    <li><?php esc_html_e('Please note: for state you can use the state’s name (e.g., "Alabama") or abbreviation (e.g., "AL"); for country you can use its name (e.g., "United States"), ISO2 code (e.g., "US"), or ISO3 code (e.g., "USA").', 'event_espresso'); ?></li>
    <li><?php esc_html_e('Any custom questions. The column header’s text does not need to match the custom question. For checkbox or multi-select questions, separate the different answer values with a pipe "|". Eg "Breakfast|Lunch|Dinner"', 'event_espresso'); ?></li>
    <li><?php esc_html_e('The payment amount, if the registrations are for a paid ticket.', 'event_espresso'); ?></li>
</ul>
<p>
    <a href="<?php echo EE_IMPORTER_URL;?>../samples/attendee-with-custom-questions.csv">
    <?php esc_html_e('Click here to see a sample CSV file.', 'event_espresso');?>
    </a>
</p>
<p>
    <?php esc_html_e('Please note you can add a new column for any custom question you have on the event.', 'event_espresso'); ?>
</p>