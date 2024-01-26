<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api;

use SectionBuilder\Product\Api\Data\SectionInstallInterface;

interface SectionInstallRepositoryInterface
{
    public function getById(int|string $id);

    public function save(SectionInstallInterface $sectionInstall);

    public function delete(SectionInstallInterface $sectionInstall);

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria): SectionInstallInterface;
}
