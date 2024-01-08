<?php
declare(strict_types=1);

namespace Xpify\Core\Model\Context;

use Magento\Authorization\Model\UserContextInterface;
use Magento\GraphQl\Model\Query\ContextParametersInterface;
use Magento\GraphQl\Model\Query\ContextParametersProcessorInterface;

/**
 * Because, Customer GraphQl module is not installed, so the user context is not be run through.
 * @see \Magento\GraphQl\Model\Query\ContextFactory::create()
 */
class AuthUserToContext implements ContextParametersProcessorInterface
{
    private $userContext;

    /**
     * @param UserContextInterface $userContext
     */
    public function __construct(
        UserContextInterface $userContext,
    ) {
        $this->userContext = $userContext;
    }

    /**
     * Add user info to context
     *
     * @param ContextParametersInterface $contextParameters
     * @return ContextParametersInterface
     */
    public function execute(ContextParametersInterface $contextParameters): ContextParametersInterface
    {
        $userId = $this->userContext->getUserId();
        $contextParameters->setUserId($userId);
        $currentUserType = $this->userContext->getUserType();
        if (null !== $currentUserType) {
            $currentUserType = (int) $currentUserType;
        }
        $contextParameters->setUserType($currentUserType);
        $contextParameters->addExtensionAttribute('is_admin', $currentUserType === UserContextInterface::USER_TYPE_ADMIN);

        return $contextParameters;
    }
}
