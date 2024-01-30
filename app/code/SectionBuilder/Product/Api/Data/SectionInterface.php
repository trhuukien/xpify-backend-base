<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api\Data;

interface SectionInterface
{
    const ID = 'entity_id';
    const PLAN_IDS = 'plan_ids';
    const NAME = 'name';
    const PRICE = 'price';
    const SRC = 'src';
    const FILE_DATA = 'file_data';

    public function getSectionId(): int;

    public function setSectionId(int|string $id): self;

    public function getPlanIds(): string;

    public function setPlanIds(string $planIds): self;

    public function getName(): string;

    public function setName(string $name): self;

    public function getPrice(): float;

    public function setPrice(float $price): self;

    public function getSrc(): string;

    public function setSrc(string $src): self;

    public function getFileData(): ?string;

    public function setFileData(?string $fileData): self;
}
