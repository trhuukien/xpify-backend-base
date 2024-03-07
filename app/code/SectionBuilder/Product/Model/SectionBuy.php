<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model;

use SectionBuilder\Product\Api\Data\SectionBuyInterface;

class SectionBuy extends \Magento\Framework\Model\AbstractModel implements SectionBuyInterface
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SectionBuilder\Product\Model\ResourceModel\SectionBuy::class);
    }

    public function getSectionBuyId(): int
    {
        return (int)$this->getData(self::ID);
    }

    public function setSectionBuyId(int|string $id): SectionBuyInterface
    {
        return $this->setData(self::ID, $id);
    }

    public function getMerchantShop(): string
    {
        return $this->getData(self::MERCHANT_SHOP);
    }

    public function setMerchantShop(string $merchantShop): SectionBuyInterface
    {
        return $this->setData(self::MERCHANT_SHOP, $merchantShop);
    }

    public function getProductId(): int|string
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function setProductId(int|string $productId): SectionBuyInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(?string $createdAt): SectionBuyInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt(?string $updatedAt): SectionBuyInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    public function getDetails(): ?string
    {
        return $this->getData(self::DETAILS);
    }

    /**
     * @inheritDoc
     */
    public function setDetails(mixed $details): SectionBuyInterface
    {
        if (is_array($details)) {
            $details = json_encode($details);
        }
        return $this->setData(self::DETAILS, $details);
    }
}
