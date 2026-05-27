<?php

class Jenga_Portal_REST_API {

    const NAMESPACE = 'jenga-portal/v1';

    public static function init() {
        add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
    }

    public static function register_routes() {
        // Dashboard stats for the logged-in client
        register_rest_route( self::NAMESPACE, '/stats', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_stats' ),
            'permission_callback' => array( __CLASS__, 'require_portal_access' ),
        ) );

        // Projects for the logged-in client
        register_rest_route( self::NAMESPACE, '/projects', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_projects' ),
            'permission_callback' => array( __CLASS__, 'require_portal_access' ),
        ) );

        // Tickets for the logged-in client
        register_rest_route( self::NAMESPACE, '/tickets', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_tickets' ),
            'permission_callback' => array( __CLASS__, 'require_portal_access' ),
        ) );
    }

    public static function require_portal_access() {
        return is_user_logged_in() && current_user_can( 'portal_view_dashboard' );
    }

    public static function get_stats( WP_REST_Request $request ) {
        $user_id = get_current_user_id();

        $active_projects = count( get_posts( array(
            'post_type'      => 'jenga_project',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array( 'key' => '_jenga_client_id', 'value' => $user_id ),
                array( 'key' => '_jenga_status', 'value' => 'Completed', 'compare' => '!=' ),
            ),
        ) ) );

        $open_tickets = count( get_posts( array(
            'post_type'      => 'jenga_ticket',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array( 'key' => '_jenga_client_id', 'value' => $user_id ),
                array( 'key' => '_jenga_ticket_status', 'value' => array( 'Resolved', 'Closed' ), 'compare' => 'NOT IN' ),
            ),
        ) ) );

        $documents = count( get_posts( array(
            'post_type'      => 'jenga_document',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array( array( 'key' => '_jenga_client_id', 'value' => $user_id ) ),
        ) ) );

        return rest_ensure_response( array(
            'active_projects' => $active_projects,
            'open_tickets'    => $open_tickets,
            'documents'       => $documents,
        ) );
    }

    public static function get_projects( WP_REST_Request $request ) {
        $user_id  = get_current_user_id();
        $projects = get_posts( array(
            'post_type'      => 'jenga_project',
            'posts_per_page' => -1,
            'meta_query'     => array( array( 'key' => '_jenga_client_id', 'value' => $user_id ) ),
        ) );

        $data = array();
        foreach ( $projects as $p ) {
            $data[] = array(
                'id'       => $p->ID,
                'title'    => $p->post_title,
                'status'   => get_post_meta( $p->ID, '_jenga_status', true ),
                'progress' => (int) get_post_meta( $p->ID, '_jenga_progress', true ),
                'due_date' => get_post_meta( $p->ID, '_jenga_due_date', true ),
            );
        }

        return rest_ensure_response( $data );
    }

    public static function get_tickets( WP_REST_Request $request ) {
        $user_id = get_current_user_id();
        $tickets = get_posts( array(
            'post_type'      => 'jenga_ticket',
            'posts_per_page' => -1,
            'meta_query'     => array( array( 'key' => '_jenga_client_id', 'value' => $user_id ) ),
        ) );

        $data = array();
        foreach ( $tickets as $t ) {
            $data[] = array(
                'id'       => $t->ID,
                'title'    => $t->post_title,
                'status'   => get_post_meta( $t->ID, '_jenga_ticket_status', true ),
                'priority' => get_post_meta( $t->ID, '_jenga_priority', true ),
                'date'     => $t->post_date,
            );
        }

        return rest_ensure_response( $data );
    }
}
