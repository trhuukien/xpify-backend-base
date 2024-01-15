<?php
declare(strict_types=1);

namespace Xpify\AppGraphQl\Model\Context;

use Magento\Framework\App\RequestInterface as IRequest;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\GraphQl\Model\Query\ContextParametersInterface;
use Magento\GraphQl\Model\Query\ContextParametersProcessorInterface;
use Shopify\Context;
use Xpify\App\Api\AppRepositoryInterface as IAppRepository;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\App\Service\GetCurrentApp;
use Xpify\Core\Helper\ShopifyContextInitializer;

class AppToContext implements ContextParametersProcessorInterface
{
    private IRequest $request;

    private Uid $uidEncoder;

    private IAppRepository $appRepository;

    private ShopifyContextInitializer $contextInitializer;
    private GetCurrentApp $getCurrentApp;

    /**
     * @param IRequest $request
     * @param Uid $uidEncoder
     * @param IAppRepository $appRepository
     * @param ShopifyContextInitializer $contextInitializer
     * @param GetCurrentApp $getCurrentApp
     */
    public function __construct(
        IRequest $request,
        Uid $uidEncoder,
        IAppRepository $appRepository,
        ShopifyContextInitializer $contextInitializer,
        GetCurrentApp $getCurrentApp
    ) {
        $this->request = $request;
        $this->uidEncoder = $uidEncoder;
        $this->appRepository = $appRepository;
        $this->contextInitializer = $contextInitializer;
        $this->getCurrentApp = $getCurrentApp;
    }

    /**
     * Add app info to context
     *
     * @param ContextParametersInterface $contextParameters
     * @return ContextParametersInterface
     */
    public function execute(ContextParametersInterface $contextParameters): ContextParametersInterface
    {
        // X-Xpify-App: Shopify App ID
        $xpifyAppHeader = $this->request->getHeader('X-Xpify-App');
        if (!$xpifyAppHeader) {
            return $contextParameters;
        }

        try {
            $decodedAppId = $this->uidEncoder->decode($xpifyAppHeader);
            if ($decodedAppId !== null) {
                $app = $this->appRepository->get($decodedAppId, IApp::ID);
                if (!$app->getId()) {
                    return $contextParameters;
                }

                $contextParameters->addExtensionAttribute('app', $app);
                // In graphql area, it should be locked to current app in context
                $this->getCurrentApp->set($app)->lock();
                try {
                    $this->contextInitializer->initialize($app);
                } catch (\Exception $e) {
                    $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shopify_context.log');
                    $logger = new \Zend_Log();
                    $logger->addWriter($writer);
                    $logger->debug($e->getMessage() . ' ||| ' . $e->getTraceAsString());

                    throw new LocalizedException(__('Failed to initialize Shopify context'));
                }
            }
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
        }

        return $contextParameters;
    }
}
