<?php
/**
 * Single Ticket Detail Template
 */

$ticket_id = intval( $_GET['ticket_id'] );
$ticket = get_post( $ticket_id );
$current_user = wp_get_current_user();

if ( ! $ticket || $ticket->post_type !== 'jenga_ticket' || get_post_meta( $ticket_id, '_jenga_client_id', true ) != $current_user->ID ) {
    echo '<div class="jenga-notice error"><p>' . __( 'Ticket not found or access denied.', 'jenga-portal' ) . '</p></div>';
    return;
}

// Handle reply submission before rendering
if ( isset( $_POST['submit_reply'] ) && isset( $_POST['ticket_reply_nonce'] ) && wp_verify_nonce( $_POST['ticket_reply_nonce'], 'submit_ticket_reply' ) ) {
    $reply_content = sanitize_textarea_field( $_POST['reply_content'] );
    if ( ! empty( $reply_content ) ) {
        $commentdata = array(
            'comment_post_ID'      => $ticket_id,
            'comment_author'       => $current_user->display_name,
            'comment_author_email' => $current_user->user_email,
            'comment_content'      => $reply_content,
            'comment_type'         => '',
            'comment_parent'       => 0,
            'user_id'              => $current_user->ID,
            'comment_approved'     => 1,
        );
        wp_insert_comment( $commentdata );
        
        // Notify admin about this comment by client
        $settings = get_option( 'jenga_portal_settings' );
        if ( empty( $settings['notify_admin_ticket'] ) || $settings['notify_admin_ticket'] ) {
            $to = get_option( 'admin_email' );
            $subject = sprintf( __( '[%s] New Reply: %s', 'jenga-portal' ), get_bloginfo( 'name' ), $ticket->post_title );
            $message = sprintf( __( '%s has replied to the ticket.', 'jenga-portal' ), $current_user->display_name ) . "\n\n";
            $message .= $reply_content . "\n\n";
            $message .= __( 'View Ticket:', 'jenga-portal' ) . ' ' . admin_url( 'post.php?post=' . $ticket_id . '&action=edit' );
            wp_mail( $to, $subject, $message );
        }
        
        // Refresh page to show comment without resubmission
        echo '<script>window.location.href="' . esc_url( add_query_arg( 'ticket_id', $ticket_id ) ) . '";</script>';
        exit;
    }
}

$status = get_post_meta( $ticket_id, '_jenga_ticket_status', true );
$priority = get_post_meta( $ticket_id, '_jenga_priority', true );
$project_id = get_post_meta( $ticket_id, '_jenga_project_id', true );

$badge_class = 'jp-badge-neutral';
if ( $status == 'Open' || $status == 'In Progress' ) $badge_class = 'jp-badge-info';
if ( $status == 'Awaiting Reply' ) $badge_class = 'jp-badge-warning';
if ( $status == 'Resolved' || $status == 'Closed' ) $badge_class = 'jp-badge-success';

