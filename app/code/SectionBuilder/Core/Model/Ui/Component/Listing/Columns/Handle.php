<?php
namespace SectionBuilder\Core\Model\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Handle extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    protected $categoryCollectionFactory;

    protected $tagCollectionFactory;

    protected $timezone;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \SectionBuilder\Category\Model\ResourceModel\CategoryProduct\CollectionFactory $categoryCollectionFactory,
        \SectionBuilder\Tag\Model\ResourceModel\TagProduct\CollectionFactory $tagCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->timezone = $timezone;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function addActionEdit(array $dataSource, $path)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        $path,
                        ['id' => $item['entity_id']]
                    ),
                    'label' => __('Edit')
                ];
            }
        }

        return $dataSource;
    }

    public function addActionDelete(array $dataSource, $path)
    {
        $currentTime = $this->timezone->date();

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                /* Restrict product deletion */
                if ($item['qty_installed'] || !$item['created_at']) {
                    continue;
                }
                if ($currentTime->getTimestamp() - strtotime($item['created_at']) > 3600) {
                    continue;
                }

                $item[$this->getData('name')]['delete'] = [
                    'href' => $this->urlBuilder->getUrl($path, ['id' => $item['entity_id']]),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete "%1"', $item['name']),
                        'message' => __('Are you sure you want to delete a "%1"?', $item['name'])
                    ]
                ];
            }
        }

        return $dataSource;
    }

    public function handleCountProduct($dataSource, $type)
    {
        if (isset($dataSource['data']['items'])) {
            if ($type === 'category_id') {
                $collection = $this->categoryCollectionFactory->create();
            } else {
                $collection = $this->tagCollectionFactory->create();
            }

            $collection->addFieldToSelect($type);
            $collection->addExpressionFieldToSelect(
                'count',
                "COUNT($type)",
                $type
            );
            $collection->getSelect()->group($type);
            $data = $collection->getData();
            foreach ($data as $datum) {
                $count[$datum[$type]] = $datum['count'];
            }

            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $count[$item['entity_id']] ?? 0;
            }
        }

        return $dataSource;
    }
}
