<?php
declare(strict_types=1);

namespace Xpify\Core\Exception;

use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class GraphQlException extends LocalizedException implements AggregateExceptionInterface, ClientAware
{
    private string $category;

    /**
     * @var boolean
     */
    private $isSafe;

    /**
     * The array of errors that have been added via the addError() method
     *
     * @var \Magento\Framework\Exception\LocalizedException[]
     */
    private $errors = [];

    /**
     * Initialize object
     *
     * @param Phrase $phrase
     * @param \Exception|null $cause
     * @param string $category
     * @param int $code
     * @param boolean $isSafe
     */
    public function __construct(Phrase $phrase, \Exception $cause = null, string $category = 'graphql-exception', $code = 0, bool $isSafe = true)
    {
        $this->isSafe = $isSafe;
        $this->category = $category;
        parent::__construct($phrase, $cause, $code);
    }

    /**
     * Add child error if used as aggregate exception
     *
     * @param LocalizedException $exception
     * @return $this
     */
    public function addError(LocalizedException $exception): self
    {
        $this->errors[] = $exception;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @inheritDoc
     */
    public function isClientSafe(): bool
    {
        return $this->isSafe;
    }

    /**
     * @inheritDoc
     */
    public function getCategory(): string
    {
        return $this->category;
    }
}
