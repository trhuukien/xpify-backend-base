<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api\Data;

interface SectionBuyInterface
{
    const ID = 'entity_id';
    const MERCHANT_SHOP = 'merchant_shop';
    const PRODUCT_ID = 'product_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DETAILS = 'details';

    public function getSectionBuyId(): int;

    public function setSectionBuyId(int|string $id): self;

    public function getMerchantShop(): string;

    public function setMerchantShop(string $merchantShop): self;

    public function getProductId(): int|string;

    public function setProductId(int|string $productId): self;

    public function getCreatedAt(): ?string;

    public function setCreatedAt(?string $createdAt): self;

    public function getUpdatedAt(): ?string;

    public function setUpdatedAt(?string $updatedAt): self;

    /**
     * Get shopify payment details [Onetime]
     *
     * @return string|null
     */
    public function getDetails(): ?string;

    /**
     * Set shopify payment details [Onetime]
     *
     * @param string|array|null $details
     * @return SectionBuyInterface
     */
    public function setDetails(mixed $details): self;
}
