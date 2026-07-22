jQuery(document).ready(function($){
    // Generic File Uploader Helper
    function setupMediaFrame(buttonId, inputId, removeBtnId, previewId, titleText) {
        var frame;

        $(buttonId).click(function(e) {
            e.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: titleText,
                button: {
                    text: 'Select File'
                },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $(inputId).val(attachment.url);
                
                if (previewId && attachment.type === 'image') {
                    $(previewId).attr('src', attachment.url).show();
                } else if (previewId) {
                    $(previewId).hide();
                }

                if (removeBtnId) {
                    $(removeBtnId).show();
                }
            });

            frame.open();
        });

        if (removeBtnId) {
            $(removeBtnId).click(function(e) {
                e.preventDefault();
                $(inputId).val('');
                if (previewId) $(previewId).attr('src', '').hide();
                $(this).hide();
            });
        }
    }

    // Profile Image Uploader
    setupMediaFrame('#jp_upload_image_btn', '#jp_profile_image', '#jp_remove_image_btn', '#jp_profile_image_preview', 'Select Journalist Profile Image');

    // CV / Resume File Uploader
    setupMediaFrame('#jp_upload_cv_btn', '#jp_footer_cv_file, #jp_cv_file', '#jp_remove_cv_btn', null, 'Select CV / Resume Document (PDF)');

    // Media Kit File Uploader
    setupMediaFrame('#jp_upload_mediakit_btn', '#jp_footer_mediakit_file, #jp_media_kit_file', '#jp_remove_mediakit_btn', null, 'Select Media Kit Document (PDF)');
});
