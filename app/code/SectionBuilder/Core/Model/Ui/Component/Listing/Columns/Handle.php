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

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \SectionBuilder\Category\Model\ResourceModel\CategoryProduct\CollectionFactory $categoryCollectionFactory,
        \SectionBuilder\Tag\Model\ResourceModel\TagProduct\CollectionFactory $tagCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function handleAction(array $dataSource, $path)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        $path,
                        ['id' => $item['entity_id']]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
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
