<?php
declare(strict_types=1);

namespace Xpify\Auth\Controller\Auth;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface as IRequest;

class Callback implements HttpGetActionInterface
{
    private IRequest $request;

    /**
     * @param IRequest $request
     */
    public function __construct(
        IRequest $request
    ) {
        $this->request = $request;
    }
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $cookies = $this->getRequest()->getCookie();
        /** @var \Laminas\Http\Headers $headers */
        $headers = $this->getRequest()->getHeaders();
        dd($headers->toArray());
    }

    /**
     * @return IRequest
     */
    public function getRequest(): IRequest
    {
        return $this->request;
    }
}
