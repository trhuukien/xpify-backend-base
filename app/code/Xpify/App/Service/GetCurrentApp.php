<?php
declare(strict_types=1);

namespace Xpify\App\Service;

use Magento\Framework\App\RequestInterface as IRequest;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Uid;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\App\Api\AppRepositoryInterface as IAppRepository;

class GetCurrentApp
{
    private ?IApp $app = null;
    private IRequest $request;
    private IAppRepository $appRepository;
    private Uid $uidEncoder;

    /**
     * @param IRequest $request
     * @param IAppRepository $appRepository
     * @param Uid $uidEncoder
     */
    public function __construct(
        IRequest $request,
        IAppRepository $appRepository,
        Uid $uidEncoder
    ) {
        $this->request = $request;
        $this->appRepository = $appRepository;
        $this->uidEncoder = $uidEncoder;
    }

    /**
     * Force set app
     *
     * @param IApp $app
     */
    public function set(IApp $app): void
    {
        $this->app = $app;
    }

    /**
     * @return IApp|null
     * @throws NoSuchEntityException
     */
    public function get()
    {
        if ($this->app) {
            return $this->app;
        }
        try {
            list($id, $requestFieldName) = $this->resolveRequestId();
        } catch (GraphQlInputException $e) {
            throw new NoSuchEntityException(__("App not found"));
        }

        if (empty($id)) {
            throw new NoSuchEntityException(__("App not found"));
        }
        $this->app = $this->appRepository->get($id, $requestFieldName);

        return $this->app;
    }

    /**
     * @throws GraphQlInputException
     */
    protected function resolveRequestId(): array
    {
        if ($this->request->getParam('_rid')) {
            return [
                $this->request->getParam('_rid'),
                IApp::REMOTE_ID
            ];
        }
        return [
            $this->request->getParam('_i') ? (int) $this->uidEncoder->decode($this->request->getParam('_i')) : null,
            IApp::ID
        ];
    }
}
