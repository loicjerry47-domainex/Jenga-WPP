<?php
/**
 * Fired when the plugin is uninstalled.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Remove role
remove_role( 'portal_client' );

// Remove capabilities from admin
$admin_role = get_role( 'administrator' );
if ( $admin_role ) {
    $admin_role->remove_cap( 'portal_view_dashboard' );
    $admin_role->remove_cap( 'portal_view_projects' );
    $admin_role->remove_cap( 'portal_manage_projects' );
    $admin_role->remove_cap( 'portal_create_tickets' );
    $admin_role->remove_cap( 'portal_view_tickets' );
    $admin_role->remove_cap( 'portal_manage_tickets' );
    $admin_role->remove_cap( 'portal_view_documents' );
    $admin_role->remove_cap( 'portal_upload_documents' );
    $admin_role->remove_cap( 'portal_manage_documents' );
    $admin_role->remove_cap( 'portal_manage_clients' );
}

// Delete custom post types
$post_types = array( 'jenga_project', 'jenga_ticket', 'jenga_document' );
foreach ( $post_types as $post_type ) {
    $posts = get_posts( array(
        'post_type'      => $post_type,
        'numberposts'    => -1,
        'fields'         => 'ids'
    ) );

    foreach ( $posts as $post_id ) {
        wp_delete_post( $post_id, true );
    }
}

// Delete options
delete_option( 'jenga_portal_settings' );
