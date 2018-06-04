<?php
/**
 * API for Billing
 *
 * @link      https://github.com/hiqdev/billing-hiapi
 * @package   billing-hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\billing\hiapi\vo;

use hiqdev\yii\DataMapper\hydrator\GeneratedHydrator;
use hiqdev\php\units\Quantity;

/**
 * Quantity Hydrator.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class QuantityHydrator extends GeneratedHydrator
{
    public function hydrate(array $data, $object)
    {
        return Quantity::create($data['unit'], $data['quantity']);
    }

    /**
     * {@inheritdoc}
     * @param object|Quantity $object
     */
    public function extract($object)
    {
        return array_filter([
            'unit'      => $object->getUnit()->getName(),
            'quantity'  => $object->getQuantity(),
        ]);
    }
}
