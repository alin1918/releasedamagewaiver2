define(
    [
        'ko',
        'jquery',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/totals',
        'mage/translate'
    ],
    function (ko, $, storage, urlBuilder, getTotalsAction, fullScreenLoader, totals) {
        'use strict';


        return function (quoteItem, insurance, isRemoved) {
            var serviceUrl = urlBuilder.build('salesigniter_damagewaiver/ajax/savedamagewaiver');
            return storage.post(
                serviceUrl,
                JSON.stringify({quote_item: quoteItem(), insurance: insurance, is_removed: isRemoved})
            ).done(
                function (response) {
                    if (response) {
                        quoteItem(response.quoteItemId);
                        var deferred = $.Deferred();
                        getTotalsAction([], deferred);
                    }
                }
            ).fail(
                function (response) {
                    //totals.isLoading(false);
                    //var error = JSON.parse(response.responseText);
                }
            );
        }
    }
);
