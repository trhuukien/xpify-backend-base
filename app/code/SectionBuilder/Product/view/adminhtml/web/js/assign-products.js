define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedProducts = config.selectedProducts,
            categoryProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            inputSelector = $('group_products');

        inputSelector.value = Object.toJSON(categoryProducts);

        function registerCategoryProduct(grid, element, checked) {
            if (checked) {
                categoryProducts.set(element.value, 1);
            } else {
                categoryProducts.unset(element.value);
            }
            inputSelector.value = Object.toJSON(categoryProducts);
            grid.reloadParams = {
                'selected_products[]': categoryProducts.keys()
            };
        }

        function categoryProductRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        gridJsObject.rowClickCallback = categoryProductRowClick;
        gridJsObject.checkboxCheckCallback = registerCategoryProduct;
    };
});
