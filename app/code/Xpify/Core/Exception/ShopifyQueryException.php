<?php
declare(strict_types=1);

namespace Xpify\Core\Exception;

use Exception;

class ShopifyQueryException extends Exception
{
    public array $errorData = [];

    public function __construct(string $message, array $errorData = [], $previous = null, $code = 0)
    {
        parent::__construct($message, $code, $previous);

        $this->errorData = $errorData;
    }

    public function getErrors(): array
    {
        return $this->errorData;
    }
}
