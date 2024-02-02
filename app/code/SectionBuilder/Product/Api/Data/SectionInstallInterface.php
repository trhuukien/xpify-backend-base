<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api\Data;

interface SectionInstallInterface
{
    const ID = 'entity_id';
    const MERCHANT_SHOP = 'merchant_shop';
    const PRODUCT_ID = 'product_id';
    const THEME_IDS = 'theme_ids';

    public function getSectionInstallId(): int;

    public function setSectionInstallId(int|string $id): self;

    public function getMerchantShop(): string;

    public function setMerchantShop(string $merchantShop): self;

    public function getProductId(): int|string;

    public function setProductId(int|string $productId): self;

    public function getThemeIds(): int|string;

    public function setThemeIds(int|string $themeIds): self;
}
