<?php
declare(strict_types=1);

namespace SectionBuilder\FileModifier\Cron;

class Plan
{
    protected $configData;

    protected $authValidation;

    protected $sectionInstallFactory;

    protected $merchantRepository;

    protected $appRepository;

    protected $sectionInstallRepository;

    protected $contextInitializer;

    protected $merchantList = [];

    protected $hasPlanList = [];

    public function __construct(
        \SectionBuilder\Core\Model\Config $configData,
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\ResourceModel\SectionInstall\CollectionFactory $sectionInstallFactory,
        \Xpify\Merchant\Api\MerchantRepositoryInterface $merchantRepository,
        \Xpify\App\Model\AppRepository $appRepository,
        \Xpify\Core\Helper\ShopifyContextInitializer $contextInitializer,
        \SectionBuilder\Product\Model\SectionInstallRepository $sectionInstallRepository
    ) {
        $this->configData = $configData;
        $this->authValidation = $authValidation;
        $this->sectionInstallFactory = $sectionInstallFactory;
        $this->merchantRepository = $merchantRepository;
        $this->appRepository = $appRepository;
        $this->contextInitializer = $contextInitializer;
        $this->sectionInstallRepository = $sectionInstallRepository;
    }

    public function process($canChange = false)
    {
        $result = [
            'alert' => '',
            'execute' => 0,
            'not_handle' => 0,
            'success' => 0,
            'error' => 0
        ];
        return $result;

        $appId = $this->configData->getAppConnectingId();
        $file = $this->configData->getFileBaseSrc();

        if (!$appId || !$file) {
            $result['alert'] = __('Not connected to the app or File src is empty!');
            return $result;
        }

        try {
            $collection = $this->sectionInstallFactory->create();
            $columnProduct = [
                'src',
                'product_name' => 'p.name',
                'plan_id'
            ];
            if ($canChange) {
                $columnProduct[] = 'version';
                $columnProduct[] = 'file_data';
            }

            $collection->join(
                ['xm' => \Xpify\Merchant\Model\ResourceModel\Merchant::MAIN_TABLE],
                'main_table.merchant_shop = xm.shop',
                [
                    'app_id',
                    'merchant_id' => 'xm.entity_id'
                ]
            )->join(
                ['p' => \SectionBuilder\Product\Model\ResourceModel\Section::MAIN_TABLE],
                'main_table.product_id = p.entity_id',
                $columnProduct
            )->join(
                ['xpp' => \Xpify\PricingPlan\Model\ResourceModel\PricingPlan::MAIN_TABLE],
                'p.plan_id = xpp.entity_id',
                [
                    'plan_code' => 'xpp.code'
                ]
            );
            $collection->addFieldToFilter('xm.app_id', $appId);
            $collection->addFieldToFilter('p.src', $file);
            $data = $collection->getData();
            $result['execute'] = count($data);

            $app = $this->appRepository->get($appId);
            $this->contextInitializer->initialize($app);
            $apiVersion = $app->getApiVersion();
        } catch (\Exception $e) {
            $result['alert'] = $e->getMessage();
            return $result;
        }

        foreach ($data as $item) {
            $merchantId = (int)$item['merchant_id'];
            $this->merchantList[$merchantId] = $this->merchantList[$merchantId]
                ?? $this->merchantRepository->getById($merchantId);
            $this->hasPlanList[$merchantId] = $this->hasPlanList[$merchantId]
                ?? $this->authValidation->hasPlan($this->merchantList[$merchantId], $item['plan_code']);

            if (!$this->hasPlanList[$merchantId]) {
                $response = $this->merchantList[$merchantId]->getRest()->delete(
                    '/admin/api/' . $apiVersion . '/themes/' . $item['theme_id'] . '/assets.json',
                    [],
                    ['asset[key]' => $file]
                );
                $message = $response->getDecodedBody();
                if (isset($message['errors'])) {
                    ++$result['error'];
                } else {
                    ++$result['success'];

                    $installRepository = $this->sectionInstallRepository->get('entity_id', (int)$item['entity_id']);
                    $this->sectionInstallRepository->delete($installRepository);
                }
            } else {
                if ($canChange && $item['version'] !== $item['product_version']) {
                    $themeId = $item['theme_id'];
                    $response = $this->merchantList[$merchantId]->getRest()->put(
                        "/admin/api/$apiVersion/themes/$themeId/assets.json",
                        null,
                        [],
                        [
                            'asset[key]' => $item['src'],
                            'asset[value]' => $item['file_data']
                        ]
                    );

                    $message = $response->getDecodedBody();
                    if (isset($message['errors'])) {
                        ++$result['error'];
                    } else {
                        ++$result['success'];

                        $installRepository = $this->sectionInstallRepository->get('entity_id', (int)$item['entity_id']);
                        $installRepository->setProductVersion($item['version']);
                        $this->sectionInstallRepository->save($installRepository);
                    }
                } else {
                    ++$result['not_handle'];
                }
            }
        }

        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->info('Kiz log system.log: Cron');

        return $result;
    }
}
