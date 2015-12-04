<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\SprykerFeature\Zed\Sales\Business\Model\OrderItemSplit\Validation;

use SprykerFeature\Zed\Sales\Business\Model\Split\Calculator;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;

class CalculatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testCalculateAmountLeft()
    {
        $calculator = new Calculator();
        $spySalesOrderItem = new SpySalesOrderItem();
        $spySalesOrderItem->setQuantity(2);

        $quantityAmountLeft = $calculator->calculateQuantityAmountLeft($spySalesOrderItem, 1);

        $this->assertEquals(1, $quantityAmountLeft);
    }

}
