define([
    'Magento_Ui/js/form/components/button',
    'underscore'
], function (Button, _) {
    'use strict';

    return Button.extend({
        defaults: {
            entityId: null,
            customerId: null
        },

        /**
         * Apply action on target component,
         * but previously create this component from template if it is not existed
         *
         * @param {Object} action - action configuration
         */
        applyAction: function (action) {
            if (action.params && action.params[0]) {
                action.params[0]['role_id'] = this.entityId;
                action.params[0]['customer_id'] = this.customerId;
            } else {
                action.params = [{
                    'role_id': this.entityId,
                    'customer_id': this.customerId
                }];
            }

            this._super();
        }
    });
});
