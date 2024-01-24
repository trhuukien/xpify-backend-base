<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api;

use SectionBuilder\Product\Api\Data\SectionInterface;

interface SectionRepositoryInterface
{
    public function getById(int|string $id);

    public function save(SectionInterface $buy);

    public function delete(SectionInterface $buy);

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria): SectionInterface;
}
