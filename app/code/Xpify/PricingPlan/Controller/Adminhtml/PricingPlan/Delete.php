<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Controller\Adminhtml\PricingPlan;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Xpify\PricingPlan\Api\PricingPlanRepositoryInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Save::ADMIN_RESOURCE;

    private JsonFactory $resultJsonFactory;

    private PricingPlanRepositoryInterface $pricingPlanRepository;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PricingPlanRepositoryInterface $pricingPlanRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PricingPlanRepositoryInterface $pricingPlanRepository
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->pricingPlanRepository = $pricingPlanRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        try {
            $this->pricingPlanRepository->deleteById($id);
        } catch (\Exception $e) {
            return $this->resultJsonFactory->create()->setData([
                'message' => $e->getMessage(),
                'error' => true,
            ]);
        }

        return $this->resultJsonFactory->create()->setData([
            'message' => __('Pricing Plan deleted successfully.'),
            'error' => false,
        ]);
    }
}
