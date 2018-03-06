// ================================================================
// WooCommerce Variation Change
// ================================================================

const WooVariationSwatches = (($) => {

    const Default = {};

    class WooVariationSwatches {

        constructor(element, config) {

            // Assign
            this._element           = $(element);
            this._config            = $.extend({}, Default, config);
            this._generated         = {};
            this.product_variations = this._element.data('product_variations');
            this.is_ajax_variation  = !this.product_variations;

            // Call
            this.init(this.is_ajax_variation);
            this.loaded(this.is_ajax_variation);
            this.update(this.is_ajax_variation);
            this.reset(this.is_ajax_variation);

            $(document).trigger('woo_variation_swatches', [this._element]);
        }

        static _jQueryInterface(config) {
            return this.each(function () {
                new WooVariationSwatches(this, config)
            })
        }

        init(is_ajax) {

            this._element.find('ul.variable-items-wrapper').each(function (i, el) {

                let select = $(this).siblings('select.woo-variation-raw-select');

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

            _.delay(() => {
                this._element.trigger('woo_variation_swatches_init', [this, this.product_variations])
                $(document).trigger('woo_variation_swatches_loaded', [this._element, this.product_variations])
            }, 1)
        }

        loaded(is_ajax) {
            if (!is_ajax) {
                this._element.on('woo_variation_swatches_init', function (event, object, product_variations) {

                    object._generated = product_variations.reduce((obj, variation) => {
                        Object.keys(variation.attributes).map((attribute_name) => {

                            if (!obj[attribute_name]) {
                                obj[attribute_name] = []
                            }

                            if (variation.attributes[attribute_name]) {
                                obj[attribute_name].push(variation.attributes[attribute_name])
                            }
                        });

                        return obj;
                    }, {});

                    $(this).find('ul.variable-items-wrapper').each(function () {
                        let li               = $(this).find('li');
                        let attribute        = $(this).data('attribute_name');
                        let attribute_values = object._generated[attribute];

                        li.each(function () {
                            let attribute_value = $(this).attr('data-value');

                            if (!_.isEmpty(attribute_values) && !attribute_values.includes(attribute_value)) {
                                $(this).removeClass('selected');
                                $(this).addClass('disabled');
                            }
                        });
                    });
                });
            }
        }

        reset(is_ajax) {
            this._element.on('reset_data', function (event) {
                let isAjaxVariation = !$(this).data('product_variations');
                $(this).find('ul.variable-items-wrapper').each(function () {
                    let li = $(this).find('li');
                    li.each(function () {
                        if (!is_ajax) {
                            $(this).removeClass('selected disabled');
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
                            options  = $(this).siblings('select.woo-variation-raw-select').find('option'),
                            current  = $(this).siblings('select.woo-variation-raw-select').find('option:selected'),
                            eq       = $(this).siblings('select.woo-variation-raw-select').find('option').eq(1),
                            li       = $(this).find('li'),
                            selects  = [];

                        // For Avada FIX
                        if (options.length < 1) {
                            options = $(this).parent().find('select.woo-variation-raw-select').find('option');
                            current = $(this).parent().find('select.woo-variation-raw-select').find('option:selected');
                            eq      = $(this).parent().find('select.woo-variation-raw-select').find('option').eq(1);
                        }

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
                        options  = $(this).siblings('select.woo-variation-raw-select').find('option'),
                        current  = $(this).siblings('select.woo-variation-raw-select').find('option:selected'),
                        eq       = $(this).siblings('select.woo-variation-raw-select').find('option').eq(1),
                        li       = $(this).find('li'),
                        selects  = [];

                    // For Avada FIX
                    if (options.length < 1) {
                        options = $(this).parent().find('select.woo-variation-raw-select').find('option');
                        current = $(this).parent().find('select.woo-variation-raw-select').find('option:selected');
                        eq      = $(this).parent().find('select.woo-variation-raw-select').find('option').eq(1);
                    }

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