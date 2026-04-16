<?php

class Jenga_Portal_CPT {

    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_post_types' ) );
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
        add_action( 'save_post', array( __CLASS__, 'save_meta_boxes' ) );
        
        // Check if rewrite rules need flushing
        if ( get_option( 'jenga_portal_flush_rewrite_rules' ) ) {
            flush_rewrite_rules();
            delete_option( 'jenga_portal_flush_rewrite_rules' );
        }
    }

    public static function register_post_types() {
        // Projects
        register_post_type( 'jenga_project', array(
            'labels'      => array(
                'name'          => __( 'Projects', 'jenga-portal' ),
                'singular_name' => __( 'Project', 'jenga-portal' ),
            ),
            'public'      => false,
            'show_ui'     => true,
            'show_in_menu'=> 'jenga-portal',
            'supports'    => array( 'title', 'editor', 'thumbnail' ),
        ) );

        // Tickets
        register_post_type( 'jenga_ticket', array(
            'labels'      => array(
                'name'          => __( 'Tickets', 'jenga-portal' ),
                'singular_name' => __( 'Ticket', 'jenga-portal' ),
            ),
            'public'      => false,
            'show_ui'     => true,
            'show_in_menu'=> 'jenga-portal',
            'supports'    => array( 'title', 'editor', 'comments' ),
        ) );

        // Documents
        register_post_type( 'jenga_document', array(
            'labels'      => array(
                'name'          => __( 'Documents', 'jenga-portal' ),
                'singular_name' => __( 'Document', 'jenga-portal' ),
            ),
            'public'      => false,
            'show_ui'     => true,
            'show_in_menu'=> 'jenga-portal',
            'supports'    => array( 'title' ),
        ) );
    }

    public static function register_taxonomies() {
        // Project Type
        register_taxonomy( 'project_type', 'jenga_project', array(
            'labels'       => array(
                'name' => __( 'Project Types', 'jenga-portal' ),
            ),
            'hierarchical' => true,
            'show_ui'      => true,
        ) );

        // Ticket Category
        register_taxonomy( 'ticket_category', 'jenga_ticket', array(
            'labels'       => array(
                'name' => __( 'Ticket Categories', 'jenga-portal' ),
            ),
            'hierarchical' => true,
            'show_ui'      => true,
        ) );
    }

    public static function add_meta_boxes() {
        add_meta_box( 'jenga_project_meta', __( 'Project Details', 'jenga-portal' ), array( __CLASS__, 'render_project_meta' ), 'jenga_project', 'normal', 'high' );
        add_meta_box( 'jenga_ticket_meta', __( 'Ticket Details', 'jenga-portal' ), array( __CLASS__, 'render_ticket_meta' ), 'jenga_ticket', 'side', 'high' );
        add_meta_box( 'jenga_document_meta', __( 'Document Details', 'jenga-portal' ), array( __CLASS__, 'render_document_meta' ), 'jenga_document', 'normal', 'high' );
    }

    public static function render_project_meta( $post ) {
        wp_nonce_field( 'jenga_save_meta', 'jenga_meta_nonce' );
        $client_id = get_post_meta( $post->ID, '_jenga_client_id', true );
        $status    = get_post_meta( $post->ID, '_jenga_status', true );
        $start     = get_post_meta( $post->ID, '_jenga_start_date', true );
        $due       = get_post_meta( $post->ID, '_jenga_due_date', true );
        $progress  = get_post_meta( $post->ID, '_jenga_progress', true );
        $budget    = get_post_meta( $post->ID, '_jenga_budget', true );
        $url       = get_post_meta( $post->ID, '_jenga_url', true );

        $clients = get_users( array( 'role__in' => array( 'portal_client', 'administrator' ) ) );
        ?>
        <p>
            <label><strong><?php _e( 'Client:', 'jenga-portal' ); ?></strong><br>
            <select name="jenga_client_id" class="widefat">
                <option value=""><?php _e( 'Select Client', 'jenga-portal' ); ?></option>
                <?php foreach( $clients as $client ) : ?>
                    <option value="<?php echo esc_attr( $client->ID ); ?>" <?php selected( $client_id, $client->ID ); ?>><?php echo esc_html( $client->display_name ); ?> (<?php echo esc_html( $client->user_email ); ?>)</option>
                <?php endforeach; ?>
            </select>
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Status:', 'jenga-portal' ); ?></strong><br>
            <select name="jenga_status" class="widefat">
                <option value="Not Started" <?php selected( $status, 'Not Started' ); ?>>Not Started</option>
                <option value="In Progress" <?php selected( $status, 'In Progress' ); ?>>In Progress</option>
                <option value="Under Review" <?php selected( $status, 'Under Review' ); ?>>Under Review</option>
                <option value="Completed" <?php selected( $status, 'Completed' ); ?>>Completed</option>
            </select>
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Start Date:', 'jenga-portal' ); ?></strong><br>
            <input type="date" name="jenga_start_date" value="<?php echo esc_attr( $start ); ?>" class="widefat">
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Due Date:', 'jenga-portal' ); ?></strong><br>
            <input type="date" name="jenga_due_date" value="<?php echo esc_attr( $due ); ?>" class="widefat">
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Progress (%):', 'jenga-portal' ); ?></strong><br>
            <input type="number" min="0" max="100" name="jenga_progress" value="<?php echo esc_attr( $progress ); ?>" class="widefat">
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Budget:', 'jenga-portal' ); ?></strong><br>
            <input type="text" name="jenga_budget" value="<?php echo esc_attr( $budget ); ?>" class="widefat">
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Project URL:', 'jenga-portal' ); ?></strong><br>
            <input type="url" name="jenga_url" value="<?php echo esc_attr( $url ); ?>" class="widefat">
            </label>
        </p>
        <?php
    }

    public static function render_ticket_meta( $post ) {
        wp_nonce_field( 'jenga_save_meta', 'jenga_meta_nonce' );
        $client_id  = get_post_meta( $post->ID, '_jenga_client_id', true );
        $project_id = get_post_meta( $post->ID, '_jenga_project_id', true );
        $priority   = get_post_meta( $post->ID, '_jenga_priority', true );
        $status     = get_post_meta( $post->ID, '_jenga_ticket_status', true );

        $clients = get_users( array( 'role__in' => array( 'portal_client', 'administrator' ) ) );
        $projects = get_posts( array( 'post_type' => 'jenga_project', 'numberposts' => -1 ) );
        ?>
        <p>
            <label><strong><?php _e( 'Client:', 'jenga-portal' ); ?></strong><br>
            <select name="jenga_ticket_client_id" class="widefat">
                <option value=""><?php _e( 'Select Client', 'jenga-portal' ); ?></option>
                <?php foreach( $clients as $client ) : ?>
                    <option value="<?php echo esc_attr( $client->ID ); ?>" <?php selected( $client_id, $client->ID ); ?>><?php echo esc_html( $client->display_name ); ?></option>
                <?php endforeach; ?>
            </select>
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Related Project:', 'jenga-portal' ); ?></strong><br>
            <select name="jenga_project_id" class="widefat">
                <option value=""><?php _e( 'None', 'jenga-portal' ); ?></option>
                <?php foreach( $projects as $proj ) : ?>
                    <option value="<?php echo esc_attr( $proj->ID ); ?>" <?php selected( $project_id, $proj->ID ); ?>><?php echo esc_html( $proj->post_title ); ?></option>
                <?php endforeach; ?>
            </select>
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Priority:', 'jenga-portal' ); ?></strong><br>
            <select name="jenga_priority" class="widefat">
                <option value="Low" <?php selected( $priority, 'Low' ); ?>>Low</option>
                <option value="Medium" <?php selected( $priority, 'Medium' ); ?>>Medium</option>
                <option value="High" <?php selected( $priority, 'High' ); ?>>High</option>
                <option value="Urgent" <?php selected( $priority, 'Urgent' ); ?>>Urgent</option>
            </select>
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Status:', 'jenga-portal' ); ?></strong><br>
            <select name="jenga_ticket_status" class="widefat">
                <option value="Open" <?php selected( $status, 'Open' ); ?>>Open</option>
                <option value="In Progress" <?php selected( $status, 'In Progress' ); ?>>In Progress</option>
                <option value="Awaiting Reply" <?php selected( $status, 'Awaiting Reply' ); ?>>Awaiting Reply</option>
                <option value="Resolved" <?php selected( $status, 'Resolved' ); ?>>Resolved</option>
                <option value="Closed" <?php selected( $status, 'Closed' ); ?>>Closed</option>
            </select>
            </label>
        </p>
        <?php
    }

    public static function render_document_meta( $post ) {
        wp_nonce_field( 'jenga_save_meta', 'jenga_meta_nonce' );
        $client_id  = get_post_meta( $post->ID, '_jenga_client_id', true );
        $project_id = get_post_meta( $post->ID, '_jenga_project_id', true );
        $doc_type   = get_post_meta( $post->ID, '_jenga_doc_type', true );
        $file_url   = get_post_meta( $post->ID, '_jenga_file_url', true );

        $clients = get_users( array( 'role__in' => array( 'portal_client', 'administrator' ) ) );
        $projects = get_posts( array( 'post_type' => 'jenga_project', 'numberposts' => -1 ) );
        ?>
        <p>
            <label><strong><?php _e( 'Client:', 'jenga-portal' ); ?></strong><br>
            <select name="jenga_doc_client_id" class="widefat">
                <option value=""><?php _e( 'Select Client', 'jenga-portal' ); ?></option>
                <?php foreach( $clients as $client ) : ?>
                    <option value="<?php echo esc_attr( $client->ID ); ?>" <?php selected( $client_id, $client->ID ); ?>><?php echo esc_html( $client->display_name ); ?></option>
                <?php endforeach; ?>
            </select>
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Related Project:', 'jenga-portal' ); ?></strong><br>
            <select name="jenga_doc_project_id" class="widefat">
                <option value=""><?php _e( 'None', 'jenga-portal' ); ?></option>
                <?php foreach( $projects as $proj ) : ?>
                    <option value="<?php echo esc_attr( $proj->ID ); ?>" <?php selected( $project_id, $proj->ID ); ?>><?php echo esc_html( $proj->post_title ); ?></option>
                <?php endforeach; ?>
            </select>
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'Type:', 'jenga-portal' ); ?></strong><br>
            <select name="jenga_doc_type" class="widefat">
                <option value="Invoice" <?php selected( $doc_type, 'Invoice' ); ?>>Invoice</option>
                <option value="Contract" <?php selected( $doc_type, 'Contract' ); ?>>Contract</option>
                <option value="Deliverable" <?php selected( $doc_type, 'Deliverable' ); ?>>Deliverable</option>
                <option value="Report" <?php selected( $doc_type, 'Report' ); ?>>Report</option>
                <option value="Other" <?php selected( $doc_type, 'Other' ); ?>>Other</option>
            </select>
            </label>
        </p>
        <p>
            <label><strong><?php _e( 'File URL:', 'jenga-portal' ); ?></strong><br>
            <input type="text" name="jenga_file_url" value="<?php echo esc_attr( $file_url ); ?>" class="widefat">
            <em>(Upload media via Add Media button and paste URL here, or use portal upload)</em>
            </label>
        </p>
        <?php
    }

    public static function save_meta_boxes( $post_id ) {
        if ( ! isset( $_POST['jenga_meta_nonce'] ) || ! wp_verify_nonce( $_POST['jenga_meta_nonce'], 'jenga_save_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'jenga_project' === $_POST['post_type'] ) {
            update_post_meta( $post_id, '_jenga_client_id', sanitize_text_field( $_POST['jenga_client_id'] ) );
            update_post_meta( $post_id, '_jenga_status', sanitize_text_field( $_POST['jenga_status'] ) );
            update_post_meta( $post_id, '_jenga_start_date', sanitize_text_field( $_POST['jenga_start_date'] ) );
            update_post_meta( $post_id, '_jenga_due_date', sanitize_text_field( $_POST['jenga_due_date'] ) );
            update_post_meta( $post_id, '_jenga_progress', absint( $_POST['jenga_progress'] ) );
            update_post_meta( $post_id, '_jenga_budget', sanitize_text_field( $_POST['jenga_budget'] ) );
            update_post_meta( $post_id, '_jenga_url', esc_url_raw( $_POST['jenga_url'] ) );
        }

        if ( 'jenga_ticket' === $_POST['post_type'] ) {
            update_post_meta( $post_id, '_jenga_client_id', sanitize_text_field( $_POST['jenga_ticket_client_id'] ) );
            update_post_meta( $post_id, '_jenga_project_id', sanitize_text_field( $_POST['jenga_project_id'] ) );
            update_post_meta( $post_id, '_jenga_priority', sanitize_text_field( $_POST['jenga_priority'] ) );
            update_post_meta( $post_id, '_jenga_ticket_status', sanitize_text_field( $_POST['jenga_ticket_status'] ) );
        }

        if ( 'jenga_document' === $_POST['post_type'] ) {
            update_post_meta( $post_id, '_jenga_client_id', sanitize_text_field( $_POST['jenga_doc_client_id'] ) );
            update_post_meta( $post_id, '_jenga_project_id', sanitize_text_field( $_POST['jenga_doc_project_id'] ) );
            update_post_meta( $post_id, '_jenga_doc_type', sanitize_text_field( $_POST['jenga_doc_type'] ) );
            update_post_meta( $post_id, '_jenga_file_url', esc_url_raw( $_POST['jenga_file_url'] ) );
        }
    }
}
