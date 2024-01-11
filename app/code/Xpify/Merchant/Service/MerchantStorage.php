<?php
declare(strict_types=1);

namespace Xpify\Merchant\Service;

use Shopify\Auth\Session;
use Shopify\Auth\SessionStorage;
use Xpify\Merchant\Api\MerchantRepositoryInterface as IMerchantRepository;

class MerchantStorage implements SessionStorage
{
    private IMerchantRepository $merchantRepository;

    /**
     * @param IMerchantRepository $merchantRepository
     */
    public function __construct(
        IMerchantRepository $merchantRepository
    ) {
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @inheritDoc
     */
    public function storeSession(Session $session): bool
    {
        // TODO: Implement storeSession() method.
        return true;
    }

    /**
     * @inheritDoc
     */
    public function loadSession(string $sessionId)
    {
        // TODO: Implement loadSession() method.
    }

    /**
     * @inheritDoc
     */
    public function deleteSession(string $sessionId): bool
    {
        // TODO: Implement deleteSession() method.

        return true;
    }
}
