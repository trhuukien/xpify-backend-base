<?php
declare(strict_types=1);

namespace Xpify\App\Service;

use Shopify\Auth\Session;
use Shopify\Clients\Graphql;
use Xpify\App\Exception\ShopifyBillingException;

class EnsureBilling
{
    public const INTERVAL_ONE_TIME = "ONE_TIME";
    public const INTERVAL_EVERY_30_DAYS = "EVERY_30_DAYS";
    public const INTERVAL_ANNUAL = "ANNUAL";

    private static $RECURRING_INTERVALS = [
        self::INTERVAL_EVERY_30_DAYS, self::INTERVAL_ANNUAL
    ];

    /**
     * Check if the given session has an active payment based on the configs.
     *
     * @param Session $session The current session to check
     * @param array   $config  Associative array that accepts keys:
     *                         - "chargeName": string, the name of the charge
     *                         - "amount": float
     *                         - "currencyCode": string
     *                         - "interval": one of the INTERVAL_* consts
     *
     * @return array Array containing
     * - hasPayment: bool
     * - confirmationUrl: string|null
     */
    public static function check(Session $session, array $config): array
    {
        $confirmationUrl = null;

        if (self::hasActivePayment($session, $config)) {
            $hasPayment = true;
        } else {
            $hasPayment = false;
            $confirmationUrl = self::requestPayment($session, $config);
        }

        return [$hasPayment, $confirmationUrl];
    }

    private static function hasSubscription(Session $session, array $config): bool
    {
        $responseBody = self::queryOrException($session, self::RECURRING_PURCHASES_QUERY);
        $subscriptions = $responseBody["data"]["currentAppInstallation"]["activeSubscriptions"];

        foreach ($subscriptions as $subscription) {
            if (
                $subscription["name"] === $config["chargeName"] &&
                (/*!self::isProd() || */!$subscription["test"])
            ) {
                return true;
            }
        }

        return false;
    }

    private static function hasActivePayment(Session $session, array $config): bool
    {
        if (self::isRecurring($config)) {
            return self::hasSubscription($session, $config);
        } else {
            return self::hasOneTimePayment($session, $config);
        }
    }

    private static function isRecurring(array $config): bool
    {
        return in_array($config["interval"], self::$RECURRING_INTERVALS);
    }

    /**
     * @param string|array $query
     */
    private static function queryOrException(Session $session, $query): array
    {
        $client = new Graphql($session->getShop(), $session->getAccessToken());

        $response = $client->query($query);
        $responseBody = $response->getDecodedBody();

        if (!empty($responseBody["errors"])) {
            throw new ShopifyBillingException("Error while billing the store", (array)$responseBody["errors"]);
        }

        return $responseBody;
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
}
