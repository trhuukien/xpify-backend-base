<?php
declare(strict_types=1);

namespace Xpify\Merchant\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Shopify\Auth\Session;
use Shopify\Context;
use Shopify\Exception\HttpRequestException;
use Shopify\Exception\MissingArgumentException;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\Core\Exception\ShopifyQueryException;
use Xpify\Core\Helper\Utils;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;
use Xpify\Merchant\Api\Data\MerchantSubscriptionInterface as ISubscription;
use Xpify\Merchant\Api\MerchantSubscriptionRepositoryInterface as ISubscriptionRepository;
use Xpify\Merchant\Exception\ShopifyBillingException;
use Xpify\Merchant\Helper\GraphqlQueryTrait;
use Xpify\Merchant\Helper\Subscription;
use Xpify\PricingPlan\Model\Source\IntervalType;

class Billing
{
    use GraphqlQueryTrait;

    private MerchantStorage $merchantStorage;
    private static $logger = null;

    private static array $RECURRING_INTERVALS = [
        IntervalType::INTERVAL_EVERY_30_DAYS, IntervalType::INTERVAL_ANNUAL
    ];
    private static ?IApp $app = null;

    /**
     * @param MerchantStorage $merchantStorage
     * @param ISubscriptionRepository $subscriptionRepository
     */
    public function __construct(
        MerchantStorage $merchantStorage
    ) {
        $this->merchantStorage = $merchantStorage;
    }

    public function isBillingRequired(Session|Imerchant $object): bool
    {
        $merchant = $object;
        if ($object instanceof Session) {
            $merchant = $this->merchantStorage->loadMerchantBySessionid($object->getId());
        }

        list($hasSubscription, $subscription) = Subscription::hasSubscription($merchant);
        return $hasSubscription && $subscription->getPrice() > 0;
    }

    /**
     * Get the subscription for the given merchant
     *
     * @param IMerchant $merchant
     * @return ISubscription
     * @throws LocalizedException
     */
    private function subscriptionOrException(IMerchant $merchant): ISubscription
    {
        return Subscription::getSubscription($merchant) ?? throw new LocalizedException(__('No subscription found'));
    }

    /**
     * Check if the given session has an active payment if not request one.
     *
     * @param Session|Imerchant $object The current session or merchant to check
     * @return array Array containing
     * - hasPayment: bool
     * - confirmationUrl: string|null
     * @throws ShopifyBillingException|NoSuchEntityException
     */
    public function check(Session|Imerchant $object): array
    {
        $merchant = $object;
        if ($object instanceof Session) {
            $merchant = $this->merchantStorage->loadMerchantBySessionid($object->getId());
        }
        $subscriptions = Subscription::getSubscriptions($merchant);
        if (count($subscriptions) === 0) {
            return [false, null];
        }
        foreach ($subscriptions as $subscription) {
            if ($subscription->getPrice() === 0.0) {
                continue;
            }
            $config = [
                'chargeName' => $subscription->getName(),
                'amount' => $subscription->getPrice(),
                'currencyCode' => \Xpify\App\Api\Data\AppInterface::CURRENCY_CODE,
                'interval' => $subscription->getInterval(),
            ];


            if ($this->hasActivePayment($merchant, $config)) {
                continue;
            }
            $appUid = Utils::idToUid($merchant->getAppId() . "");
            $sign = \Xpify\Core\Helper\Utils::createHmac([
                'data' => [
                    '_mid' => $merchant->getId(),
                    '_i' => $appUid,
                    '_sid' => $subscription->getId(),
                ],
                'buildQuery' => true,
                'buildQueryWithJoin' => true,
            ], \Xpify\Core\Model\Constants::SYS_SECRET_KEY);
            $config['return_url'] =
                Context::$HOST_SCHEME . '://' .
                Context::$HOST_NAME .
                '/xpify/billing/success' .
                "/_i/{$appUid}" .
                "/_mid/{$merchant->getId()}" .
                "/_sid/{$subscription->getId()}" .
                "/_sign/$sign";

            [$billingUrl] = $this->requestPayment($merchant, $config);
            return [true, $billingUrl];
        }

        return [false, null];
    }

