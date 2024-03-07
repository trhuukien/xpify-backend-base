<?php
namespace SectionBuilder\Core\Model;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SYSTEM_XML_APP_CONNECTING = 'section_builder/app/connecting';
    const SYSTEM_XML_API_BASE_URL = 'section_builder/api_management/base_url';
    const SYSTEM_XML_API_TOKEN = 'section_builder/api_management/token';
    const SYSTEM_XML_API_REF = 'section_builder/api_management/ref';
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

    public function getApiBaseUrl()
    {
        $path = $this->scopeConfig->getValue(
            self::SYSTEM_XML_API_BASE_URL
        );

        return rtrim($path ?? '', '/');
    }

    public function getApiToken()
    {
        return $this->scopeConfig->getValue(
            self::SYSTEM_XML_API_TOKEN
        );
    }

    public function getApiRef()
    {
        return $this->scopeConfig->getValue(
            self::SYSTEM_XML_API_REF
        );
    }

    public function getFileBaseSrc()
    {
        return $this->scopeConfig->getValue(
            self::SYSTEM_XML_FILE_BASE
        );
    }
}
