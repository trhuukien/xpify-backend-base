<?php
declare(strict_types=1);

namespace SectionBuilder\FaqGraphQl\Model\Resolver;

class FaqsQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $authValidation;

    protected $faqRepository;
    protected $criteriaBuilder;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Faq\Model\FaqRepository $faqRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->authValidation = $authValidation;
        $this->faqRepository = $faqRepository;
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
        $searchResults = $this->faqRepository->getList($this->criteriaBuilder->create());
        return $searchResults->getItems();
    }
}
