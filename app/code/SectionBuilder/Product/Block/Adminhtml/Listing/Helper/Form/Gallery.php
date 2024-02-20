<?php
namespace SectionBuilder\Product\Block\Adminhtml\Listing\Helper\Form;

class Gallery extends \Magento\Framework\View\Element\AbstractBlock
{
    protected $htmlId = 'media_gallery';

    protected $name = 'media_gallery';

    protected $image = 'image';

    protected $formName = 'section_builder_product_form';

    protected $sectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $sectionFactory,
        $data = []
    ) {
        $this->sectionFactory = $sectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        return $this->getContentHtml();
    }

    public function getImages()
    {
        $result = [];
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            return $result;
        }
        $gallery = [];
        $currentSection = $this->sectionFactory->create()->addFieldToFilter('entity_id', $id)->getFirstItem();
        $mediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
        if ($currentSection->getMediaGallery()) {
            $gallery = explode(
                \SectionBuilder\Product\Model\Helper\Image::SEPARATION,
                $currentSection->getMediaGallery()
            );
        }

        if (count($gallery)) {
            $result['images'] = [];
            $position = 1;
            foreach ($gallery as $image) {
                $label = str_replace(\SectionBuilder\Product\Model\Helper\Image::SUB_DIR, "", $image);
                $result['images'][] = [
                    'value_id' => $image,
                    'file' => $image,
                    'label' => $label,
                    'position' => $position,
                    'url' => $mediaUrl . $image,
                ];
                $position++;
            }
        }

        return $result;
    }

    /**
     * Prepares content block
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContentHtml()
    {
        $content = $this->getChildBlock('content');
        if (!$content) {
            $content = $this->getLayout()->createBlock(
                \SectionBuilder\Product\Block\Adminhtml\Listing\Helper\Form\Gallery\Content::class,
                'sb.gallery',
                [
                    'config' => [
                        'parentComponent' => 'section_builder_product_form.section_builder_product_form.block_gallery.block_gallery'
                    ]
                ]
            );
        }

        $content
            ->setId($this->getHtmlId() . '_content')
            ->setElement($this)
            ->setFormName($this->formName);
        $galleryJs = $content->getJsObjectName();
        $content->getUploader()->getConfig()->setMediaGallery($galleryJs);
        return $content->toHtml();
    }

    /**
     * @return string
     */
    protected function getHtmlId()
    {
        return $this->htmlId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return $this->getElementHtml();
    }
}
