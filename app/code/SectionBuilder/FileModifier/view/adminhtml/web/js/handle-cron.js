define([
    'jquery'
], function ($) {
    return function (config, element) {
        var logSelector = $("#cron-messages");

        $(element).click(function () {
            $.ajax({
                type: "POST",
                url: config.url,
                data: {
                    "form_key": config.form_key
                },
                beforeSend: function () {
                    $('body').trigger('processStart');
                },
                success: function (response) {
                    if (response.success) {
                        logSelector.append(`
                            <div class="messages">
                                <div class="message message-notice">${response.success}</div>
                                <div class="message message-success">Done</div>
                            </div>
                        `);
                    }

                    if (response.warning) {
                        logSelector.append(`
                            <div class="messages">
                                <div class="message message-warning">${response.warning}</div>
                            </div>
                        `);
                    }

                    if (response.error) {
                        logSelector.append(`
                            <div class="messages">
                                <div class="message message-error">${response.error}</div>
                            </div>
                        `);
                    }
                },
                complete: function () {
                    $('body').trigger('processStop');
                },
                error: function (response) {
                    logSelector.append(`
                        <div class="messages">
                            <div class="message message-error">Error</div>
                            <div class="message message-notice">${response.responseText}</div>
                        </div>
                    `);
                }
            });
        });
    };
});
