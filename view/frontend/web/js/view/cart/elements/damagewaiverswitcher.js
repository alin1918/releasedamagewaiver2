/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/element/single-checkbox',
    'SalesIgniter_DamageWaiver/js/view/cart/action/set-insurance',
    'cssdw!css/damagewaiverswitcher/styles'
], function ($, ko, Component, setInsurance) {
    'use strict';
    var quoteItemValue = ko.observable(null);

    return Component.extend({
        quoteItemValue: quoteItemValue,
        quoteItemId: 0,
        insurance: 0,

        defaults: {
            prefer: 'toggle',
            //elementTmpl: 'SalesIgniter_DamageWaiver/view/cart/elements/damagewaiverswitcher',
            templates: {
                radio: 'ui/form/components/single/radio',
                checkbox: 'ui/form/components/single/checkbox',
                toggle: 'SalesIgniter_DamageWaiver/cart/elements/damagewaiverswitcher'
            },
        },

        initialize: function () {
            this._super();
            quoteItemValue(this.quoteItemId);
        },
        /**
         * @inheritdoc
         */
        initObservable: function () {
            return this
                ._super();
            //.observe('quoteItemId');
        },
        onCheckedChanged: function () {
            this._super();

            if (this.checked()) {
                setInsurance(quoteItemValue, this.insurance, 0);
            } else {
                setInsurance(quoteItemValue, this.insurance, 1);
            }
        },

    });
});
