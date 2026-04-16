<?php
/**
 * Tickets List & Submission Template
 */

$current_user = wp_get_current_user();
$tickets = get_posts( array(
    'post_type'  => 'jenga_ticket',
    'meta_query' => array(
        array(
            'key'   => '_jenga_client_id',
            'value' => $current_user->ID,
        )
    ),
    'posts_per_page' => -1
) );

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
        <h2><?php _e( 'Support Tickets', 'jenga-portal' ); ?></h2>
        <a href="<?php echo remove_query_arg( 'view' ); ?>" style="color:var(--jp-text-muted);"><?php _e( '&larr; Back to Dashboard', 'jenga-portal' ); ?></a>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <h3><?php _e( 'Your Tickets', 'jenga-portal' ); ?></h3>
            <?php if ( $tickets ) : ?>
                <div style="display:grid; gap: 1rem;">
                    <?php foreach ( $tickets as $t ) : 
                        $status = get_post_meta( $t->ID, '_jenga_ticket_status', true );
                        $priority = get_post_meta( $t->ID, '_jenga_priority', true );
                        
                        $badge_class = 'jp-badge-neutral';
                        if ( $status == 'Open' || $status == 'In Progress' ) $badge_class = 'jp-badge-info';
                        if ( $status == 'Awaiting Reply' ) $badge_class = 'jp-badge-warning';
                        if ( $status == 'Resolved' || $status == 'Closed' ) $badge_class = 'jp-badge-success';

                        $pri_class = 'jp-badge-neutral';
                        if ( $priority == 'High' ) $pri_class = 'jp-badge-warning';
                        if ( $priority == 'Urgent' ) $pri_class = 'jp-badge-danger';
                    ?>
                        <div class="jp-card" style="padding:1rem;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div>
                                    <h4 style="margin:0 0 0.25rem 0;"><a href="?ticket_id=<?php echo $t->ID; ?>" style="color:#fff; text-decoration:none;"><?php echo esc_html( $t->post_title ); ?></a></h4>
                                    <div style="font-size:0.75rem; color:var(--jp-text-muted);">
                                        <?php echo get_the_date( '', $t->ID ); ?>
                                    </div>
                                </div>
                                <div style="display:flex; gap:0.5rem;">
                                    <span class="jp-badge <?php echo $pri_class; ?>"><?php echo esc_html( $priority ); ?></span>
                                    <span class="jp-badge <?php echo $badge_class; ?>"><?php echo esc_html( $status ); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="jp-card"><p style="margin:0;"><?php _e( 'You have no support tickets.', 'jenga-portal' ); ?></p></div>
            <?php endif; ?>
        </div>

        <div id="new">
            <div class="jp-card">
                <h3 style="margin-top:0; border-bottom: 1px solid var(--jp-border); padding-bottom: 1rem; margin-bottom: 1.5rem;"><?php _e( 'Submit a New Ticket', 'jenga-portal' ); ?></h3>
                
                <div id="jp-ticket-msg"></div>

                <form id="jp-ticket-form">
                    <div class="jp-form-group">
                        <label for="ticket_title"><?php _e( 'Ticket Subject *', 'jenga-portal' ); ?></label>
                        <input type="text" id="ticket_title" name="title" required>
                    </div>

                    <?php $terms = get_terms( array( 'taxonomy' => 'ticket_category', 'hide_empty' => false ) ); ?>
                    <?php if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) : ?>
                    <div class="jp-form-group">
                        <label for="ticket_category"><?php _e( 'Category', 'jenga-portal' ); ?></label>
                        <select id="ticket_category" name="category">
                            <option value="0"><?php _e( 'Select a Category', 'jenga-portal' ); ?></option>
                            <?php foreach ( $terms as $term ) : ?>
                                <option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="jp-form-group">
                        <label for="ticket_project"><?php _e( 'Related Project', 'jenga-portal' ); ?></label>
                        <select id="ticket_project" name="project_id">
                            <option value="0"><?php _e( 'General / None', 'jenga-portal' ); ?></option>
                            <?php foreach ( $projects as $proj ) : ?>
                                <option value="<?php echo esc_attr( $proj->ID ); ?>"><?php echo esc_html( $proj->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="jp-form-group">
                        <label for="ticket_priority"><?php _e( 'Priority', 'jenga-portal' ); ?></label>
                        <select id="ticket_priority" name="priority">
                            <option value="Low"><?php _e( 'Low', 'jenga-portal' ); ?></option>
                            <option value="Medium" selected><?php _e( 'Medium', 'jenga-portal' ); ?></option>
                            <option value="High"><?php _e( 'High', 'jenga-portal' ); ?></option>
                            <option value="Urgent"><?php _e( 'Urgent', 'jenga-portal' ); ?></option>
                        </select>
                    </div>

                    <div class="jp-form-group">
                        <label for="ticket_desc"><?php _e( 'Description *', 'jenga-portal' ); ?></label>
                        <textarea id="ticket_desc" name="description" rows="6" required></textarea>
                    </div>

                    <button type="submit" class="jp-btn"><?php _e( 'Submit Ticket', 'jenga-portal' ); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
