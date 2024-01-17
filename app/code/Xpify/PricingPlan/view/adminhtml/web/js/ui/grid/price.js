define([
    'Magento_Ui/js/grid/columns/column'
], (Column) => {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Xpify_PricingPlan/price',
        },

        getDataPrices: function (record) {
            return record[this.index];
        }
    });
});
