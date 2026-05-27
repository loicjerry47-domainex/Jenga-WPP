jQuery(document).ready(function($) {

    // ── Document meta box: WP media library uploader ─────────────────────────
    var mediaUploader;

    $('#jenga-media-upload-btn').on('click', function(e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: 'Select Document File',
            button: { text: 'Use this file' },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#jenga_file_url').val(attachment.url);
        });

        mediaUploader.open();
    });

});
