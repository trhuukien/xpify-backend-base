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

    public function joinListCategoryId($condition = ['c.is_enable IS NULL OR c.is_enable = ?', 1])
    {
        $this->getSelect()->joinLeft(
            ['cp' => $this->getTable(\SectionBuilder\Category\Model\ResourceModel\CategoryProduct::MAIN_TABLE)],
            'main_table.entity_id = cp.product_id',
            ['categories' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT cp.category_id SEPARATOR ',')")]
        )->joinLeft(
            ['c' => $this->getTable(\SectionBuilder\Category\Model\ResourceModel\Category::MAIN_TABLE)],
            'c.entity_id = cp.category_id',
            ''
        );

        if ($condition) {
            $this->getSelect()->where(...$condition);
        }

        return $this;
    }

    public function joinListCategoryName()
    {
        $separation = \SectionBuilder\Product\Model\ResourceModel\Section::SEPARATION;

        $this->getSelect()->joinLeft(
            ['cp' => $this->getTable(\SectionBuilder\Category\Model\ResourceModel\CategoryProduct::MAIN_TABLE)],
            'main_table.entity_id = cp.product_id',
            'category_id'
        )->joinLeft(
            ['c' => $this->getTable(\SectionBuilder\Category\Model\ResourceModel\Category::MAIN_TABLE)],
            'c.entity_id = cp.category_id',
            [
                'categories' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT c.name SEPARATOR '$separation')")
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

    public function joinListTagId($condition = ['t.is_enable IS NULL OR t.is_enable = ?', 1])
    {
        $this->getSelect()->joinLeft(
            ['tp' => $this->getTable(\SectionBuilder\Tag\Model\ResourceModel\TagProduct::MAIN_TABLE)],
            'main_table.entity_id = tp.product_id',
            ['tags' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT tp.tag_id SEPARATOR ',')")]
        )->joinLeft(
            ['t' => $this->getTable(\SectionBuilder\Tag\Model\ResourceModel\Tag::MAIN_TABLE)],
            't.entity_id = tp.tag_id',
            ''
        );

        if ($condition) {
            $this->getSelect()->where(...$condition);
        }

        return $this;
    }

    public function joinListTagName()
    {
        $separation = \SectionBuilder\Product\Model\ResourceModel\Section::SEPARATION;

        $this->getSelect()->joinLeft(
            ['tp' => $this->getTable(\SectionBuilder\Tag\Model\ResourceModel\TagProduct::MAIN_TABLE)],
            'main_table.entity_id = tp.product_id',
            ['tag_id']
        )->joinLeft(
            ['t' => $this->getTable(\SectionBuilder\Tag\Model\ResourceModel\Tag::MAIN_TABLE)],
            't.entity_id = tp.tag_id',
            [
                'tags' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT t.name SEPARATOR '$separation')")
            ]
        )->where(
            't.is_enable IS NULL OR t.is_enable = ?',
            1
        );

        return $this;
    }

    public function joinPricingPlan($condition = [])
    {
        $this->getSelect()->joinLeft(
            ['xpp' => \Xpify\PricingPlan\Model\ResourceModel\PricingPlan::MAIN_TABLE],
            'main_table.plan_id = xpp.entity_id',
            [
                'xpp_id' => 'xpp.entity_id',
                'xpp_code' => 'xpp.code',
                'xpp_status' => 'xpp.status',
                'xpp_name' => 'xpp.name',
                'xpp_prices' => 'xpp.prices',
                'xpp_description' => 'xpp.description'
            ]
        );

        if ($condition) {
            $this->getSelect()->where(...$condition);
        }

        return $this;
    }

    public function joinListBought($condition = "")
    {
        $this->getSelect()->joinLeft(
            ['b' => $this->getTable(\SectionBuilder\Product\Model\ResourceModel\SectionBuy::MAIN_TABLE)],
            'main_table.entity_id = b.product_id ' . $condition,
            ['bought_id' => 'b.entity_id']
        );

        return $this;
    }

    public function joinListInstalled($condition = "")
    {
        $separation = \SectionBuilder\Product\Model\ResourceModel\Section::SEPARATION;

        $this->getSelect()->joinLeft(
            ['i' => $this->getTable(\SectionBuilder\Product\Model\ResourceModel\SectionInstall::MAIN_TABLE)],
            'main_table.entity_id = i.product_id ' . $condition,
            [
                'installed' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT CONCAT(i.theme_id, ':', i.product_version) SEPARATOR '$separation')")
            ]
        );

        return $this;
    }

    public function groupById()
    {
        $this->getSelect()->group('main_table.entity_id');
        return $this;
    }
}
