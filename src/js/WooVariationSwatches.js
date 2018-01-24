// ================================================================
// WooCommerce Variation Change
// ================================================================

// decrease `woocommerce_ajax_variation_threshold` filter value for ajax testing

const WooVariationSwatches = (($) => {

    const Default = {};

    class WooVariationSwatches {

        constructor(element, config) {

            // Assign
            this._element          = $(element);
            this._config           = $.extend({}, Default, config);
            this._generated        = {};
            this.is_ajax_variation = !this._element.data('product_variations');

            // Call
            this.init();
            this.update(this.is_ajax_variation);
            this.reset(this.is_ajax_variation);

            $(document).trigger('woo_variation_swatches', [this._element]);
        }

        static _jQueryInterface(config) {
            return this.each(function () {
                new WooVariationSwatches(this, config)
            })
        }

        init() {
            this._element.find('ul.variable-items-wrapper').each(function (i, el) {

                let select = $(this).prev('select');

                $(this).on('click', 'li', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    let value = $(this).data('value');
                    select.val(value).trigger('change');
                    select.trigger('click');
                    select.trigger('focusin');
                    select.trigger('touchstart');
                });
            });
        }

        reset(is_ajax) {
            this._element.on('reset_data', function (event) {
                let isAjaxVariation = !$(this).data('product_variations');
                $(this).find('ul.variable-items-wrapper').each(function () {
                    let li = $(this).find('li');
                    li.each(function () {
                        if (!is_ajax) {
                            $(this).removeClass('selected');
                            $(this).removeClass('disabled');
                        }
                    });
                });
            });
        }

        update(is_ajax) {
            this._element.on('woocommerce_variation_has_changed', function (event) {
                if (is_ajax) {
                    $(this).find('ul.variable-items-wrapper').each(function () {
                        let selected = '',
                            options  = $(this).prev('select').find('option'),
                            current  = $(this).prev('select').find('option:selected'),
                            eq       = $(this).prev('select').find('option').eq(1),
                            li       = $(this).find('li'),
                            selects  = [];

                        options.each(function () {
                            if ($(this).val() !== '') {
                                selects.push($(this).val());
                                selected = current ? current.val() : eq.val();
                            }
                        });
                        _.delay(function () {
                            li.each(function () {
                                let value = $(this).attr('data-value');
                                $(this).removeClass('selected disabled');
                                if (value === selected) {
                                    $(this).addClass('selected');
                                }
                            });
                        }, 1);
                    });
                }
            });

            // WithOut Ajax Update
            this._element.on('woocommerce_update_variation_values', function (event) {
                $(this).find('ul.variable-items-wrapper').each(function () {

                    let selected = '',
                        options  = $(this).prev('select').find('option'),
                        current  = $(this).prev('select').find('option:selected'),
                        eq       = $(this).prev('select').find('option').eq(1),
                        li       = $(this).find('li'),
                        selects  = [];

                    options.each(function () {
                        if ($(this).val() !== '') {
                            selects.push($(this).val());
                            selected = current ? current.val() : eq.val();
                        }
                    });

                    _.delay(function () {
                        li.each(function () {
                            let value = $(this).attr('data-value');
                            $(this).removeClass('selected disabled');
                            if (_.contains(selects, value)) {
                                $(this).removeClass('disabled');
                                if (value === selected) {
                                    $(this).addClass('selected');
                                }
                            }
                            else {
                                $(this).addClass('disabled');
                            }
                        });
                    }, 1);
                });
            });
        }
    }

    /**
     * ------------------------------------------------------------------------
     * Data Api implementation
     * ------------------------------------------------------------------------
     */

    /**
     * ------------------------------------------------------------------------
     * jQuery
     * ------------------------------------------------------------------------
     */

    $.fn['WooVariationSwatches'] = WooVariationSwatches._jQueryInterface;
    $.fn['WooVariationSwatches'].Constructor = WooVariationSwatches;
    $.fn['WooVariationSwatches'].noConflict  = function () {
        $.fn['WooVariationSwatches'] = $.fn['WooVariationSwatches'];
        return WooVariationSwatches._jQueryInterface
    }

    return WooVariationSwatches;

})(jQuery);

export default WooVariationSwatches