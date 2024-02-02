<?php
namespace SectionBuilder\FileModifier\Controller\Adminhtml\Cron;

use Magento\Backend\App\Action\Context;

class Base extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    protected $cronJob;

    protected $jsonFactory;

    public function __construct(
        Context $context,
        \SectionBuilder\FileModifier\Cron\Plan $cronJob,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->cronJob = $cronJob;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        try {
            $result = $this->cronJob->process(true);
            $message['success'] = __(
                "Scan %1 merchant(s). Handle %2/%3 file(s).",
                $result['execute'],
                $result['success'],
                $result['success'] + $result['not_handle']
            );
            $message['warning'] = $result['alert'];
            $message['error'] = $result['error'] ? __("%1 error(s) occurred!", $result['error']) : '';
        } catch (\Exception $e) {
            $message['error'] = $e->getMessage();
        }

        $resultJson = $this->jsonFactory->create();
        $resultJson->setData($message);
        return $resultJson;
    }
}
