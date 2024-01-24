<?php
declare(strict_types=1);

namespace SectionBuilder\FileModifier\Cron;

use SectionBuilder\Product\Model\ResourceModel\Buy\Collection;

class Plan
{
    const APP_SECTION_BUILDER_ID = 1;
    const FILE_BASE_CSS = 'sections/bss-section-builder.css';

    protected $subcriptionFactory;

    protected $buyFactory;

    protected $merchantRepository;

    public function __construct(
        \Xpify\Merchant\Model\ResourceModel\Subscription\CollectionFactory $subcriptionFactory,
        \SectionBuilder\Product\Model\ResourceModel\Buy\CollectionFactory $buyFactory,
        \Xpify\Merchant\Api\MerchantRepositoryInterface $merchantRepository
    ) {
        $this->subcriptionFactory = $subcriptionFactory;
        $this->buyFactory = $buyFactory;
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
            $response = $merchant->getGraphql()->query(self::RECURRING_PURCHASES_QUERY);
            $responseBody = $response->getDecodedBody();
            $currentPeriodEnd = $responseBody["data"]["currentAppInstallation"]["activeSubscriptions"][0]["currentPeriodEnd"] ?? null;

            if ($currentPeriodEnd === null) {
                $collection = $this->buyFactory->create();
                $collection->addFieldToFilter('merchant_shop', $merchant->getShop());
                $collection->join(
                    ['sbp' => \SectionBuilder\Product\Model\ResourceModel\Section::MAIN_TABLE],
                    'main_table.product_id = sbp.entity_id',
                    ['sbp.src']
                );
                $collection->addFieldToFilter('sbp.src', self::FILE_BASE_CSS);
                $buyData = $collection->getFirstItem()->getData();

                if (isset($buyData['install'])) {
                    $themeInstall = explode(",", $buyData['install']);
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
