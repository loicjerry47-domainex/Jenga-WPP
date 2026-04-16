<?php
/**
 * Portal Dashboard Template
 */

$current_user = wp_get_current_user();
$first_name = $current_user->user_firstname ? $current_user->user_firstname : $current_user->display_name;

// Get Active Projects
$projects = get_posts( array(
    'post_type'  => 'jenga_project',
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key'   => '_jenga_client_id',
            'value' => $current_user->ID,
        ),
        array(
            'key'     => '_jenga_status',
            'value'   => 'Completed',
            'compare' => '!='
        )
    ),
    'posts_per_page' => -1
) );
$active_projects_count = count( $projects );

// Get Open Tickets
$tickets = get_posts( array(
    'post_type'  => 'jenga_ticket',
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key'   => '_jenga_client_id',
            'value' => $current_user->ID,
        ),
        array(
            'key'     => '_jenga_ticket_status',
            'value'   => array( 'Resolved', 'Closed' ),
            'compare' => 'NOT IN'
        )
    ),
    'posts_per_page' => -1
) );
$open_tickets_count = count( $tickets );

// Get Documents
$documents = get_posts( array(
    'post_type'  => 'jenga_document',
    'meta_query' => array(
        array(
            'key'   => '_jenga_client_id',
            'value' => $current_user->ID,
        )
    ),
    'posts_per_page' => -1
) );
$documents_count = count( $documents );

// Calculate Next Deadline
$next_deadline = '-';
$nearest_timestamp = PHP_INT_MAX;
foreach ( $projects as $proj ) {
    $due = get_post_meta( $proj->ID, '_jenga_due_date', true );
    if ( $due ) {
        $ts = strtotime( $due );
        if ( $ts > time() && $ts < $nearest_timestamp ) {
            $nearest_timestamp = $ts;
            $next_deadline = date_i18n( get_option( 'date_format' ), $ts );
        }
    }
}
?>

<div class="jenga-portal-wrapper">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h2><?php printf( __( 'Welcome back, %s', 'jenga-portal' ), esc_html( $first_name ) ); ?></h2>
        <a href="<?php echo wp_logout_url( get_permalink() ); ?>" class="jp-btn"><?php _e( 'Log Out', 'jenga-portal' ); ?></a>
    </div>

    <div class="jp-grid">
        <div class="jp-card jp-stat">
            <h3><?php _e( 'Active Projects', 'jenga-portal' ); ?></h3>
            <div class="jp-value"><?php echo intval( $active_projects_count ); ?></div>
        </div>
        <div class="jp-card jp-stat">
            <h3><?php _e( 'Open Tickets', 'jenga-portal' ); ?></h3>
            <div class="jp-value"><?php echo intval( $open_tickets_count ); ?></div>
        </div>
        <div class="jp-card jp-stat">
            <h3><?php _e( 'Documents', 'jenga-portal' ); ?></h3>
            <div class="jp-value"><?php echo intval( $documents_count ); ?></div>
        </div>
        <div class="jp-card jp-stat">
            <h3><?php _e( 'Next Deadline', 'jenga-portal' ); ?></h3>
            <div class="jp-value" style="font-size:1.5rem;"><?php echo esc_html( $next_deadline ); ?></div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div>
            <h3><?php _e( 'Recent Projects', 'jenga-portal' ); ?></h3>
            <?php if ( $projects ) : ?>
                <div style="display:grid; gap: 1rem;">
                    <?php 
                    $recent_projects = array_slice( $projects, 0, 3 );
                    foreach ( $recent_projects as $p ) : 
                        $status = get_post_meta( $p->ID, '_jenga_status', true );
                        $progress = get_post_meta( $p->ID, '_jenga_progress', true ) ?: 0;
                        $due = get_post_meta( $p->ID, '_jenga_due_date', true );
                        $badge_class = 'jp-badge-neutral';
                        if ( $status == 'Completed' ) $badge_class = 'jp-badge-success';
                        if ( $status == 'In Progress' ) $badge_class = 'jp-badge-info';
                        if ( $status == 'Under Review' ) $badge_class = 'jp-badge-warning';
                    ?>
                        <div class="jp-card">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <h4 style="margin:0;"><a href="?project_id=<?php echo $p->ID; ?>" style="color:#fff; text-decoration:none;"><?php echo esc_html( $p->post_title ); ?></a></h4>
                                <span class="jp-badge <?php echo $badge_class; ?>"><?php echo esc_html( $status ); ?></span>
                            </div>
                            <div class="jp-progress-container">
                                <div class="jp-progress-bar" data-progress="<?php echo esc_attr( $progress ); ?>"></div>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-top:0.5rem; font-size:0.875rem; color:var(--jp-text-muted);">
                                <span><?php echo esc_html( $progress ); ?>% <?php _e( 'Complete', 'jenga-portal' ); ?></span>
                                <?php if($due): ?><span><?php _e( 'Due:', 'jenga-portal' ); ?> <?php echo date_i18n( get_option('date_format'), strtotime($due) ); ?></span><?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="jp-card"><p><?php _e( 'No active projects at the moment.', 'jenga-portal' ); ?></p></div>
            <?php endif; ?>
            <div style="margin-top:1rem;">
                <a href="<?php echo add_query_arg( 'view', 'projects' ); ?>" style="color:var(--jp-accent);"><?php _e( 'View All Projects &rarr;', 'jenga-portal' ); ?></a>
            </div>
        </div>

        <div>
            <h3><?php _e( 'Quick Actions', 'jenga-portal' ); ?></h3>
            <div class="jp-card" style="display:flex; flex-direction:column; gap:1rem;">
                <a href="?view=tickets#new" class="jp-btn" style="text-align:center;"><?php _e( 'Submit New Ticket', 'jenga-portal' ); ?></a>
                <a href="mailto:<?php echo get_option('admin_email'); ?>" class="jp-btn" style="text-align:center; background:var(--jp-border); color:var(--jp-text);"><?php _e( 'Contact Support', 'jenga-portal' ); ?></a>
            </div>

            <h3 style="margin-top:2rem;"><?php _e( 'Recent Documents', 'jenga-portal' ); ?></h3>
            <?php if ( $documents ) : ?>
                <div style="display:flex; flex-direction:column; gap: 0.5rem;">
                    <?php 
                    $recent_docs = array_slice( $documents, 0, 5 );
                    foreach ( $recent_docs as $doc ) : 
                        $url = get_post_meta( $doc->ID, '_jenga_file_url', true );
                        $type = get_post_meta( $doc->ID, '_jenga_doc_type', true );
                    ?>
                        <div class="jp-card" style="padding: 1rem; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <div style="font-weight:600;"><?php echo esc_html( $doc->post_title ); ?></div>
                                <div style="font-size:0.75rem; color:var(--jp-text-muted);"><?php echo esc_html( $type ); ?></div>
                            </div>
                            <?php if ( $url ) : ?>
                                <a href="<?php echo esc_url( $url ); ?>" target="_blank" class="jp-btn" style="padding:0.25rem 0.5rem; font-size:0.75rem;"><?php _e( 'View', 'jenga-portal' ); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="jp-card" style="padding:1rem;"><p style="margin:0; font-size:0.875rem;"><?php _e( 'No documents available.', 'jenga-portal' ); ?></p></div>
            <?php endif; ?>
        </div>
    </div>
</div>
