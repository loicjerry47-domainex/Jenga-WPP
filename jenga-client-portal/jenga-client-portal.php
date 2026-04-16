<?php
/**
 * Plugin Name: Jenga Client Portal
 * Plugin URI: https://wxza.net/jenga-portal
 * Description: A branded client portal for freelancers and agencies. Give clients a private dashboard to track projects, submit support tickets, access documents, and communicate — all within your WordPress site.
 * Version: 1.0.0
 * Author: Loic Hazoume
 * Author URI: https://wxza.net
 * License: GPL-2.0-or-later
 * Text Domain: jenga-portal
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'JENGA_PORTAL_VERSION', '1.0.0' );
define( 'JENGA_PORTAL_DIR', plugin_dir_path( __FILE__ ) );
define( 'JENGA_PORTAL_URL', plugin_dir_url( __FILE__ ) );

// Includes
require_once JENGA_PORTAL_DIR . 'includes/class-portal-setup.php';
require_once JENGA_PORTAL_DIR . 'includes/class-portal-cpt.php';
require_once JENGA_PORTAL_DIR . 'includes/class-portal-shortcodes.php';
require_once JENGA_PORTAL_DIR . 'includes/class-portal-ajax.php';
require_once JENGA_PORTAL_DIR . 'includes/class-portal-notifications.php';
require_once JENGA_PORTAL_DIR . 'includes/class-portal-admin.php';
require_once JENGA_PORTAL_DIR . 'includes/class-portal-rest-api.php';

// Hooks
register_activation_hook( __FILE__, array( 'Jenga_Portal_Setup', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Jenga_Portal_Setup', 'deactivate' ) );

// Initialize
add_action( 'plugins_loaded', 'jenga_portal_init' );
function jenga_portal_init() {
    Jenga_Portal_CPT::init();
    Jenga_Portal_Shortcodes::init();
    Jenga_Portal_Ajax::init();
    Jenga_Portal_Admin::init();
    Jenga_Portal_REST_API::init();
}

// Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'jenga_portal_enqueue_scripts' );
function jenga_portal_enqueue_scripts() {
    wp_enqueue_style( 'jenga-portal-frontend', JENGA_PORTAL_URL . 'assets/css/portal-frontend.css', array(), JENGA_PORTAL_VERSION );
    wp_enqueue_script( 'jenga-portal-dashboard', JENGA_PORTAL_URL . 'assets/js/portal-dashboard.js', array( 'jquery' ), JENGA_PORTAL_VERSION, true );
    wp_enqueue_script( 'jenga-portal-tickets', JENGA_PORTAL_URL . 'assets/js/portal-tickets.js', array( 'jquery' ), JENGA_PORTAL_VERSION, true );
    
    wp_localize_script( 'jenga-portal-tickets', 'jenga_portal_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'jenga_portal_nonce' )
    ) );
}

add_action( 'admin_enqueue_scripts', 'jenga_portal_admin_scripts' );
function jenga_portal_admin_scripts() {
    wp_enqueue_style( 'jenga-portal-admin', JENGA_PORTAL_URL . 'assets/css/portal-admin.css', array(), JENGA_PORTAL_VERSION );
    wp_enqueue_script( 'jenga-portal-admin', JENGA_PORTAL_URL . 'assets/js/portal-admin.js', array( 'jquery' ), JENGA_PORTAL_VERSION, true );
}
