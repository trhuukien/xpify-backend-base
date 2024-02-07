<?php
declare(strict_types=1);

namespace SectionBuilder\Faq\Api\Data;

interface FaqInterface
{
    const ID = 'entity_id';
    const IS_ENABLE = 'is_enable';
    const TITLE = 'title';
    const CONTENT = 'content';

    public function getFaqId(): int;

    public function setFaqId(int|string $id): self;

    public function getIsEnable(): int;

    public function setIsEnable(int|string $isEnable): self;

    public function getTitle(): string;

    public function setTitle(string $title): self;

    public function getContent(): ?string;

    public function setContent(?string $content): self;
}
