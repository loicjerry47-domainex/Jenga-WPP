<?php

class Jenga_Portal_Setup {

    public static function activate() {
        self::add_roles_and_caps();
        // Trigger rewrite rules flush
        update_option( 'jenga_portal_flush_rewrite_rules', true );
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }

    private static function add_roles_and_caps() {
        // Add Portal Client role
        add_role( 'portal_client', __( 'Portal Client', 'jenga-portal' ), array(
            'read'                    => true,
            'portal_view_dashboard'   => true,
            'portal_view_projects'    => true,
            'portal_create_tickets'   => true,
            'portal_view_tickets'     => true,
            'portal_view_documents'   => true,
            'portal_upload_documents' => true,
        ) );

        // Add capabilities to Administrator
        $admin_role = get_role( 'administrator' );
        if ( $admin_role ) {
            $admin_role->add_cap( 'portal_view_dashboard' );
            $admin_role->add_cap( 'portal_view_projects' );
            $admin_role->add_cap( 'portal_manage_projects' );
            $admin_role->add_cap( 'portal_create_tickets' );
            $admin_role->add_cap( 'portal_view_tickets' );
            $admin_role->add_cap( 'portal_manage_tickets' );
            $admin_role->add_cap( 'portal_view_documents' );
            $admin_role->add_cap( 'portal_upload_documents' );
            $admin_role->add_cap( 'portal_manage_documents' );
            $admin_role->add_cap( 'portal_manage_clients' );
        }
    }
}
