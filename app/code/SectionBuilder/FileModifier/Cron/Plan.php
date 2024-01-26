<?php
declare(strict_types=1);

namespace SectionBuilder\FileModifier\Cron;

use SectionBuilder\Core\Model\Auth\Validation;

class Plan
{
    const APP_SECTION_BUILDER_ID = 1;
    const FILE_BASE_CSS = 'sections/bss-section-builder.css';

    protected $validation;

    protected $subcriptionFactory;

    protected $sectionInstallFactory;

    protected $merchantRepository;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $validation,
        \Xpify\Merchant\Model\ResourceModel\Subscription\CollectionFactory $subcriptionFactory,
        \SectionBuilder\Product\Model\ResourceModel\SectionInstall\CollectionFactory $sectionInstallFactory,
        \Xpify\Merchant\Api\MerchantRepositoryInterface $merchantRepository
    ) {
        $this->validation = $validation;
        $this->subcriptionFactory = $subcriptionFactory;
        $this->sectionInstallFactory = $sectionInstallFactory;
        $this->merchantRepository = $merchantRepository;
    }

    public function process()
    {
        $collection = $this->subcriptionFactory->create();
        $collection->addFieldToFilter('app_id', self::APP_SECTION_BUILDER_ID);
        $merchantSubscription = $collection->getData();
        $this->execute($merchantSubscription);
    }

    public function execute($merchantSubscription)
    {
        $apiVersion = \Shopify\Context::$API_VERSION;

        foreach ($merchantSubscription as $subscription) {
            $merchant = $this->merchantRepository->getById((int)$subscription['merchant_id']);
            $currentPeriodEnd = $this->validation->checkAuth($merchant);

            $response = $merchant->getGraphql()->query(self::RECURRING_PURCHASES_QUERY);
            $responseBody = $response->getDecodedBody();
            $currentPeriodEnd = $responseBody["data"]["currentAppInstallation"]["activeSubscriptions"][0]["currentPeriodEnd"] ?? null;

            if ($currentPeriodEnd === null) {
                $collection = $this->sectionInstallFactory->create();
                $collection->addFieldToFilter('merchant_shop', $merchant->getShop());
                $collection->join(
                    ['sbp' => \SectionBuilder\Product\Model\ResourceModel\Section::MAIN_TABLE],
                    'main_table.product_id = sbp.entity_id',
                    ['sbp.src']
                );
                $collection->addFieldToFilter('sbp.src', self::FILE_BASE_CSS);
                $sectionInstallData = $collection->getFirstItem()->getData();

                if (isset($sectionInstallData['install'])) {
                    $themeInstall = explode(",", $sectionInstallData['install']);
                    foreach ($themeInstall as $themeId) {
                        $merchant->getRest()->delete(
                            '/admin/api/' . $apiVersion . '/themes/' . $themeId . '/assets.json',
                            [],
                            ['asset[key]' => self::FILE_BASE_CSS]
                        );
                    }
                }
            }
        }
    }

    private const RECURRING_PURCHASES_QUERY = <<<'QUERY'
        query appSubscription {
            currentAppInstallation {
                activeSubscriptions {
                    name
                    test
                    currentPeriodEnd
                    trialDays
                }
            }
        }
    QUERY;
}
