<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api\Data;

interface SectionInterface
{
    const ID = 'entity_id';
    const IS_ENABLE = 'is_enable';
    const PLAN_ID = 'plan_id';
    const NAME = 'name';
    const KEY = 'url_key';
    const PRICE = 'price';
    const SRC = 'src';
    const PATH_SOURCE = 'path_source';
    const MEDIA_GALLERY = 'media_gallery';
    const DESCRIPTION = 'description';
    const TYPE_ID = 'type_id';
    const CHILD_IDS = 'child_ids';
    const QTY_SOLD = 'qty_sold';
    const QTY_INSTALLED = 'qty_installed';
    const VERSION = 'version';
    const RELEASE_NOTE = 'release_note';
    const DEMO_LINK = 'demo_link';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function getSectionId(): int;

    public function setSectionId(int|string $id): self;

    public function getIsEnable(): int;

    public function setIsEnable(int $isEnable): self;

    public function getPlanId(): ?string;

    public function setPlanId(?string $planId): self;

    public function getName(): string;

    public function setName(string $name): self;

    public function getKey(): string;

    public function setKey(string $key): self;

    public function getPrice(): float;

    public function setPrice(float $price): self;

    public function getSrc(): ?string;

    public function setSrc(?string $src): self;

    public function getPathSource(): ?string;

    public function setPathSource(?string $urlSource): self;

    public function getMediaGallery(): ?string;

    public function setMediaGallery(?string $mediaGallery): self;

    public function getDescription(): ?string;

    public function setDescription(?string $description): self;

    public function getTypeId(): int;

    public function setTypeId(int $typeId): self;

    public function getChildIds(): ?string;

    public function setChildIds(?string $childIds): self;

    public function getQtySold(): int;

    public function setQtySold(int $qtySold): self;

    public function getQtyInstalled(): int;

    public function setQtyInstalled(int $qtyInstall): self;

    public function getVersion(): string;

    public function setVersion(string $version): self;

    public function getReleaseNote(): ?string;

    public function setReleaseNote(?string $releaseNote): self;

    public function getDemoLink(): ?string;

    public function setDemoLink(?string $demoLink): self;

    public function getCreatedAt(): ?string;

    public function setCreatedAt(?string $createdAt): self;

    public function getUpdatedAt(): ?string;

    public function setUpdatedAt(?string $updatedAt): self;
}
