<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api\Data;

interface SectionInstallInterface
{
    const ID = 'entity_id';
    const MERCHANT_SHOP = 'merchant_shop';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_VERSION = 'product_version';
    const THEME_ID = 'theme_id';

    public function getSectionInstallId(): int;

    public function setSectionInstallId(int|string $id): self;

    public function getMerchantShop(): string;

    public function setMerchantShop(string $merchantShop): self;

    public function getProductId(): int|string;

    public function setProductId(int|string $productId): self;

    public function getProductVersion(): string;

    public function setProductVersion(string $productVersion): self;

    public function getThemeId(): string;

    public function setThemeId(string $themeIds): self;
}
