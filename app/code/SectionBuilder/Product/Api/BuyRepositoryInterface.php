<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api;

use SectionBuilder\Product\Api\Data\BuyInterface;

interface BuyRepositoryInterface
{
    public function getById(int|string $id);

    public function save(BuyInterface $buy);

    public function delete(BuyInterface $buy);

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria): BuyInterface;
}
