/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'SalesIgniter_DamageWaiver/checkout/summary/damagewaiver'
            },
            totals: quote.getTotals(),

            getValue: function () {
                var price = 0;
                if (this.totals()) {
                    price = this.totals()['damagewaiver'];
                }
                return this.getFormattedPrice(price);
            },
            getBaseValue: function () {
                var price = 0;
                if (this.totals()) {
                    price = this.totals()['base_damagewaiver'];
                }
                return this.getFormattedPrice(price);
                //return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            }
        });
    }
);

