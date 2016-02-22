<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Refund\Business\Model;

use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderItemsAndExpensesTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Refund\Dependency\Facade\RefundToOmsInterface;
use Spryker\Zed\Refund\Dependency\Facade\RefundToSalesInterface;
use Orm\Zed\Refund\Persistence\SpyRefund;
use Spryker\Zed\Sales\Persistence\SalesQueryContainer;

class Refund
{

    /**
     * @var \Spryker\Zed\Refund\Dependency\Facade\RefundToSalesInterface
     */
    protected $salesFacade;

    /**
     * @var \Spryker\Zed\Refund\Dependency\Facade\RefundToOmsInterface
     */
    protected $omsFacade;

    /**
     * @var \Spryker\Zed\Sales\Persistence\SalesQueryContainer
     */
    protected $salesQueryContainer;

    /**
     * @param \Spryker\Zed\Refund\Dependency\Facade\RefundToSalesInterface $salesFacade
     * @param \Spryker\Zed\Refund\Dependency\Facade\RefundToOmsInterface $omsFacade
     * @param \Spryker\Zed\Sales\Persistence\SalesQueryContainer $salesQueryContainer
     */
    public function __construct(
        RefundToSalesInterface $salesFacade,
        RefundToOmsInterface $omsFacade,
        SalesQueryContainer $salesQueryContainer
    ) {
        $this->salesFacade = $salesFacade;
        $this->omsFacade = $omsFacade;
        $this->salesQueryContainer = $salesQueryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    public function saveRefund(RefundTransfer $refundTransfer)
    {
        $this->salesQueryContainer->getConnection()->beginTransaction();

        $refundEntity = new SpyRefund();
        $refundEntity->fromArray($refundTransfer->toArray());
        $refundEntity->save();

        $orderItems = $refundTransfer->getOrderItems();
        $processedOrderItems = $this->processItems($orderItems);

        $expenses = $refundTransfer->getExpenses();
        $processedExpenses = $this->processExpenses($expenses);

        if (!$processedOrderItems) {
            $this->salesQueryContainer->getConnection()->rollBack();

            return null;
        }

        $this->updateOrderItemsAndExpensesAfterRefund($refundEntity->getIdRefund(), $processedOrderItems, $processedExpenses);

        $this->salesQueryContainer->getConnection()->commit();

        $orderItemsIds = [];
        /** @var \Generated\Shared\Transfer\ItemTransfer $processedOrderItem */
        foreach ($processedOrderItems as $processedOrderItem) {
            $orderItemsIds[] = $processedOrderItem->getIdSalesOrderItem();
        }

        $orderItems = $this->salesQueryContainer->querySalesOrderItem()
            ->filterByIdSalesOrderItem($orderItemsIds)
            ->find();
        $this->omsFacade->triggerEvent('start refund', $orderItems, []);

        $refundTransfer->setIdRefund($refundEntity->getIdRefund());

        return $refundTransfer;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\RefundOrderItemTransfer[] $orderItems
     *
     * @return \ArrayObject
     */
    protected function processItems(\ArrayObject $orderItems)
    {
        $orderItemArray = new \ArrayObject();
        foreach ($orderItems as $orderItem) {
            if ($orderItem->getQuantity() < 1) {
                continue;
            }

            $itemSplitResponseTransfer = $this->salesFacade->splitSalesOrderItem($orderItem->getIdOrderItem(), $orderItem->getQuantity());
            if ($itemSplitResponseTransfer->getSuccess()) {
                $idOrderItem = $itemSplitResponseTransfer->getIdOrderItem();
            } else {
                $idOrderItem = $orderItem->getIdOrderItem();
            }

            $itemTransfer = new ItemTransfer();
            $itemTransfer->setIdSalesOrderItem($idOrderItem);
            $orderItemArray[] = $itemTransfer;
        }

        return $orderItemArray;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\RefundExpenseTransfer[] $expenses
     *
     * @return \ArrayObject
     */
    protected function processExpenses(\ArrayObject $expenses)
    {
        $expensesArray = new \ArrayObject();
        foreach ($expenses as $expense) {
            if ($expense->getQuantity() < 1) {
                continue;
            }

            $expenseTransfer = new ExpenseTransfer();
            $expenseTransfer->setIdExpense($expense->getIdExpense());
            $expensesArray[] = $expenseTransfer;
        }

        return $expensesArray;
    }

    /**
     * @param int $idRefund
     * @param \ArrayObject $orderItemsArray
     * @param \ArrayObject $expensesArray
     *
     * @return void
     */
    protected function updateOrderItemsAndExpensesAfterRefund($idRefund, $orderItemsArray, $expensesArray)
    {
        $orderItemsAndExpensesTransfer = new OrderItemsAndExpensesTransfer();

        $orderItemsAndExpensesTransfer->setOrderItems($orderItemsArray);
        $orderItemsAndExpensesTransfer->setExpenses($expensesArray);

        $this->salesFacade->updateOrderItemsAndExpensesAfterRefund($idRefund, $orderItemsAndExpensesTransfer);
    }

    /**
     * @param int $idOrder
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderItem[]
     */
    public function getRefundableItems($idOrder)
    {
        return $this->salesQueryContainer
            ->querySalesOrderItem()
            ->filterByFkSalesOrder($idOrder)
            ->filterByFkRefund(null, Criteria::ISNULL)
            ->find();
    }

    /**
     * @param int $idOrder
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesExpense[]
     */
    public function getRefundableExpenses($idOrder)
    {
        return $this->salesQueryContainer
            ->querySalesExpense()
            ->filterByFkSalesOrder($idOrder)
            ->filterByFkRefund(null, Criteria::ISNULL)
            ->find();
    }

}
