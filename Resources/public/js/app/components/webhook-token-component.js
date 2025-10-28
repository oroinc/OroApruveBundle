import $ from 'jquery';
import _ from 'underscore';
import routing from 'routing';
import BaseComponent from 'oroui/js/app/components/base/component';
import LoadingMaskView from 'oroui/js/app/views/loading-mask-view';

const WebhookTokenComponent = BaseComponent.extend({
    options: {
        generateTokenRoute: 'oro_apruve_generate_token',
        webhookRoute: 'oro_apruve_webhook_notify'
    },

    /**
     * @inheritdoc
     */
    constructor: function WebhookTokenComponent(options) {
        WebhookTokenComponent.__super__.constructor.call(this, options);
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.options = _.defaults(options || {}, this.options);

        this.$button = options._sourceElement;
        this.$webhookToken = $(options.webhookTokenSelector);
        this.$webhookUrl = $(options.webhookUrlSelector);
        this.loadingMaskView = new LoadingMaskView({container: $('body')});

        this.initListeners();

        this.setWebhookUrl(this.buildWebhookUrl(this.getWebhookToken()));
    },

    initListeners: function() {
        this.$button.on('click', this.buttonClickHandler.bind(this));
    },

    buttonClickHandler: function() {
        const self = this;

        this.fetchToken(function(token) {
            self.setWebhookToken(token);
            self.setWebhookUrl(self.buildWebhookUrl(token));
        });
    },

    /**
     * @param {Function} fetchTokenCallback
     */
    fetchToken: function(fetchTokenCallback) {
        const self = this;

        $.ajax({
            url: routing.generate(this.options.generateTokenRoute),
            type: 'POST',
            beforeSend: function() {
                self.loadingMaskView.show();
            },
            success: function(response) {
                const token = response.token || '';
                if (token) {
                    fetchTokenCallback(token);
                }
            },
            complete: function() {
                self.loadingMaskView.hide();
            }
        });
    },

    /**
     * @param {String} token
     * @returns {String}
     */
    buildWebhookUrl: function(token) {
        return routing.generate(this.options.webhookRoute, {token: token}, true);
    },

    /**
     * @param {String} url
     */
    setWebhookUrl: function(url) {
        this.$webhookUrl.text(url);
    },

    /**
     * @param {String} token
     */
    setWebhookToken: function(token) {
        this.$webhookToken.val(token);
    },

    /**
     * @returns {String}
     */
    getWebhookToken: function() {
        return this.$webhookToken.val();
    },

    dispose: function() {
        if (this.disposed) {
            return;
        }

        this.$button.off('click');

        WebhookTokenComponent.__super__.dispose.call(this);
    }
});

export default WebhookTokenComponent;