$pri_class = 'jp-badge-neutral';
if ( $priority == 'High' ) $pri_class = 'jp-badge-warning';
if ( $priority == 'Urgent' ) $pri_class = 'jp-badge-danger';
?>
<div class="jenga-portal-wrapper">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <h2 style="margin:0 0 0.5rem 0;"><?php echo esc_html( $ticket->post_title ); ?></h2>
            <div style="font-size:0.875rem; color:var(--jp-text-muted);">
                <?php echo get_the_date( 'F j, Y g:i a', $ticket->ID ); ?>
            </div>
        </div>
        <a href="<?php echo remove_query_arg( 'ticket_id' ); ?>" style="color:var(--jp-text-muted);"><?php _e( '&larr; Back to Tickets', 'jenga-portal' ); ?></a>
    </div>

    <div style="display:flex; gap:1rem; margin-bottom:2rem;">
        <span class="jp-badge <?php echo $badge_class; ?>"><?php echo esc_html( $status ); ?></span>
        <span class="jp-badge <?php echo $pri_class; ?>"><?php echo esc_html( $priority ); ?> Priority</span>
        <?php if ( $project_id ) : ?>
            <span class="jp-badge jp-badge-neutral"><?php _e( 'Project:', 'jenga-portal' ); ?> <?php echo get_the_title( $project_id ); ?></span>
        <?php endif; ?>
    </div>

    <div class="jp-card" style="margin-bottom: 2rem;">
        <div style="display:flex; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <img src="<?php echo get_avatar_url( $ticket->post_author, array( 'size' => 48 ) ); ?>" alt="" style="border-radius:50%; width:48px; height:48px;">
            </div>
            <div>
                <h4 style="margin:0 0 0.25rem 0;"><?php echo esc_html( get_user_by('id', $ticket->post_author)->display_name ); ?> <span style="font-size:0.75rem; color:var(--jp-text-muted); font-weight:normal; margin-left:0.5rem;">(You)</span></h4>
                <div style="font-size:0.75rem; color:var(--jp-text-muted);"><?php echo get_the_date( 'F j, Y g:i a', $ticket->ID ); ?></div>
            </div>
        </div>
        <div style="line-height:1.6;">
            <?php echo wpautop( wp_kses_post( $ticket->post_content ) ); ?>
        </div>
    </div>

    <h3 style="margin-bottom:1.5rem;"><?php _e( 'Discussion', 'jenga-portal' ); ?></h3>

    <?php
    $comments = get_comments( array(
        'post_id' => $ticket_id,
        'order'   => 'ASC'
    ) );

    if ( $comments ) :
        foreach ( $comments as $comment ) : 
            $is_admin = user_can( $comment->user_id, 'manage_options' );
            $author_name = $is_admin ? __( 'Support Agent', 'jenga-portal' ) : get_user_by('id', $comment->user_id)->display_name;
            $bg_color = $is_admin ? 'background: rgba(201, 164, 74, 0.05); border: 1px solid rgba(201, 164, 74, 0.2);' : '';
    ?>
        <div class="jp-card" style="margin-bottom: 1.5rem; <?php echo $bg_color; ?>">
            <div style="display:flex; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <img src="<?php echo get_avatar_url( $comment->user_id, array( 'size' => 48 ) ); ?>" alt="" style="border-radius:50%; width:48px; height:48px;">
                </div>
                <div>
                    <h4 style="margin:0 0 0.25rem 0;"><?php echo esc_html( $author_name ); ?> <?php if($is_admin): ?><span class="jp-badge jp-badge-warning" style="font-size:0.6rem; padding:0.1rem 0.4rem; margin-left:0.5rem;">Admin</span><?php endif; ?></h4>
                    <div style="font-size:0.75rem; color:var(--jp-text-muted);"><?php echo comment_date( 'F j, Y g:i a', $comment ); ?></div>
                </div>
            </div>
            <div style="line-height:1.6;">
                <?php echo wpautop( wp_kses_post( $comment->comment_content ) ); ?>
            </div>
        </div>
    <?php 
        endforeach;
    endif; 
    ?>

    <?php if ( $status !== 'Closed' && $status !== 'Resolved' ) : ?>
        <div class="jp-card" style="margin-top:2rem;">
            <h4 style="margin-top:0; margin-bottom:1rem;"><?php _e( 'Write a Reply', 'jenga-portal' ); ?></h4>
            <form method="post" action="">
                <?php wp_nonce_field( 'submit_ticket_reply', 'ticket_reply_nonce' ); ?>
                <div class="jp-form-group">
                    <textarea name="reply_content" rows="4" required></textarea>
                </div>
                <button type="submit" name="submit_reply" class="jp-btn"><?php _e( 'Send Reply', 'jenga-portal' ); ?></button>
            </form>
        </div>
    <?php else : ?>
        <div class="jenga-notice info" style="background:var(--jp-card-bg); border:1px solid var(--jp-border); padding:1rem; border-radius:4px; text-align:center;">
            <p style="margin:0;"><?php _e( 'This ticket is closed. If you need further assistance, please open a new ticket.', 'jenga-portal' ); ?></p>
        </div>
    <?php endif; ?>

</div>
