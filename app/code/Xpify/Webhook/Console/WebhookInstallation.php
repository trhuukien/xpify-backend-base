<?php
declare(strict_types=1);

namespace Xpify\Webhook\Console;

use Magento\Framework\App\State;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Xpify\App\Service\GetCurrentApp;
use Xpify\Core\Console\BaseCommand;
use Xpify\Core\Helper\ShopifyContextInitializer;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;

class WebhookInstallation extends BaseCommand
{
    const OPT_SHOP = 'shop';
    const OPT_APP_ID = 'app_id';

    private \Xpify\Merchant\Api\MerchantRepositoryInterface $merchantRepository;
    private \Xpify\App\Api\AppRepositoryInterface $appRepository;
    private GetCurrentApp $getCurrentApp;
    private ShopifyContextInitializer $contextInitializer;
    private State $appState;
    private ConfigLoaderInterface $configLoader;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $state,
        \Xpify\Merchant\Api\MerchantRepositoryInterface $merchantRepository,
        \Xpify\App\Api\AppRepositoryInterface $appRepository,
        GetCurrentApp $getCurrentApp,
        ShopifyContextInitializer $contextInitializer,
        ConfigLoaderInterface $configLoader,
        State $appState,
        string $name = null
    ) {
        parent::__construct($logger, $state, $name);
        $this->merchantRepository = $merchantRepository;
        $this->appRepository = $appRepository;
        $this->getCurrentApp = $getCurrentApp;
        $this->contextInitializer = $contextInitializer;
        $this->appState = $appState;
        $this->configLoader = $configLoader;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('xpify:webhook:install');
        $this->setDescription(
            'Chạy kiểm tra và cấu hình webhook cho shop và app.'
        );

        parent::configure();
    }

    /**
     * Set all dơnloadable product manage stock is no
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     * @SuppressWarnings(UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $merchants = $this->getValidMerchant($input, $output);
            $output->writeln("Kiểm tra");
            $frontendConfigs = $this->configLoader->load(\Magento\Framework\App\Area::AREA_FRONTEND);
            foreach ($merchants as $merchant) {
                try {
                    $this->appState->emulateAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND, function (IMerchant $merchant, OutputInterface $output) use ($frontendConfigs) {
                        \Magento\Framework\App\ObjectManager::getInstance()->configure($frontendConfigs);
                        $webhookRegister = \Magento\Framework\App\ObjectManager::getInstance()->get(\Xpify\Webhook\Service\Webhook::class);
                        $selectedApp = $this->getCurrentApp->get();
                        $output->writeln(sprintf("Kiểm tra shop %s", $merchant->getShop()));
                        $this->contextInitializer->initialize($selectedApp);
                        $webhookRegister->register($merchant->getSessionId());
                        $output->writeln(sprintf("Đã cài đặt webhook cho shop %s", $merchant->getShop()));
                    }, [$merchant, $output]);
                } catch (\Throwable $e) {
                    $output->writeln(sprintf("<error>ERROR: %s</error>", $e->getMessage()));
                }
            }

            return true;
        } catch (InvalidOptionException $e) {
            $output->writeln("");
            $output->writeln(sprintf("<error>ERROR: %s</error>", $e->getMessage()));
            $output->writeln("");

            return false;
        } catch (\Exception $e) {
            $this->logger->error($e);
            $output->writeln("");
            $output->writeln("<error>{$e->getMessage()}</error>");
            $output->writeln("");

            return false;
        }
    }

    /**
     * Validate option values
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws InvalidOptionException
     * @return IMerchant[]
     */
    protected function getValidMerchant(InputInterface $input, OutputInterface $output): array
    {
        $io = new SymfonyStyle($input, $output);
        $inputShops = $input->getOption(self::OPT_SHOP);
        if (empty($inputShops)) {
            $inputShops = [$io->ask('Nhập shop domain. chỉ cần nhập shop name, không cần nhập cả .myshopify.com', null, function (?string $shopName): string {
                if (empty($shopName)) {
                    throw new InvalidOptionException('Cần nhập thông tin shop mới thực thi lệnh được.');
                }
                return $shopName;
            })];
        }
        $appId = $input->getOption(self::OPT_APP_ID);
        if (!$appId) {
            $searchResults = $this->appRepository->getList($this->getSearchCriteriaBuilder()->create());
            if ($searchResults->getTotalCount() === 0) {
                throw new InvalidOptionException(__("Không có app nào hoạt động!")->render());
            }
            $apps = $searchResults->getItems();
            $appChoices = [];
            foreach ($apps as $app) {
                $appChoices[] = $app->getId() . ' - ' . $app->getName();
            }
            $answer = $io->choice('Chọn App (nhập index hoặc nhập theo tên)', $appChoices);
            $appId = explode(' - ', $answer)[0];
            $input->setOption(self::OPT_APP_ID, $appId);
        }
        $selectedApp = $this->appRepository->get($appId);
        $this->getCurrentApp->set($selectedApp)->lock();
        return array_filter(array_map(function ($shopDomain) use ($appId, $output) {
            $searchCriteria = $this->getSearchCriteriaBuilder();
            $searchCriteria->addFilter(IMerchant::SHOP, $shopDomain . '.myshopify.com');
            $searchCriteria->addFilter(IMerchant::APP_ID, $appId);
            $searchCriteria->setPageSize(1);
            $mSearchResults = $this->merchantRepository->getList($searchCriteria->create());
            if ($mSearchResults->getTotalCount() === 0) {
                $output->writeln(sprintf("<error>Shop %s chưa cài app được chọn nên không thể chạy được lệnh!</error>", $shopDomain));
                return null;
            }
            return current($mSearchResults->getItems());
        }, $inputShops), function (?IMerchant $m) {
            return $m !== null;
        });
    }

    /**
     * Get search criteria builder
     *
     * @return \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private function getSearchCriteriaBuilder()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create(\Magento\Framework\Api\SearchCriteriaBuilder::class);
    }

    public function getInputList(): array
    {
        return [
            new InputOption(
                self::OPT_SHOP,
                "s",
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                "Shop cần kiểm tra và cài đặt (hỗ trợ mảng)",
            ),
            new InputOption(
                self::OPT_APP_ID,
                "a",
                InputOption::VALUE_OPTIONAL,
                "App ID mà cần kiểm tra",
            )
        ];
    }
}
