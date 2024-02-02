<?php
declare(strict_types=1);

namespace SectionBuilder\TagGraphQl\Model\Resolver;

class TagsQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $authValidation;

    protected $tagRepository;
    protected $criteriaBuilder;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Tag\Model\TagRepository $tagRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->authValidation = $authValidation;
        $this->tagRepository = $tagRepository;
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
        $searchResults = $this->tagRepository->getList($this->criteriaBuilder->create());
        return $searchResults->getItems();
    }
}
