jQuery($ => {
    import('./WooVariationSwatches').then(() => {
        // Init on Ajax Popup :)
        $(document).on('wc_variation_form', '.variations_form', function () {
            $(this).WooVariationSwatches();
        });
    });
});  // end of jquery main wrapper