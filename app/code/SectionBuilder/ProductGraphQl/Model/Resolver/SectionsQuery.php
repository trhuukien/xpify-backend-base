<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

class SectionsQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $authValidation;

    protected $sectionRepository;

    protected $criteriaBuilder;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\SectionRepository $sectionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->authValidation = $authValidation;
        $this->sectionRepository = $sectionRepository;
        $this->criteriaBuilder = $criteriaBuilder;
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
        $criteriaBuilder = $this->criteriaBuilder;
        $criteriaBuilder->setPageSize($args['pageSize']);
        $criteriaBuilder->setCurrentPage($args['currentPage']);
        $criteriaBuilder->addFilter('name', '%' . $args['search'] . '%', 'like');
        $searchResult = $this->sectionRepository->getList($criteriaBuilder->create());

        $data = [
            'items' => $searchResult->getItems(),
            'total_count' => $searchResult->getTotalCount()
        ];
        return $data;
    }
}
