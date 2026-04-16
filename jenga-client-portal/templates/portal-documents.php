<?php
/**
 * Documents List Template
 */

$current_user = wp_get_current_user();
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
?>
<div class="jenga-portal-wrapper">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h2><?php _e( 'My Documents', 'jenga-portal' ); ?></h2>
        <a href="<?php echo remove_query_arg( 'view' ); ?>" style="color:var(--jp-text-muted);"><?php _e( '&larr; Back to Dashboard', 'jenga-portal' ); ?></a>
    </div>

    <div class="jp-card" style="padding:0; overflow:hidden;">
        <table class="jp-table">
            <thead>
                <tr>
                    <th><?php _e( 'Document Name', 'jenga-portal' ); ?></th>
                    <th><?php _e( 'Type', 'jenga-portal' ); ?></th>
                    <th><?php _e( 'Project', 'jenga-portal' ); ?></th>
                    <th><?php _e( 'Date', 'jenga-portal' ); ?></th>
                    <th style="text-align:right;"><?php _e( 'Action', 'jenga-portal' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( $documents ) : ?>
                    <?php foreach ( $documents as $doc ) : 
                        $type = get_post_meta( $doc->ID, '_jenga_doc_type', true );
                        $url = get_post_meta( $doc->ID, '_jenga_file_url', true );
                        $project_id = get_post_meta( $doc->ID, '_jenga_project_id', true );
                        $project_title = $project_id ? get_the_title( $project_id ) : '-';
                    ?>
                        <tr>
                            <td style="font-weight:500; color:#fff;"><?php echo esc_html( $doc->post_title ); ?></td>
                            <td><span class="jp-badge jp-badge-neutral"><?php echo esc_html( $type ); ?></span></td>
                            <td><?php echo esc_html( $project_title ); ?></td>
                            <td><?php echo get_the_date( '', $doc->ID ); ?></td>
                            <td style="text-align:right;">
                                <?php if ( $url ) : ?>
                                    <a href="<?php echo esc_url( $url ); ?>" target="_blank" class="jp-btn" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;"><?php _e( 'Download', 'jenga-portal' ); ?></a>
                                <?php else : ?>
                                    <span style="color:var(--jp-text-muted); font-size:0.875rem;"><?php _e( 'Not available', 'jenga-portal' ); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:2rem;"><?php _e( 'No documents available.', 'jenga-portal' ); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
