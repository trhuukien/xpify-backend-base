<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

class SectionQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \SectionBuilder\Core\Model\Auth\Validation
     */
    protected $authValidation;

    /**
     * @var \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory
     */
    protected $sectionFactory;

    protected $imageHelper;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $sectionFactory,
        \SectionBuilder\Product\Model\Helper\Image $imageHelper
    ) {
        $this->authValidation = $authValidation;
        $this->sectionFactory = $sectionFactory;
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
        $collection = $this->sectionFactory->create();
        $collection->addFieldToFilter(
            'main_table.url_key',
            $args['key']
        );

        $collection->joinListCategoryName();
        $collection->joinListTagName();
        $collection->joinPricingPlan(
            ['xpp.entity_id IS NULL or xpp.entity_id = main_table.plan_id']
        );
        $collection->joinListBought('AND b.merchant_shop = "' . $merchant->getShop() . '"');
        $collection->joinListInstalled('AND i.merchant_shop = "' . $merchant->getShop() . '"');

        $result = $collection->getFirstItem()->getData();
        if (!$result) {
            return $result;
        }

        $separation = \SectionBuilder\Product\Model\ResourceModel\Section::SEPARATION;
        if (isset($result['xpp_id'])) {
            $result['pricing_plan'] = [
                'entity_id' => $result['xpp_id'],
                'name' => $result['xpp_name'],
                'code' => $result['xpp_code'],
                'status' => $result['xpp_status'],
                'prices' => $result['xpp_prices'] ? json_decode($result['xpp_prices'], true) : [],
                'currency' => \Xpify\PricingPlan\Api\Data\PricingPlanInterface::BASE_CURRENCY,
                'description' => $result['xpp_description']
            ];
        }
        if (isset($result['categories'])) {
            $result['categories'] = explode($separation, $result['categories']);
        }
        if (isset($result['tags'])) {
//            $result['tags'] = explode($separation, $result['tags']);
            $tags = json_decode($result['tags'], true);
            $result['tags'] = $tags;
        }

        $mediaGallery = explode(\SectionBuilder\Product\Model\Helper\Image::SEPARATION, $result['media_gallery'] ?? "");
        $baseUrl = $this->imageHelper->getBaseUrl();
        $images = [];
        foreach ($mediaGallery as $image) {
            $filename = str_replace(\SectionBuilder\Product\Model\Helper\Image::SUB_DIR, "", $image)
                ?: \SectionBuilder\Product\Model\Helper\Image::EMPTY_THUMBNAIL;
            $images[] = ['src' => $baseUrl . $filename];
        }
        $result['images'] = $images;

        if ($result['installed']) {
            $installs = explode($separation, $result['installed']);
            foreach ($installs as $key => $install) {
                list($arrInstall[$key]['theme_id'], $arrInstall[$key]['product_version']) = explode(":", $install);
            }
            $result['installed'] = $arrInstall ?? [];
        }

        $hasOneTime = $result['bought_id'];
        $hasPlan = !isset($result['pricing_plan']) || $this->authValidation->hasPlan(
            $merchant,
            $result['pricing_plan']['code']
        );

        $result['actions'] = [
            'install' => $result['price'] == 0 || $hasOneTime || (isset($result['pricing_plan']['code']) && $hasPlan),
            'purchase' => $result['price'] > 0 && !$hasOneTime,
            'plan' => !$hasPlan
        ];

        return $result;
    }
}
