<?php
declare(strict_types=1);

namespace SectionBuilder\FaqGraphQl\Model\Resolver;

class FaqsQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $mediaUrl = '';

    protected $authValidation;

    protected $faqRepository;

    protected $criteriaBuilder;

    protected $imageHelper;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Faq\Model\FaqRepository $faqRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \SectionBuilder\Product\Model\Helper\Image $imageHelper
    ) {
        $this->authValidation = $authValidation;
        $this->faqRepository = $faqRepository;
        $this->criteriaBuilder = $criteriaBuilder;
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
        $searchResults = $this->faqRepository->getList($this->criteriaBuilder->create());
        $faqs = $searchResults->getItems();
        $this->mediaUrl = $this->imageHelper->getBaseUrl('');

        foreach ($faqs as &$faq) {
            $pattern = '/{{media url=&quot;(.*?)&quot;}}/';
            $faq['content'] = preg_replace_callback($pattern, function ($matches) {
                return $this->mediaUrl . $matches[1];
            }, $faq['content']);
        }

        return $faqs;
    }
}
