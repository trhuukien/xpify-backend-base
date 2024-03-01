<?php
declare(strict_types=1);

namespace Xpify\Core\GraphQl\Query;

/**
 * This class was born to handle the error when the access token is expired.
 * Default magento core graphql just gap the error -> push it to the error array in the response.
 * But we need to throw the exception when the access token is expired. to handle redirection in frontend.
 * This will present in the rewritten GraphQl controller.
 * @see \Xpify\Core\GraphQl\Controller\GraphQl
 */
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
