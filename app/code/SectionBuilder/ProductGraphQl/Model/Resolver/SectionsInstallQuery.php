<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

class SectionsInstallQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
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
        $merchant = $this->getMerchantSession()->getMerchant();

        $collection = $this->collectionFactory->create();
        $collection->joinListInstalled(
            [
                'i.merchant_shop = ?',
                $args['merchant_shop'] ?? $merchant->getShop()
            ]
        );
        $collection->addFieldToSelect(['name', 'url_key', 'price', 'version', 'media_gallery']);
        $collection->addFieldToFilter('main_table.is_enable', 1);
        $collection->groupById();
        $items = $collection->getData();

        $baseUrl = $this->imageHelper->getBaseUrl();
        $separation = \SectionBuilder\Product\Model\ResourceModel\Section::SEPARATION;
        foreach ($items as &$item) {
            $item['product_id'] = $item['entity_id'];

            if (isset($item['installed'])) {
                $installs = explode($separation, $item['installed']);
                $arrInstall = [];
                foreach ($installs as $key => $install) {
                    list($arrInstall[$key]['theme_id'], $arrInstall[$key]['product_version']) = explode(":", $install);
                }
                $item['installed'] = $arrInstall;
            }

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