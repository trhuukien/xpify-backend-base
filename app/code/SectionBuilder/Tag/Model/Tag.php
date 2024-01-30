<?php
declare(strict_types=1);

namespace SectionBuilder\Tag\Model;

use SectionBuilder\Tag\Api\Data\TagInterface;

class Tag extends \Magento\Framework\Model\AbstractModel implements TagInterface
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SectionBuilder\Tag\Model\ResourceModel\Tag::class);
    }

    public function getTagId(): int
    {
        return $this->getData(self::ID);
    }

    public function setTagId(int|string $id): TagInterface
    {
        return $this->setData(self::ID, $id);
    }

    public function getIsEnable(): int
    {
        return $this->getData(self::IS_ENABLE);
    }

    public function setIsEnable(int|string $isEnable): TagInterface
    {
        return $this->setData(self::IS_ENABLE, $isEnable);
    }

    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): TagInterface
    {
        return $this->setData(self::NAME, $name);
    }
}
