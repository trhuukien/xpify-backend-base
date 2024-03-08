<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

class SectionTypeResolver implements TypeResolverInterface
{
    const TYPE_MAPPING = [
        "1" => "Section",
        "2" => "GroupSection",
    ];

    public function resolveType(array $data): string
    {
        return self::TYPE_MAPPING[$data['type_id']] ?? 'SectionInterface';
    }
}