    /**
     * Request a payment for the given merchant.
     *
     * @param IMerchant $merchant The current merchant to check
     * @param array   $config  Associative array that accepts keys:
     *                         - "chargeName": string, the name of the charge
     *                         - "amount": float
     *                         - "currencyCode": string
     *                         - "interval": one of the INTERVAL_* consts
     *
     * @return array - the confirmationUrl and the AppSubscription or AppPurchaseOneTime object
     * @throws ShopifyBillingException|NoSuchEntityException
     */
    public function requestPayment(IMerchant $merchant, array $config): array
    {
        $hostName = Context::$HOST_NAME;
        $shop = $merchant->getShop();
        $host = base64_encode("$shop/admin");
        $returnUrl = $config['return_url'] ?? "https://$hostName?shop={$shop}&host=$host";

        $objectKey = 'appSubscription';
        if (self::isRecurring($config)) {
            $data = self::requestRecurringPayment($merchant, $config, $returnUrl);
            $data = $data["data"]["appSubscriptionCreate"];
        } else {
            $data = self::requestOneTimePayment($merchant, $config, $returnUrl);
            $data = $data["data"]["appPurchaseOneTimeCreate"];
            $objectKey = "appPurchaseOneTime";
        }

        if (!empty($data["userErrors"])) {
            self::getLogger()->debug(__("User response error: %1", json_encode($data["userErrors"]))->render());
            throw new ShopifyBillingException("Error while billing the store. Please contact us!", $data["userErrors"]);
        }

        return [$data["confirmationUrl"], $data[$objectKey] ?? []];
    }

    /**
     * Check if the given merchant has an active payment
     *
     * @param IMerchant $merchant
     * @param array $config
     * @return bool
     * @throws ShopifyBillingException
     * @throws NoSuchEntityException
     */
    public function hasActivePayment(IMerchant $merchant, array $config): bool
    {
        if (self::isRecurring($config)) {
            return self::hasSubscription($merchant, $config);
        } else {
            return self::hasOneTimePayment($merchant, $config);
        }
    }

    /**
     * Request a one time payment for the given merchant
     *
     * @param IMerchant $merchant
     * @param array $config
     * @param string $returnUrl
     * @return array
     * @throws ShopifyBillingException|NoSuchEntityException
     */
    private static function requestOneTimePayment(IMerchant $merchant, array $config, string $returnUrl): array
    {
        return self::queryOrException(
            $merchant,
            [
                "query" => self::ONE_TIME_PURCHASE_MUTATION,
                "variables" => [
                    "name" => $config["chargeName"],
                    "price" => ["amount" => $config["amount"], "currencyCode" => $config["currencyCode"]],
                    "returnUrl" => $returnUrl,
                    "test" => !self::getCurrentApp()->isProd(),
                ],
            ]
        );
    }

    /**
     * Request a recurring payment for the given merchant
     *
     * @param IMerchant $merchant
     * @param array $config
     * @param string $returnUrl
     * @return array
     * @throws ShopifyBillingException|NoSuchEntityException
     */
    private static function requestRecurringPayment(IMerchant $merchant, array $config, string $returnUrl): array
    {
        return self::queryOrException(
            $merchant,
            [
                "query" => self::RECURRING_PURCHASE_MUTATION,
                "variables" => [
                    "name" => $config["chargeName"],
                    "lineItems" => [
                        "plan" => [
                            "appRecurringPricingDetails" => [
                                "interval" => $config["interval"],
                                "price" => ["amount" => $config["amount"], "currencyCode" => $config["currencyCode"]],
                            ],
                        ],
                    ],
                    "returnUrl" => $returnUrl,
                    "test" => !self::getCurrentApp()->isProd(),
                ],
            ]
        );
    }

    /**
     * Check merchant has one time payment
     *
     * @param IMerchant $merchant
     * @param array $config
     * @return bool
     * @throws ShopifyBillingException|NoSuchEntityException
     */
    private static function hasOneTimePayment(IMerchant $merchant, array $config): bool
    {
        $purchases = null;
        $endCursor = null;
        do {
            $responseBody = self::queryOrException(
                $merchant,
                [
                    "query" => self::ONE_TIME_PURCHASES_QUERY,
                    "variables" => ["endCursor" => $endCursor]
                ]
            );
            $purchases = $responseBody["data"]["currentAppInstallation"]["oneTimePurchases"];

            foreach ($purchases["edges"] as $purchase) {
                $node = $purchase["node"];
                if (
                    $node["name"] === $config["chargeName"] &&
                    (!self::getCurrentApp()->isProd() || !$node["test"]) &&
                    $node["status"] === "ACTIVE"
                ) {
                    return true;
                }
            }

            $endCursor = $purchases["pageInfo"]["endCursor"];
        } while ($purchases["pageInfo"]["hasNextPage"]);

        return false;
    }

