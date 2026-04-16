jQuery(document).ready(function($) {
    $('#jp-ticket-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var $msg = $('#jp-ticket-msg');
        
        var title = $('#ticket_title').val();
        var desc = $('#ticket_desc').val();
        
        if(title.length < 3 || desc.length < 10) {
            $msg.html('<div class="jp-badge jp-badge-danger" style="margin-bottom:1rem;display:block;">Please complete all required fields. Description needs minimum 10 chars.</div>');
            return;
        }

        $btn.prop('disabled', true).text('Submitting...');
        $msg.html('');
        
        var data = {
            action: 'jenga_submit_ticket',
            nonce: jenga_portal_ajax.nonce,
            title: title,
            category: $('#ticket_category').val(),
            project_id: $('#ticket_project').val(),
            priority: $('#ticket_priority').val(),
            description: desc
        };
        
        $.post(jenga_portal_ajax.ajax_url, data, function(response) {
            if(response.success) {
                $msg.html('<div class="jp-badge jp-badge-success" style="margin-bottom:1rem;display:block;">' + response.data.message + '</div>');
                $form[0].reset();
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                $msg.html('<div class="jp-badge jp-badge-danger" style="margin-bottom:1rem;display:block;">' + response.data.message + '</div>');
                $btn.prop('disabled', false).text('Submit Ticket');
            }
        }).fail(function() {
            $msg.html('<div class="jp-badge jp-badge-danger" style="margin-bottom:1rem;display:block;">A server error occurred.</div>');
            $btn.prop('disabled', false).text('Submit Ticket');
        });
    });
});
