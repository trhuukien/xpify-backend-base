<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model;

use SectionBuilder\Product\Api\Data\SectionInstallInterface;

class SectionInstall extends \Magento\Framework\Model\AbstractModel implements SectionInstallInterface
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SectionBuilder\Product\Model\ResourceModel\SectionInstall::class);
    }

    public function getSectionInstallId(): int
    {
        return (int)$this->getData(self::ID);
    }

    public function setSectionInstallId(int|string $id): SectionInstallInterface
    {
        return $this->setData(self::ID, $id);
    }

    public function getMerchantShop(): string
    {
        return $this->getData(self::MERCHANT_SHOP);
    }

    public function setMerchantShop(string $merchantShop): SectionInstallInterface
    {
        return $this->setData(self::MERCHANT_SHOP, $merchantShop);
    }

    public function getProductId(): int|string
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function setProductId(int|string $productId): SectionInstallInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    public function getThemeIds(): int|string
    {
        return $this->getData(self::THEME_IDS);
    }

    public function setThemeIds(int|string $themeIds): SectionInstallInterface
    {
        return $this->setData(self::THEME_IDS, $themeIds);
    }
}
