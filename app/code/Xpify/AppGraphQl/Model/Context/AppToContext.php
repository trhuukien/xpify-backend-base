<?php
declare(strict_types=1);

namespace Xpify\AppGraphQl\Model\Context;

use Magento\Framework\App\RequestInterface as IRequest;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\GraphQl\Model\Query\ContextParametersInterface;
use Magento\GraphQl\Model\Query\ContextParametersProcessorInterface;
use Xpify\App\Api\AppRepositoryInterface as IAppRepository;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\Core\Helper\ShopifyContextInitializer;

class AppToContext implements ContextParametersProcessorInterface
{
    private IRequest $request;

    private Uid $uidEncoder;

    private IAppRepository $appRepository;

    private ShopifyContextInitializer $contextInitializer;

    /**
     * @param IRequest $request
     * @param Uid $uidEncoder
     * @param IAppRepository $appRepository
     * @param ShopifyContextInitializer $contextInitializer
     */
    public function __construct(
        IRequest $request,
        Uid $uidEncoder,
        IAppRepository $appRepository,
        ShopifyContextInitializer $contextInitializer
    ) {
        $this->request = $request;
        $this->uidEncoder = $uidEncoder;
        $this->appRepository = $appRepository;
        $this->contextInitializer = $contextInitializer;
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
                $app = $this->appRepository->get($decodedAppId, IApp::REMOTE_ID);
                if (!$app->getId()) {
                    return $contextParameters;
                }

                $contextParameters->addExtensionAttribute('app', $app);
                $this->contextInitializer->initialize($app);
            }
        } catch (\Exception $e) {

        }

        return $contextParameters;
    }
}
