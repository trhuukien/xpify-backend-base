<?php
declare(strict_types=1);

namespace SectionBuilder\Billing\Controller\Checkout;

use Magento\Framework\App\Action\HttpGetActionInterface as IHttpGetAction;
use Magento\Framework\App\RequestInterface as IRequest;
use Magento\Framework\Controller\ResultFactory;
use SectionBuilder\Product\Api\Data\SectionInterface as ISection;
use SectionBuilder\Product\Api\SectionBuyRepositoryInterface as IPurchasedSectionRepository;
use SectionBuilder\Product\Api\SectionRepositoryInterface;
use Xpify\App\Service\GetCurrentApp;
use Xpify\Core\Helper\ShopifyContextInitializer;
use Xpify\Core\Helper\Utils;
use Xpify\Core\Model\Logger;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;
use Xpify\Merchant\Api\MerchantRepositoryInterface as IMerchantRepository;
use Xpify\Merchant\Service\Billing;
use SectionBuilder\Billing\Exception\PurchaseSectionException;

class Success implements IHttpGetAction
{
    private IRequest $request;
    private GetCurrentApp $currentApp;
    private IPurchasedSectionRepository $purchasedSectionRepository;
    private IMerchantRepository $merchantRepository;
    private ShopifyContextInitializer $shopifyContextInitializer;
    private ResultFactory $resultFactory;
    private SectionRepositoryInterface $sectionRepository;

    /**
     * @param IRequest $request
     * @param GetCurrentApp $currentApp
     * @param IPurchasedSectionRepository $purchasedSectionRepository
     * @param IMerchantRepository $merchantRepository
     * @param ShopifyContextInitializer $shopifyContextInitializer
     * @param ResultFactory $resultFactory
     * @param SectionRepositoryInterface $sectionRepository
     */
    public function __construct(
        IRequest $request,
        GetCurrentApp $currentApp,
        IPurchasedSectionRepository $purchasedSectionRepository,
        IMerchantRepository $merchantRepository,
        ShopifyContextInitializer $shopifyContextInitializer,
        ResultFactory $resultFactory,
        SectionRepositoryInterface $sectionRepository
    ) {
        $this->request = $request;
        $this->currentApp = $currentApp;
        $this->purchasedSectionRepository = $purchasedSectionRepository;
        $this->merchantRepository = $merchantRepository;
        $this->shopifyContextInitializer = $shopifyContextInitializer;
        $this->resultFactory = $resultFactory;
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $app = $this->currentApp->get();
            if ($app?->getRemoteId() !== $this->request->getParam('_rid')) {
                throw new PurchaseSectionException('Invalid request');
            }

            $merchantId = $this->request->getParam('_mid');
            $isValid = Utils::validateHmac([
                'data' => ['_mid' => $merchantId, '_rid' => $this->getRequest()->getParam('_rid')],
                'buildQuery' => true,
                'buildQueryWithJoin' => true,
                'hmac' => $this->getRequest()->getParam('_sign'),
            ], \Xpify\Core\Model\Constants::SYS_SECRET_KEY);

            if (!$isValid) {
                throw new PurchaseSectionException('Invalid signature');
            }

            $this->shopifyContextInitializer->initialize($app);
            $merchant = $this->merchantRepository->getById((int) $merchantId);
            if (!$merchant?->getId()) {
                throw new PurchaseSectionException('Merchant not found');
            }
            $responseBody = Billing::getOneTimePayment($merchant, $this->getShopifyAppPurchaseOneTimeId());
            if (empty($responseBody["data"]["node"])) {
                throw new PurchaseSectionException('Purchase not found');
            }
            $purchase = $responseBody["data"]["node"];
            if ($purchase["status"] !== 'ACTIVE') {
                throw new PurchaseSectionException('Purchase not completed!');
            }
            $section = $this->sectionRepository->get('url_key', $purchase['name']);
            if (!$section?->getId()) {
                throw new PurchaseSectionException('Section not found');
            }

            $handlers = [
                \SectionBuilder\Product\Model\Config\Source\ProductType::GROUP_TYPE_ID => 'handleGroupSectionPurchase',
                \SectionBuilder\Product\Model\Config\Source\ProductType::SIMPLE_TYPE_ID => 'handleSimpleSectionPurchase',
            ];
            if (!$section->getTypeId()) {
                throw new PurchaseSectionException('Invalid section type');
            }
            $handler = $handlers[$section->getTypeId()];
            $this->$handler($merchant, $section, $purchase);
            $returnUrl = "https://" . $merchant->getShop() . "/admin/apps/" . $app->getName() . "/section/" . $section->getKey() . "?purchase_completed=1";

            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($returnUrl);
        } catch (PurchaseSectionException $e) {
            echo $e->getMessage();
        } catch (\Throwable $e) {
            echo 'Failed to process purchase. Please contact us!';
        }

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)->setContents('');
    }

    /**
     * Put all the child section purchase into the purchased section table
     *
     * @param IMerchant $merchant
     * @param ISection $section
     * @param array $purchase
     * @throws PurchaseSectionException
     */
    private function handleGroupSectionPurchase(IMerchant $merchant, ISection $section, array $purchase): void
    {
        $childIds = $section->getChildIds();
        if (empty($childIds)) {
            throw new PurchaseSectionException('No section found');
        }
        $childIds = explode(",", $childIds);
        foreach ($childIds as $childId) {
            $childSection = $this->sectionRepository->get('entity_id', $childId);
            if (!$childSection?->getId()) {
                throw new PurchaseSectionException('Section not found');
            }
            $this->handleSimpleSectionPurchase($merchant, $childSection, $purchase, $section);
        }
    }

    /**
     * Put the section purchase into the purchased section table
     *
     * @param IMerchant $merchant
     * @param ISection $section
     * @param array $purchase
     * @param ?ISection $parent
     * @throws PurchaseSectionException
     */
    private function handleSimpleSectionPurchase(IMerchant $merchant, ISection $section, array $purchase, ISection $parent = null): void
    {
        try {
            $purchasedSection = $this->purchasedSectionRepository->create();
            $purchasedSection->setMerchantShop($merchant->getShop());
            $purchasedSection->setCreatedAt($purchase['createdAt']);
            if ($parent?->getId()) {
                $purchase['parent'] = [
                    'id' => $parent->getId(),
                    'name' => $parent->getName(),
                    'url_key' => $parent->getKey(),
                    'plan_id' => $parent->getPlanId(),
                    'price' => $parent->getPrice(),
                    'version' => $parent->getVersion(),
                    'media_gallery' => $parent->getMediaGallery(),
                    'child_ids' => $parent->getChildIds(),
                ];
            }
            $purchasedSection->setDetails(json_encode($purchase));
            $purchasedSection->setProductId($section->getId());
            $this->purchasedSectionRepository->save($purchasedSection);
        } catch (\Exception $e) {
            Logger::getLogger('section_builder_section_purchase.log')?->debug("[{$section->getId()}]" . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            throw new PurchaseSectionException('Failed to save purchase. Please contact us!');
        }
    }

    /**
     * Returns the shopify app purchase one time id
     * @return string
     */
    private function getShopifyAppPurchaseOneTimeId(): string
    {
        return 'gid://shopify/AppPurchaseOneTime/' . $this->getRequest()->getParam('charge_id');
    }

    /**
     * @return IRequest
     */
    public function getRequest(): IRequest
    {
        return $this->request;
    }
}
