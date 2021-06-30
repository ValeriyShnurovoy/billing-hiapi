<?php
declare(strict_types=1);

/**
 * API for Billing
 *
 * @link      https://github.com/hiqdev/billing-hiapi
 * @package   billing-hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\billing\hiapi\plan\GetInfo;

use hiapi\Core\Auth\AuthRule;
use hiqdev\php\billing\plan\PlanRepositoryInterface;
use yii\web\User;
use hiqdev\php\billing\plan\Plan;

final class Action
{
    private PlanRepositoryInterface $repo;
    private User $user;

    public function __construct(PlanRepositoryInterface $repo, User $user)
    {
        $this->repo = $repo;
        $this->user = $user;
    }

    public function __invoke(Command $command): Plan
    {
        return $this->repo->findOne(
            AuthRule::currentUser()->applyToSpecification($command->getSpecification())
        );
    }
}
