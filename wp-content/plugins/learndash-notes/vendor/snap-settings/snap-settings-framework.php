<?php
function snap_template( $field_groups = array(), $plugin_slug = null )  { ?>

    <?php do_action( 'snap_settings_before_wrap', $field_groups, $plugin_slug ); ?>

    <div class="wrap snap-wrap">

        <?php do_action( 'snap_settings_inside_wrap', $field_groups, $plugin_slug ); ?>

        <div class="snap-branding">
            <a href="https://www.snaporbital.com/" target="_new">
                <img src="<?php echo esc_url( LDS_URL . '/assets/img/snaporbital-color.png'); ?>" alt="Snap Orbital">
            </a>
        </div>

        <?php
        do_settings_sections( $plugin_slug );
        settings_fields($plugin_slug); ?>

        <div class="snap-header snap-primary-header">
            <?php do_action( 'snap_settings_primary_header', $plugin_slug ); ?>
        </div>

        <?php do_action( 'snap_settings_body', $plugin_slug ); ?>

        <div class="submit snap-submit"><?php submit_button(); ?></div>

    </div>

    <?php
}