    /**
     * Check merchant has subscription
     *
     * @param IMerchant $merchant
     * @param array $config
     * @return bool
     * @throws ShopifyBillingException|NoSuchEntityException
     */
    public static function hasSubscription(IMerchant $merchant, array $config): bool
    {
        $responseBody = self::queryOrException($merchant, self::RECURRING_PURCHASES_QUERY);
        $subscriptions = $responseBody["data"]["currentAppInstallation"]["activeSubscriptions"];

        foreach ($subscriptions as $subscription) {
            if (
                $subscription["name"] === $config["chargeName"] &&
                (!self::getCurrentApp()->isProd() || !$subscription["test"])
            ) {
                return true;
            }
        }

        return false;
    }

    public static function getOneTimePayment(Imerchant $m, string $id): array
    {
        return self::query($m, [
            "query" => self::GET_PURCHASED_ONETIME_QUERY,
            "variables" => ["id" => $id]
        ]);
    }

    /**
     * Query graphql or throw exception
     *
     * @param IMerchant $merchant
     * @param string|array $query
     * @return array
     * @throws ShopifyBillingException
     */
    private static function queryOrException(IMerchant $merchant, string|array $query): array
    {
        try {
            return self::query($merchant, $query);
        } catch (ShopifyQueryException $e) {
            self::getLogger()?->debug(
                __(
                    "Error while billing the store merchant ID %1. The original message: %2. Trace: %3",
                    $merchant->getId(),
                    $e->getMessage(),
                    $e->getTraceAsString()
                )->render()
            );
            throw new ShopifyBillingException("Error while billing the store", [$e->getMessage()]);
        }
    }

    /**
     * Check if the given config is recurring
     *
     * @param array $config
     * @return bool
     */
    private static function isRecurring(array $config): bool
    {
        return in_array($config["interval"], self::$RECURRING_INTERVALS);
    }

    /**
     * Get context app
     *
     * @return IApp
     * @throws NoSuchEntityException
     */
    private static function getCurrentApp(): IApp
    {
        if (!self::$app) {
            self::$app = \Magento\Framework\App\ObjectManager::getInstance()->get(\Xpify\App\Service\GetCurrentApp::class)->get();
        }
        if (self::$app === null) {
            throw new NoSuchEntityException(__("No App defined!"));
        }
        return self::$app;
    }

    /**
     * Custom logger for billing
     *
     * @return \Zend_Log|null
     */
    private static function getLogger(): ?\Zend_Log
    {
        try {
            if (!self::$logger) {
                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/app_billing.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                self::$logger = $logger;
            }
            return self::$logger;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private const RECURRING_PURCHASES_QUERY = <<<'QUERY'
    query appSubscription {
        currentAppInstallation {
            activeSubscriptions {
                name, test
            }
        }
    }
    QUERY;
    private const ONE_TIME_PURCHASES_QUERY = <<<'QUERY'
    query appPurchases($endCursor: String) {
        currentAppInstallation {
            oneTimePurchases(first: 250, sortKey: CREATED_AT, after: $endCursor) {
                edges {
                    node {
                        name, test, status
                    }
                }
                pageInfo {
                    hasNextPage, endCursor
                }
            }
        }
    }
    QUERY;
    private const RECURRING_PURCHASE_MUTATION = <<<'QUERY'
    mutation createPaymentMutation(
        $name: String!
        $lineItems: [AppSubscriptionLineItemInput!]!
        $returnUrl: URL!
        $test: Boolean
    ) {
        appSubscriptionCreate(
            name: $name
            lineItems: $lineItems
            returnUrl: $returnUrl
            test: $test
        ) {
            confirmationUrl
            userErrors {
                field, message
            }
        }
    }
    QUERY;
    private const ONE_TIME_PURCHASE_MUTATION = <<<'QUERY'
    mutation createPaymentMutation(
        $name: String!
        $price: MoneyInput!
        $returnUrl: URL!
        $test: Boolean
    ) {
        appPurchaseOneTimeCreate(
            name: $name
            price: $price
            returnUrl: $returnUrl
            test: $test
        ) {
            appPurchaseOneTime { id status createdAt }
            confirmationUrl
            userErrors {
                field, message
            }
        }
    }
    QUERY;
    private const GET_PURCHASED_ONETIME_QUERY = <<<'QUERY'
    query QueryOnetimePurchase($id: ID!) {
      node(id: $id) {
        ... on AppPurchaseOneTime {
          price {
            amount
            currencyCode
          }
          createdAt
          id
          name
          status
          test
        }
      }
    }
    QUERY;
}
