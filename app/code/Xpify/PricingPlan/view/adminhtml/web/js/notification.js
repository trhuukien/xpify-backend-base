define([
    'jquery'
], function ($) {
    'use strict';
    return {
        xNotification: function (response, clearAfter = 3000) {
            if (Array.isArray(response)) {
                response = response[0];
            }
            $('body').notification('clear')
                .notification('add', {
                    reset_pw_subuser_request_success: !response.error,
                    error: response.error,
                    message: response.message,
                    messageErrorEmail: response.messageErrorEmail,
                    insertMethod: function (message) {
                        var $wrapper = $('<div/>').html(message);

                        $('.page-main-actions').after($wrapper);
                    }
                });
            if (clearAfter) {
                setTimeout(function () {
                    $('body').notification('clear');
                }, clearAfter);
            }
        }
    };
});
