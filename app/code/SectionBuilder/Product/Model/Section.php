<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model;

use SectionBuilder\Product\Api\Data\SectionInterface;

class Section extends \Magento\Framework\Model\AbstractModel implements SectionInterface
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SectionBuilder\Product\Model\ResourceModel\Section::class);
    }

    public function getSectionId(): int
    {
        return $this->getData(self::ID);
    }

    public function setSectionId(int|string $id): SectionInterface
    {
        return $this->setData(self::ID, $id);
    }

    public function getPlanIds(): string
    {
        return $this->getData(self::PLAN_IDS);
    }

    public function setPlanIds(string $planIds): SectionInterface
    {
        return $this->setData(self::PLAN_IDS, $planIds);
    }

    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): SectionInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getPrice(): float
    {
        return (float)$this->getData(self::PRICE);
    }

    public function setPrice(float $price): self
    {
        return $this->setData(self::PRICE, $price);
    }

    public function getSrc(): string
    {
        return $this->getData(self::SRC);
    }

    public function setSrc(string $src): SectionInterface
    {
        return $this->setData(self::SRC, $src);
    }

    public function getFileData(): ?string
    {
        return $this->getData(self::FILE_DATA);
    }

    public function setFileData(?string $fileData): SectionInterface
    {
        return $this->setData(self::FILE_DATA, $fileData);
    }
}