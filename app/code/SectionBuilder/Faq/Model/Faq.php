<?php
declare(strict_types=1);

namespace SectionBuilder\Faq\Model;

use SectionBuilder\Faq\Api\Data\FaqInterface;

class Faq extends \Magento\Framework\Model\AbstractModel implements FaqInterface
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SectionBuilder\Faq\Model\ResourceModel\Faq::class);
    }

    public function getFaqId(): int
    {
        return (int)$this->getData(self::ID);
    }

    public function setFaqId(int|string $id): FaqInterface
    {
        return $this->setData(self::ID, $id);
    }

    public function getIsEnable(): int
    {
        return (int)$this->getData(self::IS_ENABLE);
    }

    public function setIsEnable(int|string $isEnable): FaqInterface
    {
        return $this->setData(self::IS_ENABLE, $isEnable);
    }

    public function getTitle(): string
    {
        return $this->getData(self::TITLE);
    }

    public function setTitle(string $title): FaqInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getContent(): ?string
    {
        return $this->getData(self::CONTENT);
    }

    public function setContent(?string $content): FaqInterface
    {
        return $this->setData(self::CONTENT, $content);
    }
}
