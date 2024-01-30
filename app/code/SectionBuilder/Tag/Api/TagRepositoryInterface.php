<?php
declare(strict_types=1);

namespace SectionBuilder\Tag\Api;

use SectionBuilder\Tag\Api\Data\TagInterface;

interface TagRepositoryInterface
{
    public function get(string $field, mixed $value);

    public function save(TagInterface $tag);

    public function delete(TagInterface $tag);

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
