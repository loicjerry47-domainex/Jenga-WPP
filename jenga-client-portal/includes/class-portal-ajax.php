<?php

class Jenga_Portal_Ajax {

    public static function init() {
        add_action( 'wp_ajax_jenga_submit_ticket', array( __CLASS__, 'submit_ticket' ) );
        add_action( 'wp_ajax_nopriv_jenga_submit_ticket', array( __CLASS__, 'no_access_ticket' ) );
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

        wp_send_json_success( array( 
            'message'   => __( 'Ticket submitted successfully!', 'jenga-portal' ),
            'ticket_id' => $ticket_id
        ) );
    }

    public static function no_access_ticket() {
        wp_send_json_error( array( 'message' => __( 'Please log in.', 'jenga-portal' ) ) );
    }
}
