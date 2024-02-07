<?php
namespace SectionBuilder\Faq\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use SectionBuilder\Faq\Model\FaqFactory;

class Faq extends Action
{
    public const ADMIN_RESOURCE = 'SectionBuilder_Faq::faq_management';

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $forwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \SectionBuilder\Faq\Model\FaqFactory
     */
    protected $faqFactory;

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
     * @param FaqFactory $faqFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $forwardFactory,
        PageFactory $pageFactory,
        FaqFactory $faqFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->forwardFactory = $forwardFactory;
        $this->pageFactory = $pageFactory;
        $this->faqFactory = $faqFactory;
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
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
