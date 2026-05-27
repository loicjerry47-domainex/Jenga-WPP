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
            array( __CLASS__, 'render_overview_page' ),
            'dashicons-groups',
            50
        );

        add_submenu_page(
            'jenga-portal',
            __( 'Overview', 'jenga-portal' ),
            __( 'Overview', 'jenga-portal' ),
            'portal_manage_clients',
            'jenga-portal',
            array( __CLASS__, 'render_overview_page' )
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
        register_setting( 'jenga_portal_settings_group', 'jenga_portal_settings', array(
            'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
        ) );
    }

    public static function sanitize_settings( $input ) {
        $clean = array();

        $page_fields = array( 'login_page', 'dashboard_page', 'projects_page', 'tickets_page', 'documents_page' );
        foreach ( $page_fields as $field ) {
            $clean[ $field ] = isset( $input[ $field ] ) ? absint( $input[ $field ] ) : 0;
        }

        $clean['brand_name'] = isset( $input['brand_name'] ) ? sanitize_text_field( $input['brand_name'] ) : '';

        $toggle_fields = array(
            'notify_admin_ticket',
            'notify_admin_ticket_reply',
            'notify_client_status',
            'notify_client_reply',
            'notify_client_document',
            'notify_client_project',
        );
        foreach ( $toggle_fields as $field ) {
            $clean[ $field ] = ! empty( $input[ $field ] ) ? 1 : 0;
        }

        return $clean;
    }

    // ── Overview Page ─────────────────────────────────────────────────────────

    public static function render_overview_page() {
        $client_count = count( get_users( array( 'role' => 'portal_client', 'fields' => 'ids' ) ) );

        $open_tickets = get_posts( array(
            'post_type'      => 'jenga_ticket',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array( array(
                'key'     => '_jenga_ticket_status',
                'value'   => array( 'Resolved', 'Closed' ),
                'compare' => 'NOT IN',
            ) ),
        ) );

        $active_projects = get_posts( array(
            'post_type'      => 'jenga_project',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array( array(
                'key'     => '_jenga_status',
                'value'   => 'Completed',
                'compare' => '!=',
            ) ),
        ) );

        $total_docs = wp_count_posts( 'jenga_document' );
        $doc_count  = isset( $total_docs->publish ) ? (int) $total_docs->publish : 0;

        $recent_tickets = get_posts( array(
            'post_type'      => 'jenga_ticket',
            'posts_per_page' => 10,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array( array(
                'key'     => '_jenga_ticket_status',
                'value'   => array( 'Resolved', 'Closed' ),
                'compare' => 'NOT IN',
            ) ),
        ) );
        ?>
        <div class="wrap jenga-admin-wrap">
            <h1><?php _e( 'Client Portal Overview', 'jenga-portal' ); ?></h1>

            <div class="jenga-stats-row">
                <div class="jenga-stat-card">
                    <div class="jenga-stat-number"><?php echo intval( $client_count ); ?></div>
                    <div class="jenga-stat-label"><?php _e( 'Portal Clients', 'jenga-portal' ); ?></div>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=jenga-portal-clients' ) ); ?>" class="jenga-stat-link"><?php _e( 'View all &rarr;', 'jenga-portal' ); ?></a>
                </div>
                <div class="jenga-stat-card">
                    <div class="jenga-stat-number"><?php echo count( $open_tickets ); ?></div>
                    <div class="jenga-stat-label"><?php _e( 'Open Tickets', 'jenga-portal' ); ?></div>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=jenga_ticket' ) ); ?>" class="jenga-stat-link"><?php _e( 'View all &rarr;', 'jenga-portal' ); ?></a>
                </div>
                <div class="jenga-stat-card">
                    <div class="jenga-stat-number"><?php echo count( $active_projects ); ?></div>
                    <div class="jenga-stat-label"><?php _e( 'Active Projects', 'jenga-portal' ); ?></div>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=jenga_project' ) ); ?>" class="jenga-stat-link"><?php _e( 'View all &rarr;', 'jenga-portal' ); ?></a>
                </div>
                <div class="jenga-stat-card">
                    <div class="jenga-stat-number"><?php echo intval( $doc_count ); ?></div>
                    <div class="jenga-stat-label"><?php _e( 'Documents', 'jenga-portal' ); ?></div>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=jenga_document' ) ); ?>" class="jenga-stat-link"><?php _e( 'View all &rarr;', 'jenga-portal' ); ?></a>
                </div>
            </div>

            <h2 style="margin-top:32px;"><?php _e( 'Open Tickets', 'jenga-portal' ); ?></h2>
            <?php if ( $recent_tickets ) : ?>
            <table class="wp-list-table widefat fixed striped jenga-overview-table">
                <thead>
                    <tr>
                        <th><?php _e( 'Ticket', 'jenga-portal' ); ?></th>
                        <th width="120"><?php _e( 'Client', 'jenga-portal' ); ?></th>
                        <th width="90"><?php _e( 'Priority', 'jenga-portal' ); ?></th>
                        <th width="110"><?php _e( 'Status', 'jenga-portal' ); ?></th>
                        <th width="130"><?php _e( 'Submitted', 'jenga-portal' ); ?></th>
                        <th width="70"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $recent_tickets as $t ) :
                        $priority = get_post_meta( $t->ID, '_jenga_priority', true );
                        $status   = get_post_meta( $t->ID, '_jenga_ticket_status', true );
                        $client   = get_user_by( 'id', get_post_meta( $t->ID, '_jenga_client_id', true ) );
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $t->post_title ); ?></strong></td>
                        <td><?php echo $client ? esc_html( $client->display_name ) : '&mdash;'; ?></td>
                        <td><span class="jenga-badge jenga-badge-<?php echo esc_attr( strtolower( $priority ) ); ?>"><?php echo esc_html( $priority ); ?></span></td>
                        <td><?php echo esc_html( $status ); ?></td>
                        <td><?php echo get_the_date( 'M j, Y', $t->ID ); ?></td>
                        <td><a href="<?php echo esc_url( get_edit_post_link( $t->ID ) ); ?>" class="button button-small"><?php _e( 'Edit', 'jenga-portal' ); ?></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
                <p><?php _e( 'No open tickets. All clear!', 'jenga-portal' ); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    // ── Clients Page ──────────────────────────────────────────────────────────

    public static function render_clients_page() {
        $clients = get_users( array( 'role' => 'portal_client' ) );
        ?>
        <div class="wrap jenga-admin-wrap">
            <h1>
                <?php _e( 'Portal Clients', 'jenga-portal' ); ?>
                <a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>" class="page-title-action"><?php _e( 'Add New User', 'jenga-portal' ); ?></a>
            </h1>
            <?php if ( ! empty( $clients ) ) : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e( 'Name', 'jenga-portal' ); ?></th>
                        <th><?php _e( 'Email', 'jenga-portal' ); ?></th>
                        <th width="80"><?php _e( 'Projects', 'jenga-portal' ); ?></th>
                        <th width="80"><?php _e( 'Tickets', 'jenga-portal' ); ?></th>
                        <th width="100"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $clients as $client ) :
                        $p_count = count( get_posts( array(
                            'post_type'      => 'jenga_project',
                            'posts_per_page' => -1,
                            'fields'         => 'ids',
                            'meta_query'     => array( array( 'key' => '_jenga_client_id', 'value' => $client->ID ) ),
                        ) ) );
                        $t_count = count( get_posts( array(
                            'post_type'      => 'jenga_ticket',
                            'posts_per_page' => -1,
                            'fields'         => 'ids',
                            'meta_query'     => array( array( 'key' => '_jenga_client_id', 'value' => $client->ID ) ),
                        ) ) );
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $client->display_name ); ?></strong></td>
                        <td><?php echo esc_html( $client->user_email ); ?></td>
                        <td><?php echo intval( $p_count ); ?></td>
                        <td><?php echo intval( $t_count ); ?></td>
                        <td><a href="<?php echo esc_url( get_edit_user_link( $client->ID ) ); ?>" class="button button-small"><?php _e( 'Edit', 'jenga-portal' ); ?></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
                <p><?php _e( 'No portal clients found. Create a WordPress user and assign the "Portal Client" role.', 'jenga-portal' ); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    // ── Settings Page ─────────────────────────────────────────────────────────

    public static function render_settings_page() {
        $settings = get_option( 'jenga_portal_settings', array() );
        ?>
        <div class="wrap jenga-admin-wrap">
            <h1><?php _e( 'Portal Settings', 'jenga-portal' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'jenga_portal_settings_group' ); ?>

                <h2 class="title"><?php _e( 'Branding', 'jenga-portal' ); ?></h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="jenga_brand_name"><?php _e( 'Brand Name', 'jenga-portal' ); ?></label></th>
                        <td>
                            <input type="text" id="jenga_brand_name" name="jenga_portal_settings[brand_name]" value="<?php echo esc_attr( isset( $settings['brand_name'] ) ? $settings['brand_name'] : '' ); ?>" class="regular-text">
                            <p class="description"><?php _e( 'Appears in email notifications. Defaults to site name if empty.', 'jenga-portal' ); ?></p>
                        </td>
                    </tr>
                </table>

                <h2 class="title"><?php _e( 'Portal Pages', 'jenga-portal' ); ?></h2>
                <p><?php _e( 'Select the WordPress pages where each portal shortcode has been placed.', 'jenga-portal' ); ?></p>
                <table class="form-table" role="presentation">
                    <?php
                    $page_settings = array(
                        'login_page'     => array( __( 'Login Page', 'jenga-portal' ), '[jenga_portal_login]' ),
                        'dashboard_page' => array( __( 'Dashboard Page', 'jenga-portal' ), '[jenga_portal_dashboard]' ),
                        'projects_page'  => array( __( 'Projects Page', 'jenga-portal' ), '[jenga_portal_projects]' ),
                        'tickets_page'   => array( __( 'Tickets Page', 'jenga-portal' ), '[jenga_portal_tickets]' ),
                        'documents_page' => array( __( 'Documents Page', 'jenga-portal' ), '[jenga_portal_documents]' ),
                    );
                    foreach ( $page_settings as $key => $info ) :
                        $selected_id = isset( $settings[ $key ] ) ? (int) $settings[ $key ] : 0;
                    ?>
                    <tr>
                        <th scope="row"><?php echo esc_html( $info[0] ); ?></th>
                        <td>
                            <?php wp_dropdown_pages( array(
                                'name'             => 'jenga_portal_settings[' . $key . ']',
                                'id'               => 'jenga_' . $key,
                                'selected'         => $selected_id,
                                'show_option_none' => __( '— Select a page —', 'jenga-portal' ),
                                'option_none_value'=> '0',
                            ) ); ?>
                            <p class="description"><?php printf( __( 'Shortcode: %s', 'jenga-portal' ), '<code>' . esc_html( $info[1] ) . '</code>' ); ?></p>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <h2 class="title"><?php _e( 'Email Notifications', 'jenga-portal' ); ?></h2>
                <table class="form-table" role="presentation">
                    <?php
                    $toggles = array(
                        'notify_admin_ticket'       => __( 'Admin: New ticket submitted', 'jenga-portal' ),
                        'notify_admin_ticket_reply' => __( 'Admin: Client replied to ticket', 'jenga-portal' ),
                        'notify_client_status'      => __( 'Client: Ticket status changed', 'jenga-portal' ),
                        'notify_client_reply'       => __( 'Client: Admin replied to ticket', 'jenga-portal' ),
                        'notify_client_document'    => __( 'Client: New document uploaded', 'jenga-portal' ),
                        'notify_client_project'     => __( 'Client: Project status changed', 'jenga-portal' ),
                    );
                    foreach ( $toggles as $toggle_key => $label ) :
                        $checked = isset( $settings[ $toggle_key ] ) ? (bool) $settings[ $toggle_key ] : true;
                    ?>
                    <tr>
                        <th scope="row"><?php echo esc_html( $label ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="jenga_portal_settings[<?php echo esc_attr( $toggle_key ); ?>]" value="1" <?php checked( $checked, true ); ?>>
                                <?php _e( 'Enabled', 'jenga-portal' ); ?>
                            </label>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
