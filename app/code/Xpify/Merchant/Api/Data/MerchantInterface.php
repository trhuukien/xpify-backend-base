<?php
declare(strict_types=1);

namespace Xpify\Merchant\Api\Data;

interface MerchantInterface
{
    const ID = 'entity_id';
    const APP_ID = 'app_id';
    const SHOP = 'shop';
    const ACCESS_TOKEN = 'access_token';

    /**
     * Get the application ID
     *
     * This method is used to get the application ID of the merchant.
     * The return type is nullable integer, meaning it can return an integer or null.
     *
     * @return int|null The application ID of the merchant or null if not set
     */
    public function getAppId(): ?int;

    /**
     * Set the application ID
     *
     * This method is used to set the application ID of the merchant.
     * It accepts a nullable integer as an argument, meaning you can pass an integer or null.
     *
     * @param int|null $appId The application ID of the merchant
     * @return self Returns the current instance of the class to allow method chaining
     */
    public function setAppId(?int $appId): self;

    /**
     * Get the shop
     *
     * This method is used to get the shop of the merchant.
     * The return type is nullable string, meaning it can return a string or null.
     *
     * @return string|null The shop of the merchant or null if not set
     */
    public function getShop(): ?string;

    /**
     * Set the shop
     *
     * This method is used to set the shop of the merchant.
     * It accepts a nullable string as an argument, meaning you can pass a string or null.
     *
     * @param string|null $shop The shop of the merchant
     * @return self Returns the current instance of the class to allow method chaining
     */
    public function setShop(?string $shop): self;

    /**
     * Get the access token
     *
     * This method is used to get the access token of the merchant.
     * The return type is nullable string, meaning it can return a string or null.
     *
     * @return string|null The access token of the merchant or null if not set
     */
    public function getAccessToken(): ?string;

    /**
     * Set the access token
     *
     * This method is used to set the access token of the merchant.
     * It accepts a nullable string as an argument, meaning you can pass a string or null.
     *
     * @param string|null $accessToken The access token of the merchant
     * @return self Returns the current instance of the class to allow method chaining
     */
    public function setAccessToken(?string $accessToken): self;
}
