<?php

class Jenga_Portal_Ajax {

    public static function init() {
        add_action( 'wp_ajax_jenga_submit_ticket',      array( __CLASS__, 'submit_ticket' ) );
        add_action( 'wp_ajax_nopriv_jenga_submit_ticket', array( __CLASS__, 'no_access_ticket' ) );
        add_action( 'wp_ajax_jenga_download_document',  array( __CLASS__, 'download_document' ) );
    }

    public static function submit_ticket() {
        check_ajax_referer( 'jenga_portal_nonce', 'nonce' );

        if ( ! current_user_can( 'portal_create_tickets' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'jenga-portal' ) ) );
        }

        $title       = sanitize_text_field( $_POST['title'] ?? '' );
        $category    = absint( $_POST['category'] ?? 0 );
        $project_id  = absint( $_POST['project_id'] ?? 0 );
        $priority    = sanitize_text_field( $_POST['priority'] ?? 'Medium' );
        $description = sanitize_textarea_field( $_POST['description'] ?? '' );
        
        if ( empty( $title ) || empty( $description ) ) {
            wp_send_json_error( array( 'message' => __( 'Title and description are required.', 'jenga-portal' ) ) );
        }

        $ticket_id = wp_insert_post( array(
            'post_title'   => $title,
            'post_content' => $description,
            'post_status'  => 'publish',
            'post_type'    => 'jenga_ticket',
            'post_author'  => get_current_user_id()
        ) );

        if ( is_wp_error( $ticket_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Error creating ticket.', 'jenga-portal' ) ) );
        }

        update_post_meta( $ticket_id, '_jenga_client_id', get_current_user_id() );
        update_post_meta( $ticket_id, '_jenga_ticket_status', 'Open' );
        update_post_meta( $ticket_id, '_jenga_priority', $priority );
        if ( $project_id ) {
            update_post_meta( $ticket_id, '_jenga_project_id', $project_id );
        }
        if ( $category ) {
            wp_set_object_terms( $ticket_id, $category, 'ticket_category' );
        }

        Jenga_Portal_Notifications::notify_admin_new_ticket( $ticket_id );

        do_action( 'jenga_portal_after_ticket_submit', $ticket_id, array(
            'title'       => $title,
            'category'    => $category,
            'project_id'  => $project_id,
            'priority'    => $priority,
            'description' => $description,
            'user_id'     => get_current_user_id(),
        ) );

        wp_send_json_success( array(
            'message'   => __( 'Ticket submitted successfully!', 'jenga-portal' ),
            'ticket_id' => $ticket_id
        ) );
    }

    public static function no_access_ticket() {
        wp_send_json_error( array( 'message' => __( 'Please log in.', 'jenga-portal' ) ) );
    }

    // ── Secure document download ──────────────────────────────────────────────

    public static function download_document() {
        check_ajax_referer( 'jenga_portal_nonce', 'nonce' );

        if ( ! current_user_can( 'portal_view_documents' ) ) {
            wp_die( __( 'Permission denied.', 'jenga-portal' ), 403 );
        }

        $doc_id = absint( $_GET['doc_id'] ?? 0 );
        if ( ! $doc_id ) {
            wp_die( __( 'Invalid document.', 'jenga-portal' ), 400 );
        }

        $doc = get_post( $doc_id );
        if ( ! $doc || $doc->post_type !== 'jenga_document' || $doc->post_status !== 'publish' ) {
            wp_die( __( 'Document not found.', 'jenga-portal' ), 404 );
        }

        $client_id = get_post_meta( $doc_id, '_jenga_client_id', true );
        if ( (int) $client_id !== get_current_user_id() && ! current_user_can( 'portal_manage_documents' ) ) {
            wp_die( __( 'Access denied.', 'jenga-portal' ), 403 );
        }

        $file_url = get_post_meta( $doc_id, '_jenga_file_url', true );
        if ( empty( $file_url ) ) {
            wp_die( __( 'File not available.', 'jenga-portal' ), 404 );
        }

        wp_redirect( esc_url_raw( $file_url ) );
        exit;
    }
}
