<?php
declare(strict_types=1);

namespace SectionBuilder\Category\Api\Data;

interface CategoryInterface
{
    const ID = 'entity_id';
    const IS_ENABLE = 'is_enable';
    const NAME = 'name';

    public function getCategoryId(): int;

    public function setCategoryId(int|string $id): self;

    public function getIsEnable(): int;

    public function setIsEnable(int|string $isEnable): self;

    public function getName(): string;

    public function setName(string $name): self;
}
