<?php
declare(strict_types=1);

namespace SectionBuilder\Core\Model\Auth;

class GraphQl
{
    public function execute($merchant, $query)
    {
        $response = $merchant->getGraphql()->query($query);
        return $response->getDecodedBody();
    }

    public function cancel($merchant, $purchaseId)
    {
        $query = [
            "query" => self::CANCEL,
            "variables" => [
                "id" => $purchaseId
            ],
        ];
        $responseBody = $this->execute($merchant, $query);

        return $responseBody;
    }

    public function hasOneTimePayment(
        \Xpify\Merchant\Api\Data\MerchantInterface $merchant,
        string $oneTimePurchaseKey
    ): bool {
        $endCursor = null;

        do {
            $responseBody = $this->execute($merchant, [
                "query" => self::ONE_TIME_PURCHASES_QUERY,
                "variables" => ["endCursor" => $endCursor]
            ]);
            $purchases = $responseBody["data"]["currentAppInstallation"]["oneTimePurchases"];

            foreach ($purchases["edges"] as $purchase) {
                $node = $purchase["node"];
                if (
                    $node["name"] === $oneTimePurchaseKey &&
                    (1 || !$node["test"]) &&
                    $node["status"] === "ACTIVE"
                ) {
                    return true;
                }
            }

            $endCursor = $purchases["pageInfo"]["endCursor"];
        } while ($purchases["pageInfo"]["hasNextPage"]);

        return false;
    }

    public function hasSubscription(
        \Xpify\Merchant\Api\Data\MerchantInterface $merchant,
        string $planName
    ): bool {
        $responseBody = $this->execute($merchant, self::RECURRING_PURCHASES_QUERY);
        $subscriptions = $responseBody["data"]["currentAppInstallation"]["activeSubscriptions"];

        foreach ($subscriptions as $subscription) {
            if (
                $subscription["name"] === $planName &&
                (1 || !$subscription["test"])
            ) {
                return true;
            }
        }

        return false;
    }

    public function getPlanByName(
        \Xpify\Merchant\Api\Data\MerchantInterface $merchant,
        string $planName
    ): array {
        $responseBody = $this->execute($merchant, self::RECURRING_PURCHASES_QUERY);
        $subscriptions = $responseBody["data"]["currentAppInstallation"]["activeSubscriptions"];

        foreach ($subscriptions as $subscription) {
            if ($subscription["name"] === $planName) {
                return $subscription;
            }
        }

        return [];
    }

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

    private const RECURRING_PURCHASES_QUERY = <<<'QUERY'
    query appSubscription {
        currentAppInstallation {
            activeSubscriptions {
                id
                name
                test
                currentPeriodEnd
                trialDays
            }
        }
    }
    QUERY;

    private const CANCEL = <<<'QUERY'
    mutation AppSubscriptionCancel($id: ID!) {
      appSubscriptionCancel(id: $id) {
        userErrors {
          field
          message
        }
        appSubscription {
          id
          status
        }
      }
    }
    QUERY;
}
