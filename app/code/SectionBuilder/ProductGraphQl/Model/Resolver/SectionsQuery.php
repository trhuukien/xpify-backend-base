<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

use Xpify\Core\Helper\Utils;

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
        $merchant = $context->getExtensionAttributes()->getMerchant();

        $collection = $this->collectionFactory->create();
        $collection->setPageSize($args['pageSize']);
        $collection->setCurPage($args['currentPage']);
        $collection->addFieldToFilter('main_table.is_enable', 1);
        $collection->joinListTagName();
        $collection->joinListBought('AND b.merchant_shop = "' . $merchant->getShop() . '"');
        $collection->joinListInstalled('AND i.merchant_shop = "' . $merchant->getShop() . '"');

        if (isset($args['filter']['owned']) && $args['filter']['owned']) {
            $collection->addFieldToFilter(['b.entity_id', 'i.entity_id'], [['notnull' => true], ['notnull' => true]]);
        }
        if (isset($args['search']) && $args['search']) {
            $collection->addFieldToFilter('main_table.name', ['like' => '%' . $args['search'] . '%']);
        }
        if (isset($args['filter']['type_id']) && $args['filter']['type_id']) {
            $collection->addFieldToFilter('main_table.type_id', $args['filter']['type_id']);
        }
        if (isset($args['filter']['product_id']) && $args['filter']['product_id']) {
            $collection->addFieldToFilter('main_table.entity_id', $args['filter']['product_id']);
        }
        if (isset($args['filter']['tag_id']) && $args['filter']['tag_id']) {
            $collection->addFieldToFilter('tag_id', $args['filter']['tag_id']);
        }
        if (isset($args['filter']['category_id']) && $args['filter']['category_id']) {
            $collection->joinCategoryTable('');
            $collection->addFieldToFilter('category_id', $args['filter']['category_id']);
        }
        if (isset($args['filter']['plan_id']) && $args['filter']['plan_id']) {
            $filterPlan = [$args['filter']['plan_id']];
//            foreach ($args['filter']['plan_id'] as $key => $planId) {
//                if ($planId == 0) {
//                    $filterPlan[] = ['null' => true];
//                    $collection->addFieldToFilter('price', 0);
//                    unset($args['filter']['plan_id'][$key]);
//                    break;
//                }
//            }
            $collection->addFieldToFilter('main_table.plan_id', $filterPlan);
        }
        if (isset($args['filter']['price']) && $args['filter']['price']) {
            $collection->addFieldToFilter('main_table.price', ['gteq' => $args['filter']['price']['min']]);
            $collection->addFieldToFilter('main_table.price', ['lteq' => $args['filter']['price']['max']]);
        }

        if (isset($args['sort']) && $args['sort']) {
            $collection->setOrder($args['sort']['column'], $args['sort']['order']);
        }
        $collection->groupById();

        $baseUrl = $this->imageHelper->getBaseUrl();
        $separation = \SectionBuilder\Product\Model\ResourceModel\Section::SEPARATION;
        $items = $collection->getData();

        foreach ($items as &$item) {
            $item['model'] = $item;
            $item['id'] = Utils::idToUid($item['entity_id']);
            if (isset($item['child_ids'])) {
                $item['child_ids'] = $item['child_ids'] ? explode(",", $item['child_ids']) : [];
            }

            if (!empty($item['tags'])) {
                $tags = json_decode($item['tags'], true);
                $item['tags'] = $tags;
            }

            $hasOneTime = $item['bought_id'];
            $hasPlan = !isset($item['pricing_plan']) || $this->authValidation->hasPlan(
                $merchant,
                $item['pricing_plan']['code']
            );

            if ($item['installed']) {
                $installs = explode($separation, $item['installed']);
                $arrInstall = [];
                foreach ($installs as $key => $install) {
                    list($arrInstall[$key]['theme_id'], $arrInstall[$key]['product_version']) = explode(":", $install);
                }
                $item['installed'] = $arrInstall;
            }

            $item['actions'] = [
                'install' => $item['price'] == 0 || $hasOneTime || (isset($item['pricing_plan']['code']) && $hasPlan),
                'purchase' => $item['price'] > 0 && !$hasOneTime,
                'plan' => !$hasPlan
            ];

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
