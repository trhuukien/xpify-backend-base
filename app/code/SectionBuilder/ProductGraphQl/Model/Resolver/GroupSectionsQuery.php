<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

class GroupSectionsQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
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
        $collection->addFieldToFilter('main_table.is_enable', 1);
        $collection->addFieldToFilter(
            'main_table.type_id',
            \SectionBuilder\Product\Model\Config\Source\ProductType::GROUP_TYPE_ID
        );
        $items = $collection->getData();

        foreach ($items as $key => &$item) {
            if (isset($item['child_ids'])) {
                $item['child_ids'] = $item['child_ids'] ? explode(",", $item['child_ids']) : [];
            } else {
                unset($items[$key]);
            }
        }

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

        return $items;
    }
}
