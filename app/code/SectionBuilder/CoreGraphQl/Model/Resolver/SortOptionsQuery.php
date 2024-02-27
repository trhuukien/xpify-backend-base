<?php
declare(strict_types=1);

namespace SectionBuilder\CoreGraphQl\Model\Resolver;

class SortOptionsQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
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
        return [
            [
                'label' => 'Popular',
                'value' => 'main_table.qty_sold asc',
                'directionLabel' => 'Ascending'
            ],
            [
                'label' => 'Popular',
                'value' => 'main_table.qty_sold desc',
                'directionLabel' => 'Descending'
            ],
            [
                'label' => 'Alphabet',
                'value' => 'main_table.name asc',
                'directionLabel' => 'A-Z'
            ],
            [
                'label' => 'Alphabet',
                'value' => 'main_table.name desc',
                'directionLabel' => 'Z-A'
            ],
            [
                'label' => 'Release',
                'value' => 'main_table.updated_at asc',
                'directionLabel' => 'Oldest'
            ],
            [
                'label' => 'Release',
                'value' => 'main_table.updated_at desc',
                'directionLabel' => 'Newest'
            ],
            [
                'label' => 'Price',
                'value' => 'main_table.price asc',
                'directionLabel' => 'Lowest'
            ],
            [
                'label' => 'Price',
                'value' => 'main_table.price desc',
                'directionLabel' => 'Highest'
            ]
        ];
    }
}
