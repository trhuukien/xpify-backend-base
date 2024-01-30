<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api;

use SectionBuilder\Product\Api\Data\SectionInstallInterface;

interface SectionInstallRepositoryInterface
{
    public function get(string $field, int|string $value);

    public function save(SectionInstallInterface $sectionInstall);

    public function delete(SectionInstallInterface $sectionInstall);
}
