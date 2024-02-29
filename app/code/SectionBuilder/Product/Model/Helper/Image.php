<?php
namespace SectionBuilder\Product\Model\Helper;

use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;

class Image
{
    const SEPARATION = ";";
    const EMPTY_THUMBNAIL = "empty_thumbnail.jpg";
    const SUB_DIR = "section_builder/product/";

    protected $urlBuilder;

    protected $fileSystem;

    /**
     * @param UrlInterface $urlBuilder
     * @param Filesystem $fileSystem
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Filesystem $fileSystem
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->fileSystem = $fileSystem;
    }

    /**
     * get images base url
     *
     * @return string
     */
    public function getBaseUrl($urlSuffix = self::SUB_DIR)
    {
        return $this->urlBuilder->getBaseUrl(
            ['_type' => UrlInterface::URL_TYPE_MEDIA]
        ) . $urlSuffix;
    }
}
