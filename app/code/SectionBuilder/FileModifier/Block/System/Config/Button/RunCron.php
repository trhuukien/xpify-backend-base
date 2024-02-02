<?php
namespace SectionBuilder\FileModifier\Block\System\Config\Button;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class RunCron extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'SectionBuilder_FileModifier::system/config/button_cron.phtml';

    protected $url;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        Context $context,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $this->url = $url;
        parent::__construct($context, $data, $secureRenderer);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    public function getAjaxUrl()
    {
        return $this->url->getUrl('section_builder/cron/base');
    }

    protected function _getElementHtml($element)
    {
        return $this->_toHtml();
    }
}
