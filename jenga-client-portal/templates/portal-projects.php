<?php
/**
 * Projects List Template
 */

$current_user = wp_get_current_user();
$projects = get_posts( array(
    'post_type'  => 'jenga_project',
    'meta_query' => array(
        array(
            'key'   => '_jenga_client_id',
            'value' => $current_user->ID,
        )
    ),
    'posts_per_page' => -1
) );
?>
<div class="jenga-portal-wrapper">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h2><?php _e( 'My Projects', 'jenga-portal' ); ?></h2>
        <a href="<?php echo remove_query_arg( 'view' ); ?>" style="color:var(--jp-text-muted);"><?php _e( '&larr; Back to Dashboard', 'jenga-portal' ); ?></a>
    </div>

    <?php if ( $projects ) : ?>
        <div style="display:grid; gap: 1.5rem;">
            <?php foreach ( $projects as $p ) : 
                $status = get_post_meta( $p->ID, '_jenga_status', true );
                $progress = get_post_meta( $p->ID, '_jenga_progress', true ) ?: 0;
                $start = get_post_meta( $p->ID, '_jenga_start_date', true );
                $due = get_post_meta( $p->ID, '_jenga_due_date', true );
                
                $badge_class = 'jp-badge-neutral';
                if ( $status == 'Completed' ) $badge_class = 'jp-badge-success';
                if ( $status == 'In Progress' ) $badge_class = 'jp-badge-info';
                if ( $status == 'Under Review' ) $badge_class = 'jp-badge-warning';
            ?>
                <div class="jp-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <h3 style="margin:0 0 0.5rem 0; font-size:1.25rem;"><a href="?project_id=<?php echo $p->ID; ?>" style="color:#fff; text-decoration:none;"><?php echo esc_html( $p->post_title ); ?></a></h3>
                            <div style="font-size:0.875rem; color:var(--jp-text-muted);">
                                <?php if($start): ?><span><?php _e( 'Started:', 'jenga-portal' ); ?> <?php echo date_i18n( get_option('date_format'), strtotime($start) ); ?></span> &bull; <?php endif; ?>
                                <?php if($due): ?><span><?php _e( 'Due:', 'jenga-portal' ); ?> <?php echo date_i18n( get_option('date_format'), strtotime($due) ); ?></span><?php endif; ?>
                            </div>
                        </div>
                        <span class="jp-badge <?php echo $badge_class; ?>"><?php echo esc_html( $status ); ?></span>
                    </div>
                    
                    <div class="jp-progress-container" style="margin-top:1.5rem;">
                        <div class="jp-progress-bar" data-progress="<?php echo esc_attr( $progress ); ?>"></div>
                    </div>
                    <div style="text-align:right; margin-top:0.5rem; font-size:0.875rem; font-weight:600; color:var(--jp-accent);">
                        <?php echo esc_html( $progress ); ?>% <?php _e( 'Complete', 'jenga-portal' ); ?>
                    </div>
                    
                    <div style="margin-top:1rem; border-top: 1px solid var(--jp-border); padding-top:1rem;">
                        <a href="?project_id=<?php echo $p->ID; ?>" class="jp-btn"><?php _e( 'View Details', 'jenga-portal' ); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="jp-card"><p><?php _e( 'You have no projects assigned.', 'jenga-portal' ); ?></p></div>
    <?php endif; ?>
</div>
