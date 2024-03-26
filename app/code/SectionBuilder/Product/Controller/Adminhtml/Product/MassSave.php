<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Controller\Adminhtml\Product;

class MassSave extends \Magento\Backend\App\Action
{
    public $productCollection;

    protected $filter;

    protected $collectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPost()->toArray();
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        $productIds = $postData['general']['product_ids'] ? explode(",", $postData['general']['product_ids']) : [];
        $data = [];
        foreach ($postData['use_default'] as $column => $isUseDefault) {
            if ($isUseDefault) {
                continue;
            }

            $data[$column] = $postData['general'][$column] ?? $postData['content'][$column] ?? '';
        }

        if (!$data || !$productIds) {
            $this->messageManager->addWarningMessage(
                __('No record have been updated')
            );

            return $resultRedirect->setPath('*/*/');
        }

        $collection = $this->collectionFactory->create();
        $collection = $collection->addFieldToFilter('entity_id', $productIds);
        $collectionSize = $collection->getSize();

        if (!$collectionSize) {
            $this->messageManager->addErrorMessage(
                __('Product not found')
            );

            return $resultRedirect->setPath('*/*/');
        }

        foreach ($collection as $section) {
            $isGroup = $section->getTypeId() === \SectionBuilder\Product\Model\Config\Source\ProductType::GROUP_TYPE_ID;
            foreach ($data as $column => $val) {
                if ($column === 'plan_id' && $isGroup) {
                    continue;
                }
                $val = ($val === "") ? null : $val;
                $section->setData($column, $val);
            }
            $section->save();
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been updated.', $collectionSize)
        );

        return $resultRedirect->setPath('*/*/');
    }
}
