/*global FVSPluginObject, wp*/

class FVSPluginHelper {

    static MediaUploader(event) {

        event.preventDefault();
        event.stopPropagation();

        let file_frame;

        if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {

            // If the media frame already exists, reopen it.
            if (file_frame) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.select_image = wp.media({
                title    : FVSPluginObject.media_title,
                button   : {
                    text : FVSPluginObject.button_title
                },
                multiple : false,
            });

            // When an image is selected, run a callback.
            file_frame.on('select', () => {
                let attachment = file_frame.state().get('selection').first().toJSON();

                if (jQuery.trim(attachment.id) !== '') {

                    let url = ( typeof(attachment.sizes.thumbnail) === 'undefined' ) ? attachment.sizes.full.url : attachment.sizes.thumbnail.url;

                    jQuery(this).prev().val(attachment.id);
                    jQuery(this).closest('.meta-image-field-wrapper').find('img').attr('src', url);
                    jQuery(this).next().show();
                }
                //file_frame.close();
            });

            // When open select selected
            file_frame.on('open', () => {

                // Grab our attachment selection and construct a JSON representation of the model.
                let selection  = file_frame.state().get('selection');
                let current    = jQuery(this).prev().val();
                let attachment = wp.media.attachment(current);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            });

            // Finally, open the modal.
            file_frame.open();
        }
    }

    static RemoveImage(event) {

        let placeholder = jQuery(this).closest('.meta-image-field-wrapper').find('img').data('placeholder');
        jQuery(this).closest('.meta-image-field-wrapper').find('img').attr('src', placeholder);
        jQuery(this).prev().prev().val('');
        jQuery(this).hide();
        return false;
    }

    static SelectWoo(selector = 'select.fvs-selectwoo') {
        jQuery(selector).selectWoo({
            allowClear : true
        });
    }

    static ColorPicker(selector = 'input.fvs-color-picker') {
        jQuery(selector).wpColorPicker();
    }

    static FieldDependency(selector = '[data-depends]') {
        jQuery(selector).FormFieldDependency();
    }
}

export default FVSPluginHelper;