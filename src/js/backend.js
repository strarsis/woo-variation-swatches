jQuery($ => {
    import('./FVSPluginHelper').then(({FVSPluginHelper}) => {
        FVSPluginHelper.SelectWoo();
        FVSPluginHelper.ColorPicker();
        FVSPluginHelper.FieldDependency();
        FVSPluginHelper.ImageUploader();
    });
});  // end of jquery main wrapper