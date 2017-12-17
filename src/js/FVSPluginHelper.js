/*global FVSPluginObject, wp*/

const FVSPluginHelper = (($) => {
    class FVSPluginHelper {

        static ImageUploader() {
            $(document).off('click', 'button.fvs_upload_image_button');
            $(document).on('click', 'button.fvs_upload_image_button', this.AddImage);
            $(document).on('click', 'button.fvs_remove_image_button', this.RemoveImage);
        }

        static AddImage(event) {

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

                    if ($.trim(attachment.id) !== '') {

                        let url = ( typeof(attachment.sizes.thumbnail) === 'undefined' ) ? attachment.sizes.full.url : attachment.sizes.thumbnail.url;

                        $(this).prev().val(attachment.id);
                        $(this).closest('.meta-image-field-wrapper').find('img').attr('src', url);
                        $(this).next().show();
                    }
                    //file_frame.close();
                });

                // When open select selected
                file_frame.on('open', () => {

                    // Grab our attachment selection and construct a JSON representation of the model.
                    let selection  = file_frame.state().get('selection');
                    let current    = $(this).prev().val();
                    let attachment = wp.media.attachment(current);
                    attachment.fetch();
                    selection.add(attachment ? [attachment] : []);
                });

                // Finally, open the modal.
                file_frame.open();
            }
        }

        static RemoveImage(event) {

            event.preventDefault();
            event.stopPropagation();

            let placeholder = $(this).closest('.meta-image-field-wrapper').find('img').data('placeholder');
            $(this).closest('.meta-image-field-wrapper').find('img').attr('src', placeholder);
            $(this).prev().prev().val('');
            $(this).hide();
            return false;
        }

        static SelectWoo(selector = 'select.fvs-selectwoo') {
            $(selector).selectWoo({
                allowClear : true
            });
        }

        static ColorPicker(selector = 'input.fvs-color-picker') {
            $(selector).wpColorPicker();
        }

        static FieldDependency(selector = '[data-depends]') {
            $(selector).FormFieldDependency();
        }
    }

    return FVSPluginHelper;
})(jQuery);

export { FVSPluginHelper };