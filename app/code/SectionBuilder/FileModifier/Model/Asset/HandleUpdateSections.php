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

    public function getSource($path)
    {
        return $this->getFileRaw->execute($path);
    }

    public function changeArgs(
        $section,
        $hasPlan,
        $sourceBase,
        array &$args
    ): void {
        $source = $this->getSource($section['path_source']);

        if ($section['bought_id'] || ($section['plan_code'] === null && $section['price'] == 0)) {
            $args['value'] = "{% style %}\n$sourceBase{% endstyle %}\n\n" . $source;
        } else {
            if ($hasPlan) {
                $args['value'] = $source;
            } else {
                throw new \Magento\Framework\Exception\AuthorizationException(__("Nang cap ban pro hoac mua section nay"));
            }
        }
    }
}
