<?php
declare(strict_types=1);

namespace SectionBuilder\Category\Model;

use SectionBuilder\Category\Api\Data\CategoryInterface;

class Category extends \Magento\Framework\Model\AbstractModel implements CategoryInterface
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SectionBuilder\Category\Model\ResourceModel\Category::class);
    }

    public function getCategoryId(): int
    {
        return (int)$this->getData(self::ID);
    }

    public function setCategoryId(int|string $id): CategoryInterface
    {
        return $this->setData(self::ID, $id);
    }

    public function getIsEnable(): int
    {
        return (int)$this->getData(self::IS_ENABLE);
    }

    public function setIsEnable(int|string $isEnable): CategoryInterface
    {
        return $this->setData(self::IS_ENABLE, $isEnable);
    }

    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): CategoryInterface
    {
        return $this->setData(self::NAME, $name);
    }
}
