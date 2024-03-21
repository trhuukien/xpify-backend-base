<?php
declare(strict_types=1);

namespace SectionBuilder\CategoryGraphQl\Model\Resolver;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SectionBuilder\Category\Api\Data\CategoryInterface as ICategory;
use SectionBuilder\Category\Model\CategoryRepository;
use Xpify\Core\Helper\Utils;

class CategoriesQueryV2 extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver  implements ResolverInterface
{
    private CategoryRepository $categoryRepository;
    private SearchCriteriaBuilder $criteriaBuilder;

    private array $filterFields = [
        ICategory::NAME,
        ICategory::IS_ENABLE,
    ];

    /**
     * @param CategoryRepository $categoryRepository
     * @param SearchCriteriaBuilder $criteriaBuilder
     */
    public function __construct(
        \SectionBuilder\Category\Model\CategoryRepository $categoryRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }
    /**
     * @inheritDoc
     */
    public function execResolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $currentPage = $args['currentPage'] ?? 1;
        $pageSize = $args['pageSize'] ?? 20;

        $this->criteriaBuilder->setPageSize($pageSize);
        $this->criteriaBuilder->setCurrentPage($currentPage);

        $filters = $args['filter'] ?? [];
        if (!empty($filters)) {
            foreach ($this->filterFields as $field) {
                if (!empty($filters[$field])) {
                    $this->criteriaBuilder->addFilter($field, $filters[$field]);
                }
            }
        }
        $criteria = $this->criteriaBuilder->create();
        $searchResults = $this->categoryRepository->getList($criteria);
        return [
            'items' => $this->mappingOutputItems($searchResults->getItems()),
            'total_count' => $searchResults->getTotalCount(),
            'page_info' => [
                'current_page' => $currentPage,
                'page_size' => $pageSize,
                'total_pages' => ceil($searchResults->getTotalCount() / $pageSize),
            ],
        ];
    }

    /**
     * Remapping data output for items
     *
     * @param ICategory[] $items
     * @return array
     */
    private function mappingOutputItems(array $items): array
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = array_merge([
                'model' => $item,
                'id' => Utils::idToUid($item->getId()),
            ], $item->getData());
        }
        return $result;
    }
}
