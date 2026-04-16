<?php

class Jenga_Portal_Shortcodes {

    public static function init() {
        add_shortcode( 'jenga_portal_login', array( __CLASS__, 'render_login' ) );
        add_shortcode( 'jenga_portal_dashboard', array( __CLASS__, 'render_dashboard' ) );
        add_shortcode( 'jenga_portal_projects', array( __CLASS__, 'render_projects' ) );
        add_shortcode( 'jenga_portal_tickets', array( __CLASS__, 'render_tickets' ) );
        add_shortcode( 'jenga_portal_documents', array( __CLASS__, 'render_documents' ) );
    }

    private static function check_access() {
        if ( ! is_user_logged_in() ) {
            $settings = get_option( 'jenga_portal_settings' );
            $login_page_id = isset( $settings['login_page'] ) ? $settings['login_page'] : 0;
            if ( $login_page_id ) {
                wp_safe_redirect( get_permalink( $login_page_id ) );
                exit;
            }
            return false;
        }

        if ( ! current_user_can( 'portal_view_dashboard' ) ) {
            return false;
        }
        return true;
    }

    public static function render_login( $atts ) {
        if ( is_user_logged_in() && current_user_can( 'portal_view_dashboard' ) ) {
            $settings = get_option( 'jenga_portal_settings' );
            $dashboard_id = isset( $settings['dashboard_page'] ) ? $settings['dashboard_page'] : 0;
            if ( $dashboard_id ) {
                wp_safe_redirect( get_permalink( $dashboard_id ) );
                exit;
            }
            return __( 'You are already logged in.', 'jenga-portal' );
        }
        
        ob_start();
        include JENGA_PORTAL_DIR . 'templates/portal-login.php';
        return ob_get_clean();
    }

    public static function render_dashboard( $atts ) {
        if ( ! self::check_access() ) {
            return '<div class="jenga-notice error"><p>' . __( 'You do not have permission to view this page.', 'jenga-portal' ) . '</p></div>';
        }

        ob_start();
        include JENGA_PORTAL_DIR . 'templates/portal-dashboard.php';
        return ob_get_clean();
    }

    public static function render_projects( $atts ) {
        if ( ! self::check_access() ) {
            return '<div class="jenga-notice error"><p>' . __( 'Access denied.', 'jenga-portal' ) . '</p></div>';
        }

        ob_start();
        
        if ( isset( $_GET['project_id'] ) ) {
            include JENGA_PORTAL_DIR . 'templates/portal-single-project.php';
        } else {
            include JENGA_PORTAL_DIR . 'templates/portal-projects.php';
        }
        
        return ob_get_clean();
    }

    public static function render_tickets( $atts ) {
        if ( ! self::check_access() ) {
            return '<div class="jenga-notice error"><p>' . __( 'Access denied.', 'jenga-portal' ) . '</p></div>';
        }

        ob_start();

        if ( isset( $_GET['ticket_id'] ) ) {
            include JENGA_PORTAL_DIR . 'templates/portal-single-ticket.php';
        } else {
            include JENGA_PORTAL_DIR . 'templates/portal-tickets.php';
        }

        return ob_get_clean();
    }

    public static function render_documents( $atts ) {
        if ( ! self::check_access() ) {
            return '<div class="jenga-notice error"><p>' . __( 'Access denied.', 'jenga-portal' ) . '</p></div>';
        }

        ob_start();
        include JENGA_PORTAL_DIR . 'templates/portal-documents.php';
        return ob_get_clean();
    }
}
