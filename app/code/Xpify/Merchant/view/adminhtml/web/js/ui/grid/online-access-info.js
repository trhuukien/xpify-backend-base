define([
    'Magento_Ui/js/grid/columns/column',
], (Column) => {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Xpify_Merchant/grid/merchant/online-access-info',
        },

        getData: function (record) {
            return record[this.index];
        },
    });
});
