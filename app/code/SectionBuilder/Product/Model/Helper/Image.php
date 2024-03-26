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

    protected $fileDriver;

    protected $filesystem;

    protected $mediaDirectory;

    protected $coreFileStorageDatabase;

    public function __construct(
        UrlInterface $urlBuilder,
        Filesystem $fileSystem,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->fileSystem = $fileSystem;
        $this->fileDriver = $fileDriver;
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
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

    public function uploadMediaGallery($media)
    {
        $gallery = [];

        if (!empty($media['images'])) {
            $images = $media['images'];
            $bannerimageDirPath = $this->mediaDirectory->getAbsolutePath("section_builder/product");
            $tmp = $this->mediaDirectory->getAbsolutePath(\SectionBuilder\Product\Model\Helper\Image::SUB_DIR . "tmp");
            if (!$this->fileDriver->isExists($bannerimageDirPath)) {
                $this->fileDriver->createDirectory($bannerimageDirPath);
                $this->fileDriver->createDirectory($tmp);
            }
            foreach ($images as $image) {
                if (empty($image['removed'])) {
                    try {
                        if (!empty($image['value_id'])) {
                            $gallery[] = $image['value_id'];
                        } elseif (!empty($image['file'])) {
                            $originalImageName = $image['file'];
                            $imageName = $originalImageName;
                            $basePath = "section_builder/product";
                            $baseTmpImagePath = "catalog/tmp/category/" . $imageName;
                            $baseImagePath = $basePath . "/" . $imageName;
                            $mediaPath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
                            $baseImageAbsolutePath = $mediaPath . $baseImagePath;
                            $i = 1;
                            while (file_exists($baseImageAbsolutePath)) {
                                $i++;
                                $p = mb_strrpos($originalImageName, '.');
                                if (false !== $p) {
                                    $imageName = mb_substr($originalImageName, 0, $p) . $i . mb_substr($originalImageName, $p);
                                } else {
                                    $imageName = $originalImageName . $i;
                                }
                                $baseImagePath = $basePath . "/" . $imageName;
                                $baseImageAbsolutePath = $mediaPath . $baseImagePath;
                            }
                            $this->coreFileStorageDatabase->copyFile(
                                $baseTmpImagePath,
                                $baseImagePath
                            );
                            $this->mediaDirectory->renameFile(
                                $baseTmpImagePath,
                                $baseImagePath
                            );

                            $gallery[] = $baseImagePath;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        return $gallery;
    }
}
