<?php
namespace SectionBuilder\Product\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use SectionBuilder\Product\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;
use SectionBuilder\Product\Model\SectionFactory;

class MassUpdate extends Product
{
    protected $filter;

    protected $dataProvider;

    public function __construct(
        Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $forwardFactory,
        PageFactory $pageFactory,
        SectionFactory $sectionFactory,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $collectionFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \SectionBuilder\Product\Model\DataProvider $dataProvider
    ) {
        parent::__construct($context, $forwardFactory, $pageFactory, $sectionFactory, $collectionFactory, $logger);
        $this->filter = $filter;
        $this->dataProvider = $dataProvider;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $productIds = array_column($collection->getData(), 'entity_id');
        $this->dataProvider->setProductIdsUpdating(implode(",", $productIds));
        $title = __('Mass Updating');
        $page = $this->pageFactory->create();
        $page->setActiveMenu('SectionBuilder_Product::product_management');
        $page->getConfig()->getTitle()->prepend($title);
        return $page;
    }
}
