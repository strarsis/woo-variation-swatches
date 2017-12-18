jQuery($ => {
    import('./CustomVariationSelect').then(() => {
        // Init on Ajax Popup :)
        $(document).on('wc_variation_form', '.variations_form', function () {
            $(this).CustomVariationSelect();
        });
    });
});  // end of jquery main wrapper