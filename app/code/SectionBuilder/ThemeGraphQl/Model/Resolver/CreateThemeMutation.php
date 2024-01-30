<?php
declare(strict_types=1);

namespace SectionBuilder\ThemeGraphQl\Model\Resolver;

class CreateThemeMutation extends \Xpify\Theme\Model\CreateThemeMutation implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
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
        return parent::execResolve($field, $context, $info, $value, $args);
    }
}
