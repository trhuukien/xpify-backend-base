<?php
namespace SectionBuilder\Category\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use SectionBuilder\Category\Model\CategoryFactory;

class Category extends Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $forwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \SectionBuilder\Category\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Label constructor.
     *
     * @param Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $forwardFactory
     * @param PageFactory $pageFactory
     * @param CategoryFactory $categoryFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $forwardFactory,
        PageFactory $pageFactory,
        CategoryFactory $categoryFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->forwardFactory = $forwardFactory;
        $this->pageFactory = $pageFactory;
        $this->categoryFactory = $categoryFactory;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->pageFactory->create();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SectionBuilder_Category::category_management');
    }
}
