/*global WVSPluginObject, wp, woocommerce_admin_meta_boxes*/

const PluginHelper = (($) => {
    class PluginHelper {

        static ImageUploader() {
            $(document).off('click', 'button.wvs_upload_image_button');
            $(document).on('click', 'button.wvs_upload_image_button', this.AddImage);
            $(document).on('click', 'button.wvs_remove_image_button', this.RemoveImage);
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
                    title    : WVSPluginObject.media_title,
                    button   : {
                        text : WVSPluginObject.button_title
                    },
                    multiple : false,
                });

                // When an image is selected, run a callback.
                file_frame.on('select', () => {
                    let attachment = file_frame.state().get('selection').first().toJSON();

                    if ($.trim(attachment.id) !== '') {

                        let url = (typeof(attachment.sizes.thumbnail) === 'undefined') ? attachment.sizes.full.url : attachment.sizes.thumbnail.url;

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

        static SelectWoo(selector = 'select.wvs-selectwoo') {
            if ($().selectWoo) {
                $(selector).selectWoo({
                    allowClear : true
                });
            }
        }

        static ColorPicker(selector = 'input.wvs-color-picker') {
            if ($().wpColorPicker) {
                $(selector).wpColorPicker();
            }
        }

        static FieldDependency(selector = '[data-depends]') {
            if ($().FormFieldDependency) {
                $(selector).FormFieldDependency();
            }
        }

        static savingDialog($wrapper, $dialog, taxonomy) {

            let data = {};
            let term = '';

            // @TODO: We should use form data, because we have to pick array based data also :)

            $dialog.find(`input, select`).each(function () {
                let key   = $(this).attr('name');
                let value = $(this).val();
                if (key) {
                    if (key === 'tag_name') {
                        term = value
                    }
                    else {
                        data[key] = value
                    }
                    $(this).val('')
                }
            });

            if (term) {
                $('.product_attributes').block({
                    message    : null,
                    overlayCSS : {
                        background : '#fff',
                        opacity    : 0.6
                    }
                });

                let ajax_data = {
                    action   : 'woocommerce_add_new_attribute',
                    taxonomy : taxonomy,
                    term     : term,
                    security : woocommerce_admin_meta_boxes.add_attribute_nonce,
                    ...data
                };

                $.post(woocommerce_admin_meta_boxes.ajax_url, ajax_data, function (response) {

                    if (response.error) {
                        // Error.
                        window.alert(response.error);
                    }
                    else if (response.slug) {
                        // Success.
                        $wrapper.find('select.attribute_values').append('<option value="' + response.term_id + '" selected="selected">' + response.name + '</option>');
                        $wrapper.find('select.attribute_values').change();
                    }

                    $('.product_attributes').unblock();
                });
            }
            else {
                $('.product_attributes').unblock();
            }
        }

        static AttributeDialog() {

            let self = this;
            $('.product_attributes').on('click', 'button.wvs_add_new_attribute', function (event) {

                event.preventDefault();

                let $wrapper  = $(this).closest('.woocommerce_attribute');
                let attribute = $wrapper.data('taxonomy');
                let title     = $(this).data('dialog_title');

                $('.wvs-attribute-dialog-for-' + attribute).dialog({
                    title         : '',
                    dialogClass   : 'wp-dialog wvs-attribute-dialog',
                    classes       : {
                        "ui-dialog" : "wp-dialog wvs-attribute-dialog"
                    },
                    autoOpen      : false,
                    draggable     : true,
                    width         : 'auto',
                    modal         : true,
                    resizable     : false,
                    closeOnEscape : true,
                    position      : {
                        my : "center",
                        at : "center",
                        of : window
                    },
                    open          : function () {
                        // close dialog by clicking the overlay behind it
                        $('.ui-widget-overlay').bind('click', function () {
                            $('#attribute-dialog').dialog('close');
                        })
                    },
                    create        : function () {
                        // style fix for WordPress admin
                        // $('.ui-dialog-titlebar-close').addClass('ui-button');
                    }
                })
                    .dialog("option", "title", title)
                    .dialog("option", "buttons",
                        [
                            {
                                text  : WVSPluginObject.dialog_save,
                                click : function () {
                                    self.savingDialog($wrapper, $(this), attribute);
                                    $(this).dialog("close").dialog("destroy");
                                }
                            },
                            {
                                text  : WVSPluginObject.dialog_cancel,
                                click : function () {
                                    $(this).dialog("close").dialog("destroy");
                                }
                            }
                        ]
                    )
                    .dialog('open')
            });
        }
    }

    return PluginHelper;
})(jQuery);

export { PluginHelper };