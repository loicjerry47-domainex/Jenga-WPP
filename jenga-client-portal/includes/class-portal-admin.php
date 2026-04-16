<?php

class Jenga_Portal_Admin {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function add_admin_menu() {
        add_menu_page(
            __( 'Client Portal', 'jenga-portal' ),
            __( 'Client Portal', 'jenga-portal' ),
            'portal_manage_clients',
            'jenga-portal',
            array( __CLASS__, 'render_dashboard_page' ),
            'dashicons-groups',
            50
        );

        add_submenu_page(
            'jenga-portal',
            __( 'Clients', 'jenga-portal' ),
            __( 'Clients', 'jenga-portal' ),
            'portal_manage_clients',
            'jenga-portal-clients',
            array( __CLASS__, 'render_clients_page' )
        );

        add_submenu_page(
            'jenga-portal',
            __( 'Settings', 'jenga-portal' ),
            __( 'Settings', 'jenga-portal' ),
            'manage_options',
            'jenga-portal-settings',
            array( __CLASS__, 'render_settings_page' )
        );
    }

    public static function register_settings() {
        register_setting( 'jenga_portal_settings_group', 'jenga_portal_settings' );
    }

    public static function render_dashboard_page() {
        ?>
        <div class="wrap jenga-admin-wrap">
            <h1><?php _e( 'Client Portal Overview', 'jenga-portal' ); ?></h1>
            <p><?php _e( 'Welcome to the Jenga Client Portal administration area.', 'jenga-portal' ); ?></p>
        </div>
        <?php
    }

    public static function render_clients_page() {
        ?>
        <div class="wrap jenga-admin-wrap">
            <h1><?php _e( 'Portal Clients', 'jenga-portal' ); ?></h1>
            <?php
            $clients = get_users( array( 'role' => 'portal_client' ) );
            if ( ! empty( $clients ) ) {
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr><th>' . __( 'Name', 'jenga-portal' ) . '</th><th>' . __( 'Email', 'jenga-portal' ) . '</th></tr></thead><tbody>';
                foreach ( $clients as $client ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( $client->display_name ) . '</td>';
                    echo '<td>' . esc_html( $client->user_email ) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<p>' . __( 'No clients found.', 'jenga-portal' ) . '</p>';
            }
            ?>
        </div>
        <?php
    }

    public static function render_settings_page() {
        ?>
        <div class="wrap jenga-admin-wrap">
            <h1><?php _e( 'Portal Settings', 'jenga-portal' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'jenga_portal_settings_group' );
                $settings = get_option( 'jenga_portal_settings' );
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Login Page ID', 'jenga-portal' ); ?></th>
                        <td><input type="number" name="jenga_portal_settings[login_page]" value="<?php echo isset( $settings['login_page'] ) ? esc_attr( $settings['login_page'] ) : ''; ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Dashboard Page ID', 'jenga-portal' ); ?></th>
                        <td><input type="number" name="jenga_portal_settings[dashboard_page]" value="<?php echo isset( $settings['dashboard_page'] ) ? esc_attr( $settings['dashboard_page'] ) : ''; ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Notify Admin on New Ticket', 'jenga-portal' ); ?></th>
                        <td><input type="checkbox" name="jenga_portal_settings[notify_admin_ticket]" value="1" <?php checked( 1, isset( $settings['notify_admin_ticket'] ) ? $settings['notify_admin_ticket'] : 0 ); ?> /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
