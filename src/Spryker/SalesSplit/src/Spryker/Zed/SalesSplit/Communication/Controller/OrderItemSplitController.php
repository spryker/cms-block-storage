<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\SalesSplit\Communication\Controller;

use Spryker\Zed\Application\Communication\Controller\AbstractController;
use Spryker\Zed\SalesSplit\Communication\Form\OrderItemSplitForm;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\SalesSplit\Communication\SalesSplitCommunicationFactory getFactory()
 * @method \Spryker\Zed\SalesSplit\Business\SalesSplitFacade getFacade()
 */
class OrderItemSplitController extends AbstractController
{

    const SALES_ORDER_DETAIL_URL = '/sales/details?id-sales-order=%d';
    const SPLIT_SUCCESS_MESSAGE = 'Order item with "%d" was successfully split.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function splitAction(Request $request)
    {
        $orderItemForm = $this
            ->getFactory()
            ->createOrderItemSplitForm()
            ->handleRequest($request);

        $formData = $orderItemForm->getData();

        if ($orderItemForm->isValid()) {
            $this->getFacade()->splitSalesOrderItem(
                $formData[OrderItemSplitForm::FIELD_ID_ORDER_ITEM],
                $formData[OrderItemSplitForm::FIELD_QUANTITY]
            );
        }

        return $this->redirectResponse(sprintf(self::SALES_ORDER_DETAIL_URL, $formData[OrderItemSplitForm::FIELD_ID_ORDER]));
    }

}
