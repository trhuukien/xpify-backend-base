<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api;

use SectionBuilder\Product\Api\Data\SectionInterface;

interface SectionRepositoryInterface
{
    public function get(string $field, mixed $value): SectionInterface;

    public function save(SectionInterface $section);

    public function delete(SectionInterface $section);

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
