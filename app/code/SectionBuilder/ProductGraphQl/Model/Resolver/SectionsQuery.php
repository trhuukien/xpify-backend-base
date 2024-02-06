<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

class SectionsQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $authValidation;

    protected $sectionRepository;

    protected $criteriaBuilder;

    protected $collectionFactory;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\SectionRepository $sectionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $collectionFactory
    ) {
        $this->authValidation = $authValidation;
        $this->sectionRepository = $sectionRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheirtdoc
     */
    public function execResolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $collection = $this->collectionFactory->create();
        $collection->setPageSize($args['pageSize']);
        $collection->setCurPage($args['currentPage']);
        if (isset($args['filter']['category_id']) && $args['filter']['category_id']) {
            $collection->joinCategoryTable('');
            $collection->addFieldToFilter('category_id', $args['filter']['category_id']);
        }
        if (isset($args['filter']['tag_id']) && $args['filter']['tag_id']) {
            $collection->joinTagTable('');
            $collection->addFieldToFilter('tag_id', $args['filter']['tag_id']);
        }
        if (isset($args['search']) && $args['search']) {
            $collection->addFieldToFilter('main_table.name', ['like' => '%' . $args['search'] . '%']);
        }
        $collection->groupById();

        return [
            'items' => $collection->getData(),
            'total_count' => $collection->getSize(),
            'page_info' => [
                'current_page' => $collection->getCurPage(),
                'page_size' => $collection->getPageSize(),
                'total_pages' => $collection->getLastPageNumber()
            ]
        ];
    }
}
