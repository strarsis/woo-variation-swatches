jQuery($ => {
    import('./FVSPluginHelper').then((Helper) => {

        const FVSPluginHelper = Helper.default;

        FVSPluginHelper.SelectWoo();
        FVSPluginHelper.ColorPicker();
        FVSPluginHelper.FieldDependency();

        $(document).off('click', 'button.fvs_upload_image_button');
        $(document).on('click', 'button.fvs_upload_image_button', FVSPluginHelper.MediaUploader);
        $(document).on('click', 'button.fvs_remove_image_button', FVSPluginHelper.RemoveImage);
    });
});  // end of jquery main wrapper