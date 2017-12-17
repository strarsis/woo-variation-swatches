// ================================================================
// WooCommerce Variation Change
// ================================================================

const FVSCustomVariationInput = (($) => {

    const Default = {};

    class FVSCustomVariationInput {

        constructor(element, config) {

            // Assign
            this._element = $(element);
            this._config  = $.extend({}, Default, config);

            // Call
            //this.addInputMarkup();
            this.wrapperAction();
            this.resetDataAction();
        }

        static _jQueryInterface(config) {
            return this.each(function () {
                new FVSCustomVariationInput(this, config)
            })
        }

        wrapperAction() {
            this._element.find('ul.variable-items-wrapper').each(function (i, el) {

                let select = $(this).prev('select'),
                    li     = $(this).find('li');

                $(this).on('click', 'li:not(.selected)', function () {
                    let value = $(this).data('value');
                    li.removeClass('selected');
                    select.val(value).trigger('change');
                    $(this).addClass('selected');
                });

                $(this).on('click', 'li.selected', function () {
                    li.removeClass('selected');
                    select.val('').trigger('change');
                    select.trigger('click');
                    select.trigger('focusin');
                    select.trigger('touchstart');
                });

            });
        }

        resetDataAction() {
            this._element.on('reset_data', function (event) {
                $(this).find('ul.variable-items-wrapper').each(function () {

                    let li = $(this).find('li');
                    li.each(function () {
                        $(this).removeClass('selected');
                        $(this).removeClass('disabled');
                    });
                });
            });
        }

        updateVariationValueAction() {
            this._element.on('woocommerce_update_variation_values', function (event) {
                $(this).find('ul.variable-items-wrapper').each(function () {

                    let selected = '',
                        options  = $(this).prev('select').find('option'),
                        current  = $(this).prev('select').find('option:selected'),
                        eq       = $(this).prev('select').find('option').eq(1),
                        li       = $(this).find('li'),
                        selects  = [];

                    options.each(function () {
                        if ($(this).val() != '') {
                            selects.push($(this).val());
                            selected = current ? current.val() : eq.val();
                        }
                    });

                    _.delay(function () {
                        li.each(function () {
                            let value = $(this).data('value');
                            $(this).removeClass('selected disabled');

                            if (_.contains(selects, value)) {
                                $(this).removeClass('disabled');

                                if (value == selected) {
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

    $.fn['FVSCustomVariationInput'] = FVSCustomVariationInput._jQueryInterface;
    $.fn['FVSCustomVariationInput'].Constructor = FVSCustomVariationInput;
    $.fn['FVSCustomVariationInput'].noConflict  = function () {
        $.fn['FVSCustomVariationInput'] = $.fn['FVSCustomVariationInput'];
        return FVSCustomVariationInput._jQueryInterface
    }

    return FVSCustomVariationInput;

})(jQuery);

export default FVSCustomVariationInput