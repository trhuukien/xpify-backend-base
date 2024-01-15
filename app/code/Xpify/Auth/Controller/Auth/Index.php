<?php
declare(strict_types=1);

namespace Xpify\Auth\Controller\Auth;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface as IRequest;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Query\Uid;
use Xpify\App\Service\GetCurrentApp;
use Xpify\Auth\Service\AuthRedirection;
use Xpify\Core\Helper\ShopifyContextInitializer;
use Xpify\Core\Helper\Utils;
use Xpify\Merchant\Api\MerchantRepositoryInterface as IMerchantRepository;
use Shopify\Auth\OAuth;
use Xpify\App\Api\Data\AppInterface as IApp;

class Index implements HttpGetActionInterface
{
    private IRequest $request;
    private Uid $uidEncoder;
    private IMerchantRepository $merchantRepository;
    private ShopifyContextInitializer $contextInitializer;
    private RedirectFactory $redirectFactory;
    private GetCurrentApp $getCurrentApp;
    private AuthRedirection $authRedirection;

    /**
     * @param IRequest $request
     * @param Uid $uidEncoder
     * @param IMerchantRepository $merchantRepository
     * @param ShopifyContextInitializer $contextInitializer
     * @param RedirectFactory $redirectFactory
     * @param GetCurrentApp $getCurrentApp
     * @param AuthRedirection $authRedirection
     */
    public function __construct(
        IRequest $request,
        Uid $uidEncoder,
        IMerchantRepository $merchantRepository,
        ShopifyContextInitializer $contextInitializer,
        RedirectFactory $redirectFactory,
        GetCurrentApp $getCurrentApp,
        AuthRedirection $authRedirection
    ) {
        $this->request = $request;
        $this->uidEncoder = $uidEncoder;
        $this->merchantRepository = $merchantRepository;
        $this->contextInitializer = $contextInitializer;
        $this->redirectFactory = $redirectFactory;
        $this->getCurrentApp = $getCurrentApp;
        $this->authRedirection = $authRedirection;
    }
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->verifySignature();
        $shop = \Shopify\Utils::sanitizeShopDomain($this->getRequest()->getParam('shop'));
        $this->merchantRepository->cleanNotCompleted($this->getRequest()->getParam('shop'));

        $app = $this->getCurrentApp->get();
        $this->contextInitializer->initialize($app);
        $redirectUrl = $this->authRedirection->createRedirectUrl($app, $shop);
        return $this->redirectFactory->create()->setUrl($redirectUrl);
    }

    /**
     * Xác minh chữ ký điện tử cho yêu cầu.
     *
     * @return void
     * @throws LocalizedException
     */
    protected function verifySignature()
    {
        try {
            $isValid = Utils::validateHmac([
                'data' => [
                    'shop' => $this->getRequest()->getParam('shop'),
                    '_i' => (int) $this->uidEncoder->decode($this->getRequest()->getParam('_i')),
                ],
                'buildQuery' => true,
                'buildQueryWithJoin' => true,
                'hmac' => $this->getRequest()->getParam('sign'),
            ], \Xpify\Core\Model\Constants::SYS_SECRET_KEY);
            if (!$isValid) {
                throw new LocalizedException(__("Invalid signature."));
            }
        } catch (\Throwable $e) {
            throw new LocalizedException(__("Invalid Request. Please login from your Shopify admin."));
        }
    }

    /**
     * @return IRequest
     */
    public function getRequest(): IRequest
    {
        return $this->request;
    }
}
