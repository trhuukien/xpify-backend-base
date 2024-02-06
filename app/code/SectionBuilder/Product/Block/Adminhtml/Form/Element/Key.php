<?php
namespace SectionBuilder\Product\Block\Adminhtml\Form\Element;

class Key extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    public function getElementHtml()
    {
        $disabled = $this->getDisabled();
        if ($disabled === 'is_enable') {
            return parent::getElementHtml();
        } else {
            $html = '<input id="' . $this->getHtmlId() . '" name="' . $this->getName() . '" value="' . $this->getEscapedValue() . '" ' . ($this->getDisabled() ? 'disabled' : '') . ' title="' . $this->getTitle() . '" ' . $this->serialize($this->getHtmlAttributes()) . '/>';
            return $html;
        }
    }
}
