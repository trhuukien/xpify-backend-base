<?php
namespace SectionBuilder\Product\Model;

class DataProvider
{
    protected $productIdsUpdating;

    public function getProductIdsUpdating()
    {
        return $this->productIdsUpdating;
    }

    public function setProductIdsUpdating($productIdsUpdating)
    {
        $this->productIdsUpdating = $productIdsUpdating;
    }
}
