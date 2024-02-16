<?php
declare(strict_types=1);

namespace SectionBuilder\FileModifier\Model\Asset;

class HandleUpdateSections
{
    /**
     * @var \SectionBuilder\Core\Model\Config
     */
    protected $configData;

    /**
     * @var \SectionBuilder\Core\Model\Auth\Validation
     */
    protected $authValidation;

    /**
     * @var \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory
     */
    protected $sectionFactory;

    public function __construct(
        \SectionBuilder\Core\Model\Config $configData,
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $sectionFactory
    ) {
        $this->configData = $configData;
        $this->authValidation = $authValidation;
        $this->sectionFactory = $sectionFactory;
    }

    public function beforeUpdateAssetGraphql(
        $section,
        \Xpify\Merchant\Api\Data\MerchantInterface $merchant,
        array &$args
    ): void {
        $isFree = ($section['plan_code'] === null && $section['price'] == 0);

        if ($isFree || $this->authValidation->hasOneTime($merchant, $section['url_key'])) {
            $collection = $this->sectionFactory->create();
            $collection->addFieldToFilter(
                \SectionBuilder\Product\Api\Data\SectionInterface::SRC,
                $this->configData->getFileBaseSrc()
            );
            $baseCssData = $collection->getFirstItem()->getData();

            if (isset($baseCssData[\SectionBuilder\Product\Api\Data\SectionInterface::FILE_DATA])) {
                $args['value'] = "{% style %}\n" .
                    $baseCssData[\SectionBuilder\Product\Api\Data\SectionInterface::FILE_DATA] .
                    "{% endstyle %}\n\n" .
                    $section[\SectionBuilder\Product\Api\Data\SectionInterface::FILE_DATA];
            }
        } else {
            $hasPlan = $section['plan_code'] && $this->authValidation->hasPlan($merchant, $section['plan_code']);
            if ($hasPlan) {
                $args['value'] = $section[\SectionBuilder\Product\Api\Data\SectionInterface::FILE_DATA];
            } else {
                throw new \Magento\Framework\Exception\AuthorizationException(__("Nang cap ban pro hoac mua section nay"));
            }
        }
    }
}
