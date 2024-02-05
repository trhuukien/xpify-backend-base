<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model\ResourceModel\Section;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = \SectionBuilder\Product\Api\Data\SectionInterface::ID;

    /**
     * Define collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SectionBuilder\Product\Model\Section::class,
            \SectionBuilder\Product\Model\ResourceModel\Section::class
        );
    }

    public function joinCategoryTable($fieldSelect = '*')
    {
        $this->getSelect()->joinLeft(
            ['cp' => $this->getTable(\SectionBuilder\Category\Model\ResourceModel\CategoryProduct::MAIN_TABLE)],
            'main_table.entity_id = cp.product_id',
            'cp.category_id'
        )->joinLeft(
            ['c' => $this->getTable(\SectionBuilder\Category\Model\ResourceModel\Category::MAIN_TABLE)],
            'c.entity_id = cp.category_id',
            $fieldSelect
        );

        return $this;
    }

    public function joinListCategoryId()
    {
        $this->getSelect()->joinLeft(
            ['cp' => $this->getTable(\SectionBuilder\Category\Model\ResourceModel\CategoryProduct::MAIN_TABLE)],
            'main_table.entity_id = cp.product_id',
            ['categories' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT cp.category_id SEPARATOR ",")')]
        )->joinLeft(
            ['c' => $this->getTable(\SectionBuilder\Category\Model\ResourceModel\Category::MAIN_TABLE)],
            'c.entity_id = cp.category_id',
            ['category_is_enable' => 'c.is_enable']
        )->where(
            'c.is_enable IS NULL OR c.is_enable = ?',
            1
        );

        return $this;
    }

    public function joinListCategoryName()
    {
        $this->getSelect()->joinLeft(
            ['cp' => $this->getTable(\SectionBuilder\Category\Model\ResourceModel\CategoryProduct::MAIN_TABLE)],
            'main_table.entity_id = cp.product_id',
            ['category_id']
        )->joinLeft(
            ['c' => $this->getTable(\SectionBuilder\Category\Model\ResourceModel\Category::MAIN_TABLE)],
            'c.entity_id = cp.category_id',
            [
                ['category_is_enable' => 'c.is_enable'],
                'categories' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT c.name SEPARATOR ",")')
            ]
        )->where(
            'c.is_enable IS NULL OR c.is_enable = ?',
            1
        );

        return $this;
    }

    public function joinTagTable($fieldSelect = '*')
    {
        $this->getSelect()->joinLeft(
            ['tp' => $this->getTable(\SectionBuilder\Tag\Model\ResourceModel\TagProduct::MAIN_TABLE)],
            'main_table.entity_id = tp.product_id',
            'tp.tag_id'
        )->joinLeft(
            ['t' => $this->getTable(\SectionBuilder\Tag\Model\ResourceModel\Tag::MAIN_TABLE)],
            't.entity_id = tp.tag_id',
            $fieldSelect
        );

        return $this;
    }

    public function joinListTagId()
    {
        $this->getSelect()->joinLeft(
            ['tp' => $this->getTable(\SectionBuilder\Tag\Model\ResourceModel\TagProduct::MAIN_TABLE)],
            'main_table.entity_id = tp.product_id',
            ['tags' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT tp.tag_id SEPARATOR ",")')]
        )->joinLeft(
            ['t' => $this->getTable(\SectionBuilder\Tag\Model\ResourceModel\Tag::MAIN_TABLE)],
            't.entity_id = tp.tag_id',
            ['tag_is_enable' => 't.is_enable']
        )->where(
            't.is_enable IS NULL OR t.is_enable = ?',
            1
        );

        return $this;
    }

    public function joinListTagName()
    {
        $this->getSelect()->joinLeft(
            ['tp' => $this->getTable(\SectionBuilder\Tag\Model\ResourceModel\TagProduct::MAIN_TABLE)],
            'main_table.entity_id = tp.product_id',
            ['tag_id']
        )->joinLeft(
            ['t' => $this->getTable(\SectionBuilder\Tag\Model\ResourceModel\Tag::MAIN_TABLE)],
            't.entity_id = tp.tag_id',
            [
                ['tag_is_enable' => 't.is_enable'],
                'tags' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT t.name SEPARATOR ",")')
            ]
        )->where(
            't.is_enable IS NULL OR t.is_enable = ?',
            1
        );

        return $this;
    }

    public function groupById()
    {
        $this->getSelect()->group('main_table.entity_id');
        return $this;
    }
}
