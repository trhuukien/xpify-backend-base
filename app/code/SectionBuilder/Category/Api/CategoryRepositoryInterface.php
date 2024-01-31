<?php
declare(strict_types=1);

namespace SectionBuilder\Category\Api;

use SectionBuilder\Category\Api\Data\CategoryInterface;

interface CategoryRepositoryInterface
{
    public function get(string $field, mixed $value);

    public function save(CategoryInterface $category);

    public function delete(CategoryInterface $category);

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
