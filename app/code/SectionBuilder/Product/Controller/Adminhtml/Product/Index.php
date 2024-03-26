<?php
/**
 *
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_ProductLabel
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace SectionBuilder\Product\Controller\Adminhtml\Product;

use SectionBuilder\Product\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;

class Index extends Product
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->setActiveMenu('SectionBuilder_Product::product_management');
        $page->getConfig()->getTitle()->prepend(__('Section Product'));

        return $page;
    }
}
