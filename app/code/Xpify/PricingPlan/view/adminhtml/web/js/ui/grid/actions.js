define([
    'Magento_Customer/js/grid/columns/actions',
    'underscore',
    'jquery',
    'Xpify_Core/js/notification',
    'mage/translate'
], function (Action, _, $, xNotifier, $t) {
    'use strict';

    return Action.extend({
        /**
         * Send customer address listing ajax request
         *
         * @param {String} href
         */
        request: function (href) {
            const settings = _.extend({}, this.ajaxSettings, {
                url: href,
                data: {
                    'form_key': window.FORM_KEY
                }
            });

            $('body').trigger('processStart');

            return $.ajax(settings)
                .done(function (response) {
                    if (response.error !== undefined) {
                        xNotifier.xNotification(response, 10000);
                    }
                })
                .fail(function () {
                    xNotifier.xNotification({
                        error: true,
                        message: $t('Sorry, there has been an error processing your request. Please try again later.')
                    }, 10000);
                })
                .always(function () {
                    $('body').trigger('processStop');
                });
        }
    });
});
