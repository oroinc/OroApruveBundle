(function() {
    'use strict';
    window.apruve = {
        /** @property {Object} */
        eventCallbacks: {},

        /** @property {String} */
        APRUVE_LAUNCHED_EVENT: 'APRUVE_LAUNCHED_EVENT',

        /** @property {String} */
        APRUVE_COMPLETE_EVENT: 'APRUVE_COMPLETE_EVENT',

        /** @property {String} */
        APRUVE_CLOSED_EVENT: 'APRUVE_CLOSED_EVENT',

        /**
         * @param {String} eventName
         * @param {Function} callback
         */
        registerApruveCallback: function(eventName, callback) {
            if (!this.eventCallbacks[eventName]) {
                this.eventCallbacks[eventName] = [];
            }
            this.eventCallbacks[eventName].push(callback);
        },

        /**
         * @param {Integer} order
         * @param {String} hash
         */
        setOrder: function(order, hash) {
            if (order && hash) {
                this.order = order;
            }
        },

        startCheckout: function() {
            if (this.eventCallbacks[this.APRUVE_LAUNCHED_EVENT]) {
                this.eventCallbacks[this.APRUVE_LAUNCHED_EVENT].forEach(function(callback) {
                    callback();
                });
            }

            if (this.order && this.order.amount_cents === 1300 && this.eventCallbacks[this.APRUVE_COMPLETE_EVENT]) {
                this.eventCallbacks[this.APRUVE_COMPLETE_EVENT].forEach(function(callback) {
                    callback();
                });
            } else if (this.eventCallbacks[this.APRUVE_CLOSED_EVENT]) {
                this.eventCallbacks[this.APRUVE_CLOSED_EVENT].forEach(function(callback) {
                    callback();
                });
            }
        }
    };
})();
