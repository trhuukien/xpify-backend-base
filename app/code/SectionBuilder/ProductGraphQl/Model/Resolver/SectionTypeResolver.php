<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

class SectionTypeResolver implements TypeResolverInterface
{
    const TYPE_MAPPING = [
        "1" => "Section",
        "2" => "GroupSection",
    ];

    /**
     * @inheritDoc
     * @throws GraphQlNoSuchEntityException
     */
    public function resolveType(array $data): string
    {
        if (empty($data['type_id']) || empty(self::TYPE_MAPPING[$data['type_id']])) {
            throw new GraphQlNoSuchEntityException(__('Section not found'));
        }
        return self::TYPE_MAPPING[$data['type_id']];
    }
}
