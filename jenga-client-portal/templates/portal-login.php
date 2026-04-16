<?php
/**
 * Portal Login Template
 */
?>
<div class="jenga-portal-wrapper">
    <div class="jp-login-container" style="max-width: 400px; margin: 0 auto; text-align: center;">
        <?php
        $settings = get_option( 'jenga_portal_settings' );
        $dashboard_id = isset( $settings['dashboard_page'] ) ? $settings['dashboard_page'] : 0;
        $redirect = $dashboard_id ? get_permalink( $dashboard_id ) : home_url();

        echo '<h2>' . __( 'Client Portal Login', 'jenga-portal' ) . '</h2>';

        wp_login_form( array(
            'redirect'       => esc_url( $redirect ),
            'form_id'        => 'jp-loginform',
            'label_username' => __( 'Email Address or Username', 'jenga-portal' ),
            'label_password' => __( 'Password', 'jenga-portal' ),
            'label_remember' => __( 'Remember Me', 'jenga-portal' ),
            'label_log_in'   => __( 'Log In', 'jenga-portal' ),
            'remember'       => true
        ) );
        ?>
        <p style="margin-top:20px;">
            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" style="color:var(--jp-text-muted);"><?php _e( 'Lost your password?', 'jenga-portal' ); ?></a>
        </p>
    </div>
</div>
