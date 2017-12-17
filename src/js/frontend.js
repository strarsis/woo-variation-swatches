jQuery($ => {
    import('./FVSCustomVariationInput').then(() => {
        $('.variations_form').FVSCustomVariationInput();

        // Init on Ajax Popup :)
        $(document).on('wc_variation_form', '.variations_form', function () {
            $(this).FVSCustomVariationInput();
        });
    });
});  // end of jquery main wrapper