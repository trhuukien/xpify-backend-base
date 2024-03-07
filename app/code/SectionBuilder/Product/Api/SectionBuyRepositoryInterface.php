<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api;

use SectionBuilder\Product\Api\Data\SectionBuyInterface;

interface SectionBuyRepositoryInterface
{
    public function get(string $field, int|string $value);

    public function save(SectionBuyInterface $sectionBuy);

    public function delete(SectionBuyInterface $sectionBuy);

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
