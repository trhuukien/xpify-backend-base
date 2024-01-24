<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model;

use SectionBuilder\Product\Api\Data\BuyInterface;

class Buy extends \Magento\Framework\Model\AbstractModel implements BuyInterface
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SectionBuilder\Product\Model\ResourceModel\Buy::class);
    }

    public function getBuyId(): int
    {
        return $this->getData(self::ID);
    }

    public function setBuyId(int|string $id): BuyInterface
    {
        return $this->setData(self::ID, $id);
    }

    public function getMerchantShop(): string
    {
        return $this->getData(self::MERCHANT_SHOP);
    }

    public function setMerchantShop(string $merchantShop): BuyInterface
    {
        return $this->setData(self::MERCHANT_SHOP, $merchantShop);
    }

    public function getProductId(): int
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function setProductId(int|string $productId): BuyInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    public function getInstall(): string
    {
        return $this->getData(self::MERCHANT_SHOP);
    }

    public function setInstall(string $install): BuyInterface
    {
        return $this->setData(self::INSTALL, $install);
    }
}
