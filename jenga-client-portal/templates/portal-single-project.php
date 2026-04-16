<?php
/**
 * Single Project Detail Template
 */

$project_id = intval( $_GET['project_id'] );
$project = get_post( $project_id );
$current_user = wp_get_current_user();

if ( ! $project || $project->post_type !== 'jenga_project' || get_post_meta( $project_id, '_jenga_client_id', true ) != $current_user->ID ) {
    echo '<div class="jenga-notice error"><p>' . __( 'Project not found or access denied.', 'jenga-portal' ) . '</p></div>';
    return;
}

$status = get_post_meta( $project_id, '_jenga_status', true );
$progress = get_post_meta( $project_id, '_jenga_progress', true ) ?: 0;
$start = get_post_meta( $project_id, '_jenga_start_date', true );
$due = get_post_meta( $project_id, '_jenga_due_date', true );
$budget = get_post_meta( $project_id, '_jenga_budget', true );
$url = get_post_meta( $project_id, '_jenga_url', true );

$badge_class = 'jp-badge-neutral';
if ( $status == 'Completed' ) $badge_class = 'jp-badge-success';
if ( $status == 'In Progress' ) $badge_class = 'jp-badge-info';
if ( $status == 'Under Review' ) $badge_class = 'jp-badge-warning';

?>
<div class="jenga-portal-wrapper">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h2><?php echo esc_html( $project->post_title ); ?></h2>
        <a href="<?php echo remove_query_arg( 'project_id' ); ?>" style="color:var(--jp-text-muted);"><?php _e( '&larr; Back to Projects', 'jenga-portal' ); ?></a>
    </div>

    <div class="jp-grid">
        <div class="jp-card">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0;"><?php _e( 'Project Status', 'jenga-portal' ); ?></h3>
                <span class="jp-badge <?php echo $badge_class; ?>"><?php echo esc_html( $status ); ?></span>
            </div>
            <div class="jp-progress-container" style="margin-top:1.5rem; height: 12px;">
                <div class="jp-progress-bar" data-progress="<?php echo esc_attr( $progress ); ?>"></div>
            </div>
            <div style="text-align:right; margin-top:0.5rem; font-weight:600; color:var(--jp-accent);">
                <?php echo esc_html( $progress ); ?>% <?php _e( 'Complete', 'jenga-portal' ); ?>
            </div>
        </div>

        <div class="jp-card">
            <h3 style="margin:0 0 1rem 0; border-bottom:1px solid var(--jp-border); padding-bottom:0.5rem;"><?php _e( 'Project Details', 'jenga-portal' ); ?></h3>
            <ul style="list-style:none; padding:0; margin:0; font-size:0.875rem;">
                <li style="margin-bottom:0.5rem; display:flex; justify-content:space-between;">
                    <span style="color:var(--jp-text-muted);"><?php _e( 'Start Date:', 'jenga-portal' ); ?></span>
                    <span><?php echo $start ? date_i18n( get_option('date_format'), strtotime($start) ) : '-'; ?></span>
                </li>
                <li style="margin-bottom:0.5rem; display:flex; justify-content:space-between;">
                    <span style="color:var(--jp-text-muted);"><?php _e( 'Due Date:', 'jenga-portal' ); ?></span>
                    <span><?php echo $due ? date_i18n( get_option('date_format'), strtotime($due) ) : '-'; ?></span>
                </li>
                <li style="margin-bottom:0.5rem; display:flex; justify-content:space-between;">
                    <span style="color:var(--jp-text-muted);"><?php _e( 'Budget:', 'jenga-portal' ); ?></span>
                    <span><?php echo $budget ? esc_html( $budget ) : '-'; ?></span>
                </li>
                <?php if ( $url ) : ?>
                <li style="margin-top:1rem;">
                    <a href="<?php echo esc_url( $url ); ?>" target="_blank" class="jp-btn" style="width:100%; text-align:center;"><?php _e( 'View Project Live', 'jenga-portal' ); ?></a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="jp-card">
        <h3 style="margin-top:0; border-bottom:1px solid var(--jp-border); padding-bottom:1rem; margin-bottom:1.5rem;"><?php _e( 'Description & Scope', 'jenga-portal' ); ?></h3>
        <div style="line-height:1.6;">
            <?php echo wpautop( wp_kses_post( $project->post_content ) ); ?>
        </div>
    </div>
</div>
