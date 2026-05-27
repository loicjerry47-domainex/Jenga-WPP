<?php

class Jenga_Portal_Notifications {

    private static function get_settings() {
        return get_option( 'jenga_portal_settings', array() );
    }

    private static function get_brand_name() {
        $settings = self::get_settings();
        return ! empty( $settings['brand_name'] ) ? $settings['brand_name'] : get_bloginfo( 'name' );
    }

    private static function is_enabled( $toggle_key, $default = true ) {
        $settings = self::get_settings();
        if ( ! isset( $settings[ $toggle_key ] ) ) {
            return $default;
        }
        return (bool) $settings[ $toggle_key ];
    }

    private static function html_email( $brand, $heading, $body_html ) {
        $accent = '#c9a44a';
        return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f4f4f4;font-family:\'Helvetica Neue\',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:40px 20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a26;border-radius:12px;overflow:hidden;border:1px solid rgba(255,255,255,0.08);">
  <tr><td style="padding:32px 40px;border-bottom:1px solid rgba(255,255,255,0.08);">
    <p style="margin:0;font-size:18px;font-weight:600;color:' . esc_attr( $accent ) . ';">' . esc_html( $brand ) . '</p>
    <h1 style="margin:8px 0 0 0;font-size:22px;font-weight:400;color:#ffffff;font-family:Georgia,serif;">' . esc_html( $heading ) . '</h1>
  </td></tr>
  <tr><td style="padding:32px 40px;color:#c8c8d0;font-size:15px;line-height:1.7;">' . $body_html . '</td></tr>
  <tr><td style="padding:24px 40px;border-top:1px solid rgba(255,255,255,0.08);text-align:center;">
    <p style="margin:0;font-size:12px;color:#5c5c70;">' . esc_html( $brand ) . ' &mdash; Client Portal</p>
  </td></tr>
</table>
</td></tr></table>
</body></html>';
    }

    private static function send( $to, $subject, $html ) {
        add_filter( 'wp_mail_content_type', array( __CLASS__, 'set_html_content_type' ) );
        wp_mail( $to, $subject, $html );
        remove_filter( 'wp_mail_content_type', array( __CLASS__, 'set_html_content_type' ) );
    }

    public static function set_html_content_type() {
        return 'text/html';
    }

    // ── Notify admin: client submitted a new ticket ───────────────────────────

    public static function notify_admin_new_ticket( $ticket_id ) {
        if ( ! self::is_enabled( 'notify_admin_ticket' ) ) {
            return;
        }

        $ticket  = get_post( $ticket_id );
        $client  = get_user_by( 'id', $ticket->post_author );
        $brand   = self::get_brand_name();
        $priority = get_post_meta( $ticket_id, '_jenga_priority', true );
        $admin_url = admin_url( 'post.php?post=' . $ticket_id . '&action=edit' );

        $subject = sprintf( '[%s] New Support Ticket: %s', $brand, $ticket->post_title );

        $body = '<p>A new support ticket has been submitted by <strong>' . esc_html( $client->display_name ) . '</strong> (' . esc_html( $client->user_email ) . ').</p>
<table style="width:100%;border-collapse:collapse;margin:20px 0;">
<tr><td style="padding:8px 0;color:#8e9299;width:120px;">Subject:</td><td style="padding:8px 0;color:#fff;">' . esc_html( $ticket->post_title ) . '</td></tr>
<tr><td style="padding:8px 0;color:#8e9299;">Priority:</td><td style="padding:8px 0;color:#fff;">' . esc_html( $priority ) . '</td></tr>
</table>
<p><a href="' . esc_url( $admin_url ) . '" style="display:inline-block;padding:12px 24px;background:#c9a44a;color:#0a0a0f;text-decoration:none;border-radius:6px;font-weight:600;">View Ticket in Admin</a></p>';

        self::send( get_option( 'admin_email' ), $subject, self::html_email( $brand, 'New Support Ticket', $body ) );
    }

    // ── Notify admin: client replied to ticket ────────────────────────────────

    public static function notify_admin_ticket_reply( $ticket_id, $reply_content, $user_id ) {
        if ( ! self::is_enabled( 'notify_admin_ticket_reply' ) ) {
            return;
        }

        $ticket    = get_post( $ticket_id );
        $client    = get_user_by( 'id', $user_id );
        $brand     = self::get_brand_name();
        $admin_url = admin_url( 'post.php?post=' . $ticket_id . '&action=edit' );

        $subject = sprintf( '[%s] New Reply: %s', $brand, $ticket->post_title );

        $body = '<p><strong>' . esc_html( $client->display_name ) . '</strong> replied to a ticket.</p>
<blockquote style="border-left:3px solid #c9a44a;margin:16px 0;padding:12px 16px;background:rgba(201,164,74,0.05);color:#e1e1e6;">' . nl2br( esc_html( $reply_content ) ) . '</blockquote>
<p><a href="' . esc_url( $admin_url ) . '" style="display:inline-block;padding:12px 24px;background:#c9a44a;color:#0a0a0f;text-decoration:none;border-radius:6px;font-weight:600;">View Ticket in Admin</a></p>';

        self::send( get_option( 'admin_email' ), $subject, self::html_email( $brand, 'New Ticket Reply', $body ) );
    }

    // ── Notify client: ticket status changed ──────────────────────────────────

    public static function notify_client_ticket_status_change( $ticket_id, $new_status ) {
        if ( ! self::is_enabled( 'notify_client_status' ) ) {
            return;
        }

        $ticket    = get_post( $ticket_id );
        $client_id = get_post_meta( $ticket_id, '_jenga_client_id', true );
        $client    = get_user_by( 'id', $client_id );

        if ( ! $client ) {
            return;
        }

        $brand    = self::get_brand_name();
        $settings = self::get_settings();
        $page_url = isset( $settings['tickets_page'] ) ? get_permalink( $settings['tickets_page'] ) : home_url();
        $ticket_url = add_query_arg( 'ticket_id', $ticket_id, $page_url );

        $subject = sprintf( '[%s] Ticket Updated: %s', $brand, $ticket->post_title );

        $body = '<p>The status of your support ticket has been updated.</p>
<table style="width:100%;border-collapse:collapse;margin:20px 0;">
<tr><td style="padding:8px 0;color:#8e9299;width:120px;">Ticket:</td><td style="padding:8px 0;color:#fff;">' . esc_html( $ticket->post_title ) . '</td></tr>
<tr><td style="padding:8px 0;color:#8e9299;">New Status:</td><td style="padding:8px 0;color:#c9a44a;font-weight:600;">' . esc_html( $new_status ) . '</td></tr>
</table>
<p><a href="' . esc_url( $ticket_url ) . '" style="display:inline-block;padding:12px 24px;background:#c9a44a;color:#0a0a0f;text-decoration:none;border-radius:6px;font-weight:600;">View Your Ticket</a></p>';

        self::send( $client->user_email, $subject, self::html_email( $brand, 'Ticket Status Updated', $body ) );
    }

    // ── Notify client: admin posted a reply ───────────────────────────────────

    public static function notify_client_new_reply( $ticket_id, $comment_id ) {
        if ( ! self::is_enabled( 'notify_client_reply' ) ) {
            return;
        }

        $ticket    = get_post( $ticket_id );
        $client_id = get_post_meta( $ticket_id, '_jenga_client_id', true );
        $client    = get_user_by( 'id', $client_id );
        $comment   = get_comment( $comment_id );

        if ( ! $client || ! $comment ) {
            return;
        }

        $brand    = self::get_brand_name();
        $settings = self::get_settings();
        $page_url   = isset( $settings['tickets_page'] ) ? get_permalink( $settings['tickets_page'] ) : home_url();
        $ticket_url = add_query_arg( 'ticket_id', $ticket_id, $page_url );

        $subject = sprintf( '[%s] New Reply to Your Ticket: %s', $brand, $ticket->post_title );

        $body = '<p>The support team has replied to your ticket.</p>
<blockquote style="border-left:3px solid #c9a44a;margin:16px 0;padding:12px 16px;background:rgba(201,164,74,0.05);color:#e1e1e6;">' . nl2br( esc_html( $comment->comment_content ) ) . '</blockquote>
<p><a href="' . esc_url( $ticket_url ) . '" style="display:inline-block;padding:12px 24px;background:#c9a44a;color:#0a0a0f;text-decoration:none;border-radius:6px;font-weight:600;">Reply to This Ticket</a></p>';

        self::send( $client->user_email, $subject, self::html_email( $brand, 'Support Team Reply', $body ) );
    }

    // ── Notify client: new document uploaded ──────────────────────────────────

    public static function notify_client_new_document( $document_id ) {
        if ( ! self::is_enabled( 'notify_client_document' ) ) {
            return;
        }

        $document  = get_post( $document_id );
        $client_id = get_post_meta( $document_id, '_jenga_client_id', true );
        $client    = get_user_by( 'id', $client_id );

        if ( ! $client ) {
            return;
        }

        $brand    = self::get_brand_name();
        $doc_type = get_post_meta( $document_id, '_jenga_doc_type', true );
        $settings = self::get_settings();
        $docs_url = isset( $settings['documents_page'] ) ? get_permalink( $settings['documents_page'] ) : home_url();

        $subject = sprintf( '[%s] New Document Available: %s', $brand, $document->post_title );

        $body = '<p>A new document has been uploaded to your client portal.</p>
<table style="width:100%;border-collapse:collapse;margin:20px 0;">
<tr><td style="padding:8px 0;color:#8e9299;width:120px;">Document:</td><td style="padding:8px 0;color:#fff;">' . esc_html( $document->post_title ) . '</td></tr>
<tr><td style="padding:8px 0;color:#8e9299;">Type:</td><td style="padding:8px 0;color:#fff;">' . esc_html( $doc_type ) . '</td></tr>
</table>
<p><a href="' . esc_url( $docs_url ) . '" style="display:inline-block;padding:12px 24px;background:#c9a44a;color:#0a0a0f;text-decoration:none;border-radius:6px;font-weight:600;">View Your Documents</a></p>';

        self::send( $client->user_email, $subject, self::html_email( $brand, 'New Document Available', $body ) );
    }

    // ── Notify client: project status changed ─────────────────────────────────

    public static function notify_client_project_status_change( $project_id, $new_status ) {
        if ( ! self::is_enabled( 'notify_client_project' ) ) {
            return;
        }

        $project   = get_post( $project_id );
        $client_id = get_post_meta( $project_id, '_jenga_client_id', true );
        $client    = get_user_by( 'id', $client_id );

        if ( ! $client ) {
            return;
        }

        $brand    = self::get_brand_name();
        $progress = get_post_meta( $project_id, '_jenga_progress', true );
        $settings = self::get_settings();
        $page_url    = isset( $settings['projects_page'] ) ? get_permalink( $settings['projects_page'] ) : home_url();
        $project_url = add_query_arg( 'project_id', $project_id, $page_url );

        $subject = sprintf( '[%s] Project Update: %s', $brand, $project->post_title );

        $body = '<p>Your project status has been updated.</p>
<table style="width:100%;border-collapse:collapse;margin:20px 0;">
<tr><td style="padding:8px 0;color:#8e9299;width:120px;">Project:</td><td style="padding:8px 0;color:#fff;">' . esc_html( $project->post_title ) . '</td></tr>
<tr><td style="padding:8px 0;color:#8e9299;">New Status:</td><td style="padding:8px 0;color:#c9a44a;font-weight:600;">' . esc_html( $new_status ) . '</td></tr>
<tr><td style="padding:8px 0;color:#8e9299;">Progress:</td><td style="padding:8px 0;color:#fff;">' . esc_attr( $progress ) . '%</td></tr>
</table>
<p><a href="' . esc_url( $project_url ) . '" style="display:inline-block;padding:12px 24px;background:#c9a44a;color:#0a0a0f;text-decoration:none;border-radius:6px;font-weight:600;">View Project Details</a></p>';

        self::send( $client->user_email, $subject, self::html_email( $brand, 'Project Status Updated', $body ) );
    }
}
