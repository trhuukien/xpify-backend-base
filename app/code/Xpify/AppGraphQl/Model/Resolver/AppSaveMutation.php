<?php
declare(strict_types=1);

namespace Xpify\AppGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Xpify\App\Api\AppRepositoryInterface;
use Xpify\AppGraphQl\Model\AppResultFormatter;
use Xpify\AppGraphQl\Model\AuthorizationTrait;

class AppSaveMutation implements ResolverInterface
{
    use AuthorizationTrait;

    private AppRepositoryInterface $appRepository;

    private Uid $uidEncoder;

    private AppResultFormatter $appResultFormatter;

    /**
     * @param AppRepositoryInterface $appRepository
     * @param Uid $uidEncoder
     * @param AppResultFormatter $appResultFormatter
     */
    public function __construct(
        AppRepositoryInterface $appRepository,
        Uid $uidEncoder,
        AppResultFormatter $appResultFormatter
    ) {
        $this->appRepository = $appRepository;
        $this->uidEncoder = $uidEncoder;
        $this->appResultFormatter = $appResultFormatter;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->authorize($context);
        try {
            $this->validateArgs($args);
            if (!empty($args['input']['id'])) {
                $app = $this->appRepository->get($this->uidEncoder->decode((string) $args['input']['id']));
            } else {
                $app = $this->appRepository->newInstance();
            }
            $filteredChanges = array_filter($args['input'], function ($key) {
                return $key !== 'id';
            }, ARRAY_FILTER_USE_KEY);
            if (!empty($filteredChanges)) {
                foreach ($filteredChanges as $key => $value) {
                    $app->setData($key, $value);
                }

                $this->appRepository->save($app);
            }

            return array_merge(['model' => $app], $this->appResultFormatter->toGraphQlOutput($app));
        } catch (GraphQlInputException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__("App not found!"));
        }
    }

    /**
     * Validate input arguments
     *
     * @param array $args
     * @return void
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args): void
    {
        if (empty($args['input'])) {
            throw new GraphQlInputException(__("Input argument is required."));
        }
        if (empty($args['input']['id']) && empty($args['input']['name'])) {
            throw new GraphQlInputException(__("Name is required."));
        }
        // validate name length must less or equal 30
        if (strlen($args['input']['name']) > 30) {
            throw new GraphQlInputException(__("Name must less or equal 30 characters."));
        }
    }
}
