<?php
namespace SectionBuilder\Core\Model\Block\Adminhtml\System\Config\Disable;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class ConnectingApp extends \SectionBuilder\Core\Model\Block\Adminhtml\System\Config\Disable
{
    protected $configData;

    public function __construct(
        \SectionBuilder\Core\Model\Config $configData,
        Context $context,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $this->configData = $configData;
        parent::__construct($context, $data, $secureRenderer);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $appId = $this->configData->getAppConnectingId();
        if ($appId) {
            return parent::_getElementHtml($element);
        }

        return $element->getElementHtml();
    }
}
