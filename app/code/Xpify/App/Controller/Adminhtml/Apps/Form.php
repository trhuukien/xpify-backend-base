<?php
declare(strict_types=1);

namespace Xpify\App\Controller\Adminhtml\Apps;

use Magento\Backend\App\Action\Context;
use Xpify\App\Model\AppFactory as FApp;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Form extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    /**
     * @var FApp
     */
    private $appFactory;
    private \Magento\Framework\Registry $registry;
    private \Magento\Framework\View\Result\PageFactory $pageFactory;

    /**
     * @param Context $context
     * @param FApp $shopFactory
     * @param Registry $registry
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        FApp $appFactory,
        \Magento\Framework\Registry $registry,
        // inject page factory
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->appFactory = $appFactory;
        $this->registry = $registry;
        $this->pageFactory = $pageFactory;
    }

    /**
     * Render form page
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // Create emtpty shop
        $shop = $this->appFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            $shop->load($id);
        }

        $title = __('New App');
        if (isset($shop) && $shop->getId()) {
            $title = __("Edit: %1", $shop->getShopDomain());
        }
        $this->registry->register('current_app', $shop);
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend($title);
        return $page;
    }
}
