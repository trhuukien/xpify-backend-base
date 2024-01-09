<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Controller\Adminhtml\PricingPlan;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;
use Xpify\PricingPlan\Api\PricingPlanRepositoryInterface;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Xpify_PricingPlan::pricing_plan';

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
    public function execute(): Json
    {
        $postData = $this->getRequest()->getPost()->toArray();
        try {
            if (empty($postData[IPricingPlan::ID])) {
                $obj = $this->pricingPlanRepository->create();
            } else {
                $obj = $this->pricingPlanRepository->get($postData[IPricingPlan::ID]);
            }
        } catch (NoSuchEntityException $e) {
            $obj = $this->pricingPlanRepository->create();
        }

        try {
            $this->validatePayload($postData);
            $obj->setData($postData);
            $this->pricingPlanRepository->save($obj);
            $resData = [
                'message' => __('Pricing Plan saved successfully.'),
                'error' => false,
            ];
        } catch (\Exception $e) {
            $resData = [
                'message' => $e->getMessage(),
                'error' => true,
            ];
        }
        return $this->resultJsonFactory->create()->setData($resData);
    }

    /**
     * Validate payload
     *
     * @param array $payload
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function validatePayload(array &$payload): void
    {
        if (empty($payload[IPricingPlan::APP_ID])) {
            throw new \Magento\Framework\Exception\InputException(__('Please create App first.'));
        }
        $keysToUnset = ['form_key', 'currency', IPricingPlan::ID, IPricingPlan::FREE_TRIAL_DAYS, IPricingPlan::SORT_ORDER];
        foreach ($keysToUnset as $key) {
            if (isset($payload[$key]) && (empty($payload[$key]) || !is_numeric($payload[$key]))) {
                unset($payload[$key]);
            }
        }

        $keysToCheck = [IPricingPlan::ENABLE_FREE_TRIAL, IPricingPlan::STATUS];
        foreach ($keysToCheck as $key) {
            if (isset($payload[$key]) && !in_array((int) $payload[$key], [0, 1])) {
                unset($payload[$key]);
            }
        }
    }
}
