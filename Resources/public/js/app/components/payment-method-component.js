/* global apruve */
define(function(require) {
    'use strict';

    const _ = require('underscore');
    const mediator = require('oroui/js/mediator');
    const scriptjs = require('scriptjs');

    const BaseComponent = require('oropayment/js/app/components/payment-method-component');

    const PaymentMethodComponent = BaseComponent.extend({
        /**
         * @property {Object}
         */
        options: {
            orderIdParamName: '',
            paymentMethod: null,
            apruveJsUrls: {
                test: '//test.apruve.com/js/v4/apruve.js',
                prod: '//app.apruve.com/js/v4/apruve.js'
            },
            testMode: true
        },

        apruve: null,

        returnUrl: '',

        errorUrl: '',

        /**
         * @inheritDoc
         */
        constructor: function PaymentMethodComponent(options) {
            PaymentMethodComponent.__super__.constructor.call(this, options);
        },

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = _.defaults(options || {}, this.options);

            mediator.on('checkout:place-order:response', this.handleSubmit, this);
            mediator.on('checkout:payment:method:changed', this.onPaymentMethodChanged, this);

            this.loadAppruveJsLibrary();
        },

        /**
         * @param {Object} eventData
         */
        onPaymentMethodChanged: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
                this.loadAppruveJsLibrary();
            }
        },

        loadAppruveJsLibrary: function() {
            const appruveJsUrl =
                this.options.testMode ? this.options.apruveJsUrls.test : this.options.apruveJsUrls.prod;

            this._deferredInit();
            scriptjs(appruveJsUrl, this.initializeApruve.bind(this));
        },

        initializeApruve: function() {
            this.apruve = apruve;

            this.apruve
                .registerApruveCallback(this.apruve.APRUVE_LAUNCHED_EVENT, this.handleApruveLaunch.bind(this));
            this.apruve
                .registerApruveCallback(this.apruve.APRUVE_COMPLETE_EVENT, this.handleApruveComplete.bind(this));
            this.apruve
                .registerApruveCallback(this.apruve.APRUVE_CLOSED_EVENT, this.handleApruveClose.bind(this));

            this._resolveDeferredInit();
        },

        /**
         * @param {Object} eventData
         */
        handleSubmit: function(eventData) {
            if (eventData.responseData.paymentMethod === this.options.paymentMethod) {
                eventData.stopped = true;

                const responseData = _.extend({successUrl: this.getSuccessUrl()}, eventData.responseData);

                if (!responseData.apruveOrder) {
                    mediator.execute('redirectTo', {url: this.errorUrl}, {redirect: true});

                    return;
                }

                this.returnUrl = responseData.returnUrl;
                this.errorUrl = responseData.errorUrl;

                const self = this;
                // Ensure that apruve library is loaded before starting apruve checkout.
                this.deferredInit.done(function() {
                    // Provide order object and secure hash to apruve.
                    self.apruve.setOrder(responseData.apruveOrder, responseData.apruveOrderSecureHash);

                    self.apruve.startCheckout();
                });
            }
        },

        /**
         * Hide loading mask only when apruve popup is fully loaded.
         */
        handleApruveLaunch: function() {
            mediator.execute('hideLoading');
        },

        /**
         * @param {String} orderId
         */
        handleApruveComplete: function(orderId) {
            mediator.execute('showLoading');
            mediator.execute(
                'redirectTo',
                {url: this.returnUrl + '?' + this.options.orderIdParamName + '=' + orderId},
                {redirect: true}
            );
        },

        handleApruveClose: function() {
            mediator.execute('showLoading');
            mediator.execute('redirectTo', {url: this.errorUrl}, {redirect: true});
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            mediator.off(null, null, this);

            PaymentMethodComponent.__super__.dispose.call(this);
        }
    });

    return PaymentMethodComponent;
});
