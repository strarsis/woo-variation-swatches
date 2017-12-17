jQuery($ => {
    import('./FVSCustomVariationInput').then(() => {
        // Init on Ajax Popup :)
        $(document).on('wc_variation_form', '.variations_form', function () {
            $(this).FVSCustomVariationInput();
        });
    });
});  // end of jquery main wrapper