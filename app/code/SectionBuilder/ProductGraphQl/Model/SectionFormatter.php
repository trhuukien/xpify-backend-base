<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model;

use SectionBuilder\Product\Api\Data\SectionInterface as ISection;

final class SectionFormatter
{
    /**
     * To Array
     *
     * @param ISection $section
     * @return array
     */
    public static function toGraphQl(ISection $section): array
    {
        $fields = [
            'entity_id',
            'is_enable',
            'plan_id',
            'name',
            'url_key',
            'price',
            'src',
            'version',
            'description',
            'release_note',
            'demo_link',
        ];
        // get needed fields
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $section->getData($field);
        }
        $uidEncoder = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\GraphQl\Query\Uid::class);
        return array_merge(['model' => $section, 'id' => $uidEncoder->encode($section->getId())], $data);
    }
}
