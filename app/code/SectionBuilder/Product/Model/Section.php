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
        return (int)$this->getData(self::ID);
    }

    public function setSectionId(int|string $id): SectionInterface
    {
        return $this->setData(self::ID, $id);
    }

    public function getIsEnable(): int
    {
        return (int)$this->getData(self::IS_ENABLE);
    }

    public function setIsEnable(int $isEnable): SectionInterface
    {
        return $this->setData(self::IS_ENABLE, $isEnable);
    }

    public function getPlanId(): ?string
    {
        return $this->getData(self::PLAN_ID);
    }

    public function setPlanId(?string $planId): SectionInterface
    {
        return $this->setData(self::PLAN_ID, $planId);
    }

    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): SectionInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getKey(): string
    {
        return $this->getData(self::KEY);
    }

    public function setKey(string $key): SectionInterface
    {
        return $this->setData(self::KEY, $key);
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

    public function getMediaGallery(): ?string
    {
        return $this->getData(self::MEDIA_GALLERY);
    }

    public function setMediaGallery(?string $mediaGallery): SectionInterface
    {
        return $this->setData(self::MEDIA_GALLERY, $mediaGallery);
    }

    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription(?string $description): SectionInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getVersion(): string
    {
        return $this->getData(self::VERSION);
    }

    public function setVersion(string $version): SectionInterface
    {
        return $this->setData(self::VERSION, $version);
    }

    public function getReleaseNote(): ?string
    {
        return $this->getData(self::RELEASE_NOTE);
    }

    public function setReleaseNote(?string $releaseNote): SectionInterface
    {
        return $this->setData(self::RELEASE_NOTE, $releaseNote);
    }

    public function getDemoLink(): ?string
    {
        return $this->getData(self::DEMO_LINK);
    }

    public function setDemoLink(?string $demoLink): SectionInterface
    {
        return $this->setData(self::DEMO_LINK, $demoLink);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(?string $createdAt): SectionInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt(?string $updatedAt): SectionInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
