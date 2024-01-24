<?php
declare(strict_types=1);

namespace SectionBuilder\AssetGraphQl\Model\Resolver;

class UpdateAssetMutation extends \Xpify\Asset\Model\UpdateAssetMutation implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \SectionBuilder\Core\Model\Auth\Validation
     */
    protected $authValidation;

    /**
     * @var \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory
     */
    protected $sectionFactory;

    /**
     * @var \SectionBuilder\Product\Model\ResourceModel\Buy\CollectionFactory
     */
    protected $buyFactory;

    public function __construct(
        \Xpify\Theme\Model\Validation $validation,
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $sectionFactory,
        \SectionBuilder\Product\Model\ResourceModel\Buy\CollectionFactory $buyFactory
    ) {
        parent::__construct($validation);
        $this->authValidation = $authValidation;
        $this->sectionFactory = $sectionFactory;
        $this->buyFactory = $buyFactory;
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
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('SectionBuilder\FileModifier\Cron\Plan')
            ->process();
        dd(123);

        $this->validation->validateArgs(
            $args,
            ['theme_id', 'asset'],
            ['value']
        );

        $collection = $this->sectionFactory->create();
        $collection->addFieldToFilter('src', $args['asset']);
        $section = $collection->getData()[0] ?? [];

        if ($section) {
            $content = $section['file_data'];

            $merchant = $this->getMerchantSession()->getMerchant();
            $isPlanPro = $this->authValidation->checkAuth($merchant);

            $collectionBuy = $this->buyFactory->create();
            $collectionBuy->addFieldToFilter('merchant_shop', $merchant->getShop());
            $collectionBuy->addFieldToFilter('product_id', $section['entity_id']);
            $bought = $collectionBuy->getData()[0] ?? [];

            if ($bought) {
                $collection = $this->sectionFactory->create();
                $collection->addFieldToFilter(
                    'src',
                    \SectionBuilder\Product\Model\ResourceModel\Section::FILE_BASE_CSS
                );
                $sectionBaseCss = $collection->getData()[0]['file_data'] ?? '';

                $content = "{% style %}\n" . $sectionBaseCss . "{% endstyle %}\n\n" . $content;
            } elseif (!$isPlanPro) {
                throw new \Magento\Framework\Exception\AuthorizationException(__("Nang cap ban pro hoac mua section nay"));
            }
        } else {
            throw new \Magento\Framework\Exception\ValidatorException(__("Section khong ton tai"));
        }

        $args['value'] = $content ?? $args['value'];
        return parent::execResolve($field, $context, $info, $value, $args);
    }
}
