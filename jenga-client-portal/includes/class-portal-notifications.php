<?php

class Jenga_Portal_Notifications {

    public static function notify_admin_new_ticket( $ticket_id ) {
        $settings = get_option( 'jenga_portal_settings' );
        if ( isset( $settings['notify_admin_ticket'] ) && ! $settings['notify_admin_ticket'] ) {
            return;
        }

        $ticket = get_post( $ticket_id );
        $client = get_user_by( 'id', $ticket->post_author );

        $to = get_option( 'admin_email' );
        $subject = sprintf( __( '[%s] New Support Ticket: %s', 'jenga-portal' ), get_bloginfo( 'name' ), $ticket->post_title );
        $message = sprintf( __( 'A new support ticket has been submitted by %s.', 'jenga-portal' ), $client->display_name ) . "\n\n";
        $message .= sprintf( __( 'Title: %s', 'jenga-portal' ), $ticket->post_title ) . "\n";
        $message .= sprintf( __( 'Priority: %s', 'jenga-portal' ), get_post_meta( $ticket_id, '_jenga_priority', true ) ) . "\n\n";
        $message .= __( 'View Ticket:', 'jenga-portal' ) . ' ' . admin_url( 'post.php?post=' . $ticket_id . '&action=edit' );

        wp_mail( $to, $subject, $message );
    }
}
