jQuery($ => {
    import('./PluginHelper').then(({PluginHelper}) => {
        PluginHelper.SelectWoo();
        PluginHelper.ColorPicker();
        PluginHelper.FieldDependency();
        PluginHelper.ImageUploader();
    });
});  // end of jquery main wrapper