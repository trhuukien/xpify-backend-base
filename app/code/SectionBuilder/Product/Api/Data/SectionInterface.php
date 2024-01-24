<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api\Data;

interface SectionInterface
{
    const ID = 'entity_id';
    const NAME = 'name';
    const SOURCE = 'source';
    const FILE_DATA = 'file_data';

    public function getSectionId(): int;

    public function setSectionId(int|string $id): self;

    public function getName(): string;

    public function setName(string $name): self;

    public function getSrc(): string;

    public function setSrc(string $src): self;

    public function getFileData(): ?string;

    public function setFileData(?string $fileData): self;
}
