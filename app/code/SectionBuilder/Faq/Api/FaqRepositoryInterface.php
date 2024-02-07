<?php
declare(strict_types=1);

namespace SectionBuilder\Faq\Api;

use SectionBuilder\Faq\Api\Data\FaqInterface;

interface FaqRepositoryInterface
{
    public function get(string $field, mixed $value);

    public function save(FaqInterface $faq);

    public function delete(FaqInterface $faq);

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
