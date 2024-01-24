<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api\Data;

interface BuyInterface
{
    const ID = 'entity_id';
    const MERCHANT_SHOP = 'merchant_shop';
    const PRODUCT_ID = 'product_id';
    const INSTALL = 'install';

    public function getBuyId(): int;

    public function setBuyId(int|string $id): self;

    public function getMerchantShop(): string;

    public function setMerchantShop(string $merchantShop): self;

    public function getProductId(): int;

    public function setProductId(int|string $productId): self;

    public function getInstall(): string;

    public function setInstall(string $install): self;
}
