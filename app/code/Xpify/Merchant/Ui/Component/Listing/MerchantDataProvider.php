<?php
declare(strict_types=1);

namespace Xpify\Merchant\Ui\Component\Listing;

use Xpify\Merchant\Model\ResourceModel\Merchant\CollectionFactory as MerchantCollectionFactory;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;

class MerchantDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    public function __construct(
        MerchantCollectionFactory $factory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $factory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $collection = $this->getCollection();
        $data = $collection->toArray();
        $items = &$data['items'];
        foreach ($items as &$item) {
            $this->modifyItemData($item);
        }
        return $data;
    }

    /**
     * Modify item data, e.g. add online_access_info
     *
     * @param array $i - Item Data
     */
    public function modifyItemData(array &$i): void
    {
        $onlineAccessInfoFields = [
            'user_id', 'user_first_name', 'user_last_name', 'user_email', 'user_email_verified', 'account_owner', 'locale', 'collaborator'
        ];
        $onlineAccessInfo = [];
        foreach ($onlineAccessInfoFields as $f) {
            $onlineAccessInfo[$f] = $i[$f];
            unset($i[$f]);
        }
        $i['online_access_info'] = $onlineAccessInfo;
    }
}
