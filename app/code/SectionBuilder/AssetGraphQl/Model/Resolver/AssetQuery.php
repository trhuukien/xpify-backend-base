<?php
declare(strict_types=1);

namespace SectionBuilder\AssetGraphQl\Model\Resolver;

class AssetQuery extends \Xpify\Asset\Model\AssetQuery implements \Magento\Framework\GraphQl\Query\ResolverInterface
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
