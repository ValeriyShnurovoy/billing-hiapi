<?php
/**
 * API for Billing
 *
 * @link      https://github.com/hiqdev/billing-hiapi
 * @package   billing-hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\billing\hiapi\target\Purchase;

use hiapi\Core\Endpoint\BuilderFactory;
use hiapi\Core\Endpoint\Endpoint;
use hiapi\Core\Endpoint\EndpointBuilder;
use hiapi\endpoints\Module\Multitenant\Tenant;
use hiqdev\billing\hiapi\customer\CustomerLoader;
use hiqdev\billing\hiapi\plan\PlanLoader;
use hiqdev\php\billing\target\Target;

final class BulkBuilder
{
    public function __invoke(BuilderFactory $build): Endpoint
    {
        return $this->create($build)->build();
    }

    public function create(BuilderFactory $build): EndpointBuilder
    {
        return $build->endpoint(self::class)
                     ->exportTo(Tenant::ALL)
                     ->take($build->many(Command::class))
#                    ->checkPermission('object.buy')
                     ->middlewares(
                        $build->repeat(CustomerLoader::class),
                        $build->repeat(PlanLoader::class),
                        $build->repeat(Action::class)
                     )
                     ->return($build->many(Target::class));
    }
}
