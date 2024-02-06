<?php
namespace SectionBuilder\Product\Block\Adminhtml\Listing\Helper\Form\Gallery;

use Magento\Framework\View\Element\AbstractBlock;

class Content extends \Magento\Backend\Block\Widget
{
    protected $_template = 'form/gallery.phtml';

    protected $serializer;

    protected $imageUploadConfigDataProvider;

    /**
     * Content constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Serialize\SerializerInterface $jsonEncoder
     * @param \Magento\Backend\Block\DataProviders\ImageUploadConfig $imageUploadConfigDataProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Backend\Block\DataProviders\ImageUploadConfig $imageUploadConfigDataProvider,
        array $data = []
    ) {
        $this->serializer = $serializer;
        $this->imageUploadConfigDataProvider = $imageUploadConfigDataProvider;
        parent::__construct($context, $data);
    }

    /**
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'uploader',
            \Magento\Backend\Block\Media\Uploader::class,
            ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
        );

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->getUrl('section_builder/listing_upload/gallery')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );

        return parent::_prepareLayout();
    }

    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * @return string
     */
    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            __('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    /**
     * @return string
     */
    public function getImagesJson()
    {
        $value = $this->getElement()->getImages();

        if (is_array($value) &&
            array_key_exists('images', $value) &&
            is_array($value['images']) &&
            count($value['images'])
        ) {
            $images = $this->sortImagesByPosition($value['images']);

            return $this->serializer->serialize($images);
        }

        return '[]';
    }

    /**
     * Sort images array by position key
     *
     * @param array $images
     * @return array
     */
    private function sortImagesByPosition($images)
    {
        if (is_array($images)) {
            usort($images, function ($imageA, $imageB) {
                return ($imageA['position'] < $imageB['position']) ? -1 : 1;
            });
        }

        return $images;
    }
}
