<?php
add_action( 'admin_menu', 'ldms_welcome_screen_pages' );
function ldms_welcome_screen_pages() {
  add_dashboard_page(
    __( 'Welcome To LearnDash Private Sessions', 'ldmessenger' ),
    __( 'Welcome To LearnDash Private Sessions', 'ldmessenger' ),
    'read',
    'ldms-welcome-screen',
    'ldms_welcome_screen_content'
  );
}

function ldms_welcome_screen_content() { ?>
  <div class="wrap">

    <div style="max-width: 800px; margin-bottom: 50px;">

        <h1><?php esc_html_e( 'LearnDash Private Sessions', 'ldmessenger' ); ?></h1>

        <p style="font-size: 18px; font-weight: 200; line-height: 1.65em;">With LearnDash Private Sessions your group leaders can start private, 1 on 1 coaching sessions with any of their group members. Provide more value by sharing files, contact information and personalized interactions.</p>

    </div>

    <div style="background: #fff; border: 1px solid #efefef; padding: 20px 0;">

    <table class="doc-table">
        <tr>
            <td valign="top" class="ldms-info">
                <h3>How LearnDash Private Sessions Works</h3>
                <p>LearnDash Private Sessions allows <a href="<?php echo admin_url(); ?>edit.php?post_type=groups">group leaders</a> to create individual coaching sessions with any of their group members.</p>
                <p>Sessions are accessed through the private sessions tab located at the bottom of the page (when logged in as a group leader.)</p>
                <p>Coaching sessions are completely private and can't be accessed without being logged in and part of the session.</p>

            </td>
            <td valign="top">

                <h3>Getting Started</h3>

                <ul>
                    <li><a href="<?php echo get_permalink(ldms_get_option('ldma_sessions_page')); ?>">View your private sessions page</a></li>
                    <li><a href="<?php echo admin_url(); ?>edit.php?post_type=groups">Manage LearnDash groups</a></li>
                    <li><a href="<?php echo admin_url(); ?>admin.php?page=learndash-private-sessions">Private sessions settings &amp; apperance</a></li>
                    <li><a href="https://docs.snaporbital.com" target="_new">Documentation &amp; support</a></li>
                </ul>

            </td>
            <td valign="top" class="shortcodes">
                <h3>Shortcodes</h3>

                <p>We've bundled three shortcodes:</p>

                <p><code>[sessions_widget]</code> <br>Outputs the link to new sessions and existing sessions</p>
                <p><code>[sessions_text_link]</code> <br>Outputs a text link to messages with new message count, use attribute link="off" to only output the text (for use in menus)</p>
                <p><code>[create_session]</code> <br>Outputs the create a new session shortcode (only visible to logged in group leaders)</p>
                <p><code>[private_sessions]</code> <br>Outputs the table of private sessions you're connected with.</p>
            </td>
        </tr>
    </table>
    </div>

  </div>
  <style type="text/css">
    .doc-table td {
        padding: 20px;
    }
    .doc-table h3 {
        margin-top: 0;
    }
    .doc-table td.shortcodes  {
        border-left: 1px solid #efefef;
    }
    .doc-table td.ldms-info {
        width: 33%;
    }
    .doc-table td.ldms-info p {
        font-size: 14px;
        line-height: 1.65em;
    }
    .doc-table ul {
        list-style: disc;
        padding-left: 15px;
        font-size: 14px;
    }
    </style>
  <?php
}
add_action( 'admin_menu', 'ldms_welcome_screen_remove_menus' );

function ldms_welcome_screen_remove_menus() {
    remove_submenu_page( 'index.php', 'ldms-welcome-screen' );
}
