<?php
namespace SectionBuilder\Core\Model;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SYSTEM_XML_APP_CONNECTING = 'section_builder/app/connecting';
    const SYSTEM_XML_FILE_BASE = 'section_builder/file_management/src';

    protected $messageManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->messageManager = $messageManager;
    }

    public function getAppConnectingId($throw = false)
    {
        $appId = (int)$this->scopeConfig->getValue(
            self::SYSTEM_XML_APP_CONNECTING
        );

        if (!$appId && $throw) {
            $this->messageManager->addErrorMessage(__("Please config app connecting in Stores > Configuration > Shopify App > App > Connecting"));
        }

        return $appId;
    }

    public function getFileBaseSrc()
    {
        return $this->scopeConfig->getValue(
            self::SYSTEM_XML_FILE_BASE
        );
    }
}
