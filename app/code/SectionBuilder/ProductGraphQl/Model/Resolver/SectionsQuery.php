<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

class SectionsQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $authValidation;

    protected $sectionRepository;

    protected $criteriaBuilder;

    protected $collectionFactory;

    protected $imageHelper;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\SectionRepository $sectionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $collectionFactory,
        \SectionBuilder\Product\Model\Helper\Image $imageHelper
    ) {
        $this->authValidation = $authValidation;
        $this->sectionRepository = $sectionRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->imageHelper = $imageHelper;
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
        $collection->addFieldToFilter('main_table.is_enable', 1);
        $collection->addFieldToFilter(
            'main_table.type_id',
            \SectionBuilder\Product\Model\Config\Source\ProductType::SIMPLE_TYPE_ID
        );

        if (isset($args['search']) && $args['search']) {
            $collection->addFieldToFilter('main_table.name', ['like' => '%' . $args['search'] . '%']);
        }
        if (isset($args['filter']['product_id']) && $args['filter']['product_id']) {
            $collection->addFieldToFilter('entity_id', $args['filter']['product_id']);
        }
        if (isset($args['filter']['category_id']) && $args['filter']['category_id']) {
            $collection->joinCategoryTable('');
            $collection->addFieldToFilter('category_id', $args['filter']['category_id']);
        }
        if (isset($args['filter']['tag_id']) && $args['filter']['tag_id']) {
            $collection->joinTagTable('');
            $collection->addFieldToFilter('tag_id', $args['filter']['tag_id']);
        }
        if (isset($args['filter']['plan_id']) && $args['filter']['plan_id']) {
            $filterPlan = [$args['filter']['plan_id']];
            foreach ($args['filter']['plan_id'] as $key => $planId) {
                if ($planId == 0) {
                    $filterPlan[] = ['null' => true];
                    $collection->addFieldToFilter('price', 0);
                    unset($args['filter']['plan_id'][$key]);
                    break;
                }
            }
            $collection->addFieldToFilter('plan_id', $filterPlan);
        }
        if (isset($args['filter']['price']) && $args['filter']['price']) {
            $collection->addFieldToFilter('main_table.price', ['gteq' => $args['filter']['price']['min']]);
            $collection->addFieldToFilter('main_table.price', ['lteq' => $args['filter']['price']['max']]);
        }
        if (isset($args['sort']) && $args['sort']) {
            $collection->setOrder($args['sort']['column'], $args['sort']['order']);
        }
        $collection->groupById();

        $items = $collection->getData();
        $baseUrl = $this->imageHelper->getBaseUrl();
        foreach ($items as &$item) {
            $mediaGallery = explode(\SectionBuilder\Product\Model\Helper\Image::SEPARATION, $item['media_gallery'] ?? "");
            $images = [];
            foreach ($mediaGallery as $image) {
                $filename = str_replace(\SectionBuilder\Product\Model\Helper\Image::SUB_DIR, "", $image)
                    ?: \SectionBuilder\Product\Model\Helper\Image::EMPTY_THUMBNAIL;
                $images[] = ['src' => $baseUrl . $filename];
            }
            $item['images'] = $images;
        }

        return [
            'items' => $items,
            'total_count' => $collection->getSize(),
            'page_info' => [
                'current_page' => $collection->getCurPage(),
                'page_size' => $collection->getPageSize(),
                'total_pages' => $collection->getLastPageNumber()
            ]
        ];
    }
}
