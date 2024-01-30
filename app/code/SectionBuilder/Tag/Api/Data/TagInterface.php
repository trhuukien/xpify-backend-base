<?php
declare(strict_types=1);

namespace SectionBuilder\Tag\Api\Data;

interface TagInterface
{
    const ID = 'entity_id';
    const IS_ENABLE = 'is_enable';
    const NAME = 'name';

    public function getTagId(): int;

    public function setTagId(int|string $id): self;

    public function getIsEnable(): int;

    public function setIsEnable(int|string $id): self;

    public function getName(): string;

    public function setName(string $name): self;
}
