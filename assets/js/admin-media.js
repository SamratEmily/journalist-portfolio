jQuery(document).ready(function($){
    // Generic File Uploader Helper
    function setupMediaFrame(buttonSelector, inputSelector, removeBtnSelector, previewSelector, titleText) {
        $(document).on('click', buttonSelector, function(e) {
            e.preventDefault();

            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                alert('WordPress Media Library is loading or unavailable. Please refresh the page.');
                return;
            }

            var frame = wp.media({
                title: titleText || 'Select File',
                button: {
                    text: 'Select File'
                },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                if (attachment && attachment.url) {
                    $(inputSelector).val(attachment.url).trigger('change');
                    if (previewSelector && attachment.type === 'image') {
                        $(previewSelector).attr('src', attachment.url).css('display', 'block').show();
                    } else if (previewSelector) {
                        $(previewSelector).hide();
                    }
                    if (removeBtnSelector) {
                        $(removeBtnSelector).show();
                    }
                }
            });

            frame.open();
        });

        if (removeBtnSelector) {
            $(document).on('click', removeBtnSelector, function(e) {
                e.preventDefault();
                $(inputSelector).val('');
                if (previewSelector) $(previewSelector).attr('src', '').hide();
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

    // Universal Upload Button Handler (.jp-upload-btn)
    $(document).on('click', '.jp-upload-btn', function(e) {
        e.preventDefault();

        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            alert('WordPress Media Library is loading or unavailable. Please refresh the page.');
            return;
        }

        var $btn = $(this);
        var $parent = $btn.closest('div, p, td, tr, .jp-field-group');
        var $input = $btn.siblings('input').first();
        if (!$input.length) {
            $input = $parent.find('input[type="text"], input[type="url"], input.jp-media-url').first();
        }
        var $preview = $parent.find('.jp-thumb-preview, img').first();

        var frame = wp.media({
            title: 'Select or Upload Thumbnail Image',
            button: {
                text: 'Use Selected Image'
            },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            if (attachment && attachment.url) {
                $input.val(attachment.url).trigger('change');
                if ($preview.length) {
                    $preview.attr('src', attachment.url).css('display', 'block').show();
                }
            }
        });

        frame.open();
    });
});
