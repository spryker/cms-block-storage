<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\SearchPage\Communication\Controller;

use SprykerFeature\Zed\SearchPage\Communication\SearchPageDependencyContainer;
use SprykerFeature\Zed\Application\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method SearchPageDependencyContainer getDependencyContainer()
 */
class FormController extends AbstractController
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function createPageElementAction(Request $request)
    {
        $form = $this->getDependencyContainer()->createPageElementForm($request);

        $form->init();

        if ($form->isValid()) {
            $pageElementTransfer = new \Generated\Shared\Transfer\SearchPagePageElementTransfer();

            $pageElementTransfer->fromArray($form->getRequestData());

            $this->getDependencyContainer()->getSearchPageFacade()->createPageElement($pageElementTransfer);
            $form->setActiveValuesToDefault();
        }

        return $this->jsonResponse($form->renderData());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function updatePageElementAction(Request $request)
    {
        $form = $this->getDependencyContainer()->createPageElementForm($request);

        $form->init();

        if ($form->isValid()) {
            $pageElementTransfer = new \Generated\Shared\Transfer\SearchPagePageElementTransfer();
            $pageElementTransfer->fromArray($form->getRequestData());

            $this->getDependencyContainer()->getSearchPageFacade()->updatePageElement($pageElementTransfer);
            $form->setActiveValuesToDefault();
        }

        return $this->jsonResponse($form->renderData());
    }

    public function deletePageElementAction(Request $request)
    {

    }
}
