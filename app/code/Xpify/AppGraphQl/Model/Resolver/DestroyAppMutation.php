<?php
declare(strict_types=1);

namespace Xpify\AppGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Xpify\App\Api\AppRepositoryInterface;
use Xpify\AppGraphQl\Model\AuthorizationTrait;

class DestroyAppMutation implements ResolverInterface
{
    use AuthorizationTrait;

    private $appRepository;

    private $uidEncoder;

    /**
     * @param AppRepositoryInterface $appRepository
     * @param Uid $uidEncoder
     */
    public function __construct(
        AppRepositoryInterface $appRepository,
        Uid $uidEncoder
    ) {
        $this->appRepository = $appRepository;
        $this->uidEncoder = $uidEncoder;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->authorize($context);
        try {
            if (!empty($args['id'])) {
                $this->appRepository->deleteById($this->uidEncoder->decode((string) $args['id']));
            }
        } catch (\Exception $e) {

        }
        return true;
    }
}
