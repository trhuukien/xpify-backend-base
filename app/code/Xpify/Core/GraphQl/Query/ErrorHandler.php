<?php
declare(strict_types=1);

namespace Xpify\Core\GraphQl\Query;

class ErrorHandler extends \Magento\Framework\GraphQl\Query\ErrorHandler
{
    /**
     * @inheritDoc
     * @throws \Xpify\AuthGraphQl\Exception\GraphQlShopifyReauthorizeRequiredException
     */
    public function handle(array $errors, callable $formatter): array
    {
        /** @var \GraphQL\Error\Error $error */
        foreach ($errors as $error) {
            if ($error?->getPrevious() instanceof \Xpify\AuthGraphQl\Exception\GraphQlShopifyReauthorizeRequiredException) {
                throw $error?->getPrevious();
            }
        }
        return parent::handle($errors, $formatter);
    }
}
