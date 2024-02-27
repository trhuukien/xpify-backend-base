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

        $collection = $this->sectionFactory->create();
        $collection->addFieldToFilter(
            'main_table.url_key',
            $args['key']
        );
        $collection->join(
            ['xpp' => \Xpify\PricingPlan\Model\ResourceModel\PricingPlan::MAIN_TABLE],
            'main_table.plan_id = xpp.entity_id',
            [
                'xpp_id' => 'xpp.entity_id',
                'xpp_name' => 'xpp.name',
                'xpp_code' => 'xpp.code',
                'xpp_status' => 'xpp.status'
            ]
        );
        $collection->joinListCategoryName();
        $collection->joinListTagName();

        $result = $collection->getFirstItem()->getData();

        if (!$result) {
            return $result;
        }

        if (isset($result['xpp_id'])) {
            $result['pricing_plan'] = [
                'entity_id' => $result['xpp_id'],
                'name' => $result['xpp_name'],
                'code' => $result['xpp_code'],
                'status' => $result['xpp_status']
            ];
        }
        if (isset($result['categories'])) {
            $result['categories'] = explode(\SectionBuilder\Product\Model\ResourceModel\Section::SEPARATION, $result['categories']);
        }
        if (isset($result['tags'])) {
            $result['tags'] = explode(\SectionBuilder\Product\Model\ResourceModel\Section::SEPARATION, $result['tags']);
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

        if (1) { // Free
            $result['is_show_install'] = true;
            $result['is_show_purchase'] = false;
            $result['is_show_plan'] = false;
        } else {
            $merchant = $this->getMerchantSession()->getMerchant();
            $bought = $this->authValidation->hasOneTime(
                $merchant,
                $result[\SectionBuilder\Product\Api\Data\SectionInterface::KEY]
            );
            $result['is_show_plan'] = !$this->authValidation->hasPlan($merchant, $result['plan_need_subscribe']);
            $result['is_show_install'] = !$result['is_show_plan'] || $bought;
            $result['is_show_purchase'] = !$bought;
        }

        return $result;
    }
}
