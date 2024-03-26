<?php
namespace SectionBuilder\Product\Block\Adminhtml\Grid\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $imageHelper;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \SectionBuilder\Product\Model\Helper\Image $imageHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->imageHelper = $imageHelper;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $media = explode(
            \SectionBuilder\Product\Model\Helper\Image::SEPARATION,
            $row->getMediaGallery() ?? \SectionBuilder\Product\Model\Helper\Image::EMPTY_THUMBNAIL
        );

        $result = '<div class="media-group_products">';
        foreach ($media as $index => $img) {
            $img = $this->imageHelper->getBaseUrl() . str_replace(
                \SectionBuilder\Product\Model\Helper\Image::SUB_DIR,
                "",
                $img
            );
            $result .= "<div><img src='$img'/></div>";

            if ($index >= 0) { // Shows only a few images
                break;
            }
        }

        $result .= '</div>';

        return $result;
    }
}
