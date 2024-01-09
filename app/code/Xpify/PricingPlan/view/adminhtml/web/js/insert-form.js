define([
    'Magento_Ui/js/form/components/insert-form'
], function (Insert) {
    'use strict';

    return Insert.extend({
        defaults: {
            listens: {
                responseData: 'onResponse'
            },
            modules: {
                listing: '${ $.listingProvider }',
                modal: '${ $.modalProvider }'
            }
        },

        /**
         * Close modal, reload customer address listing and save customer address
         *
         * @param {Object} responseData
         */
        onResponse: function (responseData) {
            if (!responseData.error) {
                this.modal().closeModal();
                this.listing().reload({
                    refresh: true
                });
            }
            // xNotification.xNotification(responseData);
        },

        /**
         * Event method that closes "Edit role" modal and refreshes grid after role
         * was removed through "Delete" button on the "Edit role" modal
         *
         * @param {String} id - role ID to delete
         */
        onRoleDelete: function (id) {
            this.modal().closeModal();
            this.listing().reload({
                refresh: true
            });
        }
    });
});
