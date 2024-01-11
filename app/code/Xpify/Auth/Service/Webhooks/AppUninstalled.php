<?php
declare(strict_types=1);

namespace Xpify\Auth\Service\Webhooks;

use Shopify\Webhooks\Handler;
use Xpify\Merchant\Api\MerchantRepositoryInterface as IMerchantRepository;

class AppUninstalled implements Handler
{
    private IMerchantRepository $merchantRepository;

    /**
     * @param IMerchantRepository $merchantRepository
     */
    public function __construct(IMerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @inheritDoc
     */
    public function handle(string $topic, string $shop, array $body): void
    {
        try {
            $this->merchantRepository->de($shop);
            $this->getLogger()?->info(__("$shop đã gỡ cài đặt app")->render());
        } catch (\Exception $e) {
            $this->getLogger()?->debug(__("Failed to uninstall app for shop $shop: %1", $e->getMessage())->render());
        }
    }

    /**
     * Logger hehe
     *
     * @return \Zend_Log|null
     */
    private function getLogger(): ?\Zend_Log
    {
        try {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/app_uninstalled.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            return $logger;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
