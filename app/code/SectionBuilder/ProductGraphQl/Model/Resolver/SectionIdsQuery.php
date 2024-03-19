<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

class SectionIdsQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
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
        $collection->joinListBought('AND b.merchant_shop = "' . $args['merchant_shop'] ?? $merchant->getShop() . '"');
        $collection->addFieldToFilter('main_table.is_enable', 1);
        $collection->addFieldToFilter('main_table.type_id', \SectionBuilder\Product\Model\Config\Source\ProductType::SIMPLE_TYPE_ID);
        $collection->groupById();
        $items = $collection->getData();

        return $items;
    }
}
