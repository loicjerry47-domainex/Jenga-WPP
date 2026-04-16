jQuery(document).ready(function($) {
    // Animate progress bars
    $('.jp-progress-bar').each(function() {
        var width = $(this).data('progress');
        $(this).css('width', width + '%');
    });
});
