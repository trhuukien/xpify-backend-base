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

    protected $getFileRaw;

    public function __construct(
        \SectionBuilder\Core\Model\Config $configData,
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $sectionFactory,
        \SectionBuilder\FileModifier\Model\GetFileRaw $getFileRaw
    ) {
        $this->configData = $configData;
        $this->authValidation = $authValidation;
        $this->sectionFactory = $sectionFactory;
        $this->getFileRaw = $getFileRaw;
    }

    public function beforeUpdateAssetGraphql(
        $section,
        \Xpify\Merchant\Api\Data\MerchantInterface $merchant,
        array &$args
    ): void {
        $isFree = ($section['plan_code'] === null && $section['price'] == 0);
        $source = $this->getFileRaw->execute($section['path_source']);

        if ($isFree || $section['bought_id']) {
            $collection = $this->sectionFactory->create();
            $collection->addFieldToFilter(
                \SectionBuilder\Product\Api\Data\SectionInterface::SRC,
                $this->configData->getFileBaseSrc()
            );
            $collection->addFieldToSelect(['path_source', 'version']);
            $baseCssData = $collection->getFirstItem()->getData();
            $sourceCss = $baseCssData['path_source'] ?? $this->getFileRaw->execute($baseCssData['path_source']);

            if ($sourceCss) {
                $args['value'] = "{% style %}\n$sourceCss{% endstyle %}\n\n" . $source;
            }
        } else {
            $hasPlan = $section['plan_code'] && $this->authValidation->hasPlan($merchant, $section['plan_code']);
            if ($hasPlan) {
                $args['value'] = $source;
            } else {
                throw new \Magento\Framework\Exception\AuthorizationException(__("Nang cap ban pro hoac mua section nay"));
            }
        }
    }
}
