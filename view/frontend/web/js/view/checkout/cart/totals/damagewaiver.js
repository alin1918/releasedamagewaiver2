define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'

], function (ko, Component, quote, priceUtils, totals) {
    'use strict';
    var show_hide_damagewaiver_blockConfig = window.checkoutConfig.show_hide_damagewaiver_block;
    var damagewaiver_label = window.checkoutConfig.damagewaiver_label;
    var damagewaiver_amount = window.checkoutConfig.damagewaiver_amount;

    return Component.extend({

        totals: quote.getTotals(),
        canVisibleCustomFeeBlock: show_hide_damagewaiver_blockConfig,
        getFormattedPrice: ko.observable(priceUtils.formatPrice(damagewaiver_amount, quote.getPriceFormat())),
        getDamagewaiverLabel:ko.observable(damagewaiver_label),

        isDisplayed: function () {
            return this.getValue() != 0;
        },
        getValue: function() {
            var price = 0;
            if (this.totals() && totals.getSegment('damagewaiver')) {
                price = totals.getSegment('damagewaiver').value;
            }            
            
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        }
    });
});
