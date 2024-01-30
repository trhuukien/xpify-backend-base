<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model;

use SectionBuilder\Product\Api\Data\SectionInstallInterface;

class SectionInstallRepository implements \SectionBuilder\Product\Api\SectionInstallRepositoryInterface
{
    protected $sectionInstall;

    protected $sectionInstallFactory;

    protected $collectionFactory;

    protected $collectionProcessor;

    public function __construct(
        \SectionBuilder\Product\Model\ResourceModel\SectionInstall $sectionInstall,
        \SectionBuilder\Product\Model\SectionInstallFactory $sectionInstallFactory,
        \SectionBuilder\Product\Model\ResourceModel\SectionInstall\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->sectionInstall = $sectionInstall;
        $this->sectionInstallFactory = $sectionInstallFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function create()
    {
        return $this->sectionInstallFactory->create();
    }

    public function get(string $field, int|string $value)
    {
        try {
            $sectionInstall = $this->create();
            $this->sectionInstall->load($sectionInstall, $value, $field);

            if (!$sectionInstall->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Section Install does not exist.'));
            }
            return $sectionInstall;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__($e->getMessage()));
        }
    }

    public function save(SectionInstallInterface $sectionInstall)
    {
        try {
            $this->sectionInstall->save($sectionInstall);
            return $sectionInstall;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($e->getMessage()));
        }
    }

    public function delete(SectionInstallInterface $sectionInstall)
    {
        try {
            $this->sectionInstall->delete($sectionInstall);
            return true;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($e->getMessage()));
        }
    }
}
