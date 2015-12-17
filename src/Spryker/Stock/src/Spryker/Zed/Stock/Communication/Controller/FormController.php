<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Stock\Communication\Controller;

use Generated\Shared\Transfer\StockProductTransfer;
use Generated\Shared\Transfer\TypeTransfer;
use Spryker\Zed\Application\Communication\Controller\AbstractController;
use Spryker\Zed\Stock\Communication\StockDependencyContainer;
use Spryker\Zed\Stock\Business\StockFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method StockDependencyContainer getCommunicationFactory()
 * @method StockFacade getFacade()
 */
class FormController extends AbstractController
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function stockAction(Request $request)
    {
        $form = $this->getCommunicationFactory()->getStockForm($request);

        if ($form->isValid()) {
            $stockTypeTransfer = new TypeTransfer();
            $stockTypeTransfer->fromArray($form->getRequestData());

            if ($stockTypeTransfer->getIdStock() === null) {
                $this->getFacade()->createStockType($stockTypeTransfer);
            } else {
                $this->getFacade()->updateStockType($stockTypeTransfer);
            }
            $form->setActiveValuesToDefault();
        }

        return $this->jsonResponse($form->renderData());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function stockProductAction(Request $request)
    {
        $form = $this->getCommunicationFactory()->getStockProductForm($request);

        if ($form->isValid()) {
            $stockProduct = new StockProductTransfer();
            $stockProduct->fromArray($form->getRequestData());

            if ($stockProduct->getIdStockProduct() === null) {
                $this->getStockFacade()->createStockProduct($stockProduct);
            } else {
                $this->getStockFacade()->updateStockProduct($stockProduct);
            }
            $form->setActiveValuesToDefault();
        }

        return $this->jsonResponse($form->renderData());
    }

}
