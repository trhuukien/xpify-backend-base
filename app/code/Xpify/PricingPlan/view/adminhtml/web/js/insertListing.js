define([
    'Magento_Ui/js/form/components/insert-listing'
], function (InsertListing) {
    'use strict';

    return InsertListing.extend({
        defaults: {
            imports: {
                appId: '${ $.provider }:data.general.entity_id',
            },
            listens: {
                'appId': 'onAppIdChange'
            }
        },

        /** @inheritdoc */
        initObservable: function () {
            return this._super()
                .observe([
                    'appId'
                ]);
        },

        onAppIdChange: function (value) {
            console.log({ value })
        },
    });
});
