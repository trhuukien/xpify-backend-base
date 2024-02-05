<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api\Data;

interface SectionInterface
{
    const ID = 'entity_id';
    const IS_ENABLE = 'is_enable';
    const PLAN_ID = 'plan_id';
    const NAME = 'name';
    const PRICE = 'price';
    const SRC = 'src';
    const FILE_DATA = 'file_data';

    public function getSectionId(): int;

    public function setSectionId(int|string $id): self;

    public function getIsEnable(): int;

    public function setIsEnable(int $isEnable): self;

    public function getPlanId(): ?string;

    public function setPlanId(?string $planId): self;

    public function getName(): string;

    public function setName(string $name): self;

    public function getPrice(): float;

    public function setPrice(float $price): self;

    public function getSrc(): string;

    public function setSrc(string $src): self;

    public function getFileData(): ?string;

    public function setFileData(?string $fileData): self;
}
