<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

class GroupSectionQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
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
        if (!$args['key']) {
            return [];
        }

        $merchant = $this->getMerchantSession()->getMerchant();
        $collection = $this->collectionFactory->create();
        $collection->joinListBought(
            [
                'b.merchant_shop IS NULL or b.merchant_shop = ?',
                $merchant->getShop()
            ]
        );
        $collection->addFieldToFilter(
            'main_table.url_key',
            $args['key']
        );

        $item = $collection->getFirstItem()->getData();
        if (!isset($item['child_ids'])) {
            return [];
        }

        $item['child_ids'] = $item['child_ids'] ? explode(",", $item['child_ids']) : [];
        $mediaGallery = explode(
            \SectionBuilder\Product\Model\Helper\Image::SEPARATION,
            $item['media_gallery'] ?? ""
        );
        $baseUrl = $this->imageHelper->getBaseUrl();
        $images = [];
        foreach ($mediaGallery as $image) {
            $filename = str_replace(\SectionBuilder\Product\Model\Helper\Image::SUB_DIR, "", $image)
                ?: \SectionBuilder\Product\Model\Helper\Image::EMPTY_THUMBNAIL;
            $images[] = ['src' => $baseUrl . $filename];
        }
        $item['images'] = $images;

        $item['actions']['purchase'] = !$item['bought_id'];

        return $item;
    }
}
