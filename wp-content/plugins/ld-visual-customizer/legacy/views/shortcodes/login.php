<div id="lds-shortcode">
    <div class="lds-dialog">

        <hgroup class="lds-dialog-title">
            <h2><?php esc_html_e( 'Login', 'lds_skins' ); ?></h2>
        </hgroup>

        <?php
        $login_args = array();

        /*
         * Parse args
         */
        if( isset( $args['redirect'] ) ) $login_args['redirect']            = esc_html($args['redirect']);
        if( isset( $args['username'] ) ) $login_args['label_username']      = esc_html($args['username']);
        if( isset( $args['password'] ) ) $login_args['label_password']      = esc_html($args['password']);
        if( isset( $args['button'] ) ) $login_args['label_log_in']          = esc_html($args['button']);
        if( isset( $args['remember_me'] ) ) $login_args['label_remember']   = esc_html($args['remember_me']);

        $lost_password = ( isset( $args['lost_password'] ) ? $args['lost_password'] : __( 'Lost Password', 'lds_skins' ) );

        wp_login_form( $login_args ); ?>

        <p class="lds-dialog-lost-password"><a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>"><?php echo esc_html($lost_password); ?></a></p>

    </div>
</div>
