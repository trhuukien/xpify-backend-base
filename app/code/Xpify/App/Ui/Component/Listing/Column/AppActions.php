<?php
declare(strict_types=1);

namespace Xpify\App\Ui\Component\Listing\Column;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class AppActions extends Column
{
    const APP_PATH_EDIT = 'xpify/apps/edit';
    const APP_PATH_DELETE = 'xpify/apps/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    protected $escaper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->escaper = $escaper;
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
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        static::APP_PATH_EDIT,
                        ['id' => $item['entity_id']]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
                $appName = $this->escaper->escapeHtml($item['name']);
                $item[$this->getData('name')]['delete'] = [
                    'href' => $this->urlBuilder->getUrl(static::APP_PATH_DELETE, ['id' => $item['entity_id']]),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete %1', $appName),
                        'message' => __('Are you sure you want to delete a %1 record?', $appName),
                    ],
                    'post' => true,
                ];
            }
        }

        return $dataSource;
    }
}
