<?php
namespace SectionBuilder\Product\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'thumbnail';

    /**
     * @var \SectionBuilder\Product\Model\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \SectionBuilder\Product\Model\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \SectionBuilder\Product\Model\Helper\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getName();
            foreach ($dataSource['data']['items'] as &$item) {
                $filename = explode(\SectionBuilder\Product\Model\Helper\Image::SEPARATION, $item['media_gallery'] ?? '');
                $filename = str_replace(\SectionBuilder\Product\Model\Helper\Image::SUB_DIR, "", $filename[0])
                    ?: \SectionBuilder\Product\Model\Helper\Image::EMPTY_THUMBNAIL;
                $item[$fieldName . '_src'] = $this->imageHelper->getBaseUrl() . $filename;
                $item[$fieldName . '_alt'] = $item['name'];
                $item[$fieldName . '_orig_src'] = $this->imageHelper->getBaseUrl() . $filename;
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'section_builder/product/edit',
                    ['id' => $item['entity_id'], 'store' => $this->context->getRequestParam('store')]
                );
            }
        }

        return $dataSource;
    }
}
