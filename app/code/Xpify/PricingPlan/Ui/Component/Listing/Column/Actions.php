<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    const PRICING_PLAN_DELETE_PATH = 'xpify/pricingplan/delete';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource($dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $item[$name]['edit'] = [
                    'callback' => [
                        [
                            'provider' => 'customer_form.areas.bss_company_account_manage_role.'
                                . 'bss_company_account_manage_role.'
                                . 'bss_companyaccount_customer_listroles_update_modal.'
                                . 'update_bss_companyaccount_customer_listroles_form_loader',
                            'target' => 'destroyInserted',
                        ],
                        [
                            'provider' => 'customer_form.areas.bss_company_account_manage_role.'
                                . 'bss_company_account_manage_role.'
                                . 'bss_companyaccount_customer_listroles_update_modal',
                            'target' => 'openModal',
                        ],
                        [
                            'provider' => 'customer_form.areas.bss_company_account_manage_role.'
                                . 'bss_company_account_manage_role.'
                                . 'bss_companyaccount_customer_listroles_update_modal.'
                                . 'update_bss_companyaccount_customer_listroles_form_loader',
                            'target' => 'render',
                            'params' => [
                                'entity_id' => $item['entity_id'],
                            ],
                        ]
                    ],
                    'href' => '#',
                    'label' => __('Edit'),
                    'hidden' => false,
                ];

                $item[$name]['delete'] = [
                    'href' => $this->urlBuilder->getUrl(
                        self::PRICING_PLAN_DELETE_PATH,
                        ['app_id' => $item['app_id'], 'id' => $item['entity_id']]
                    ),
                    'label' => __('Delete'),
                    'isAjax' => true,
                    'confirm' => [
                        'title' => __('Delete plan'),
                        'message' => __('Are you sure you want to delete this plan?')
                    ]
                ];
            }
        }

        return $dataSource;
    }
}
