<?php
declare(strict_types=1);

namespace Xpify\AppGraphQl\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\GraphQl\Model\Query\ContextInterface;

trait AuthorizationTrait
{
    private $_aclPolicy;

    /**
     * Authorize the current user
     *
     * @param ContextInterface $context
     * @return void
     * @throws GraphQlAuthorizationException
     */
    protected function authorize($context)
    {
        if ($context->getUserId() && $context->getUserType() === UserContextInterface::USER_TYPE_ADMIN) {
            $user = $this->_loadUser($context->getUserId());
            $aclRoleId = $user->getAclRole();
            $isAllowed = $this->_getAclPolicy()->isAllowed($aclRoleId, \Xpify\App\Controller\Adminhtml\Apps\Edit::ADMIN_RESOURCE);
            if ($isAllowed) {
                return;
            }
        }
        throw new GraphQlAuthorizationException(__("The current user cannot perform operations on apps"));
    }

    /**
     * Load authed user details
     *
     * @param int|string $id
     * @return \Magento\User\Model\User
     * @throws GraphQlAuthorizationException
     */
    private function _loadUser($id)
    {
        $user = \Magento\Framework\App\ObjectManager::getInstance()->create(\Magento\User\Model\User::class)->load($id);
        if ($user->getId()) {
            return $user;
        }

        throw new GraphQlAuthorizationException(__("The current user cannot perform operations on apps"));
    }

    /**
     * Retrieve acl policy service
     *
     * @return \Magento\Framework\Authorization\PolicyInterface|mixed
     */
    private function _getAclPolicy()
    {
        if (!$this->_aclPolicy) {
            $this->_aclPolicy = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Authorization\PolicyInterface::class);
        }

        return $this->_aclPolicy;
    }
}
