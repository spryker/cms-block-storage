<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Setup\Communication\Controller;

use SprykerFeature\Zed\Setup\Business\SetupFacade;
use Symfony\Component\HttpFoundation\Request;
use SprykerFeature\Zed\Application\Communication\Plugin\TransferObject\TransferServer;
use Silex\Application;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use SprykerFeature\Zed\Application\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method SetupFacade getFacade()
 */
class TransferController extends AbstractController
{

    /**
     * @param Request $request
     * @return Response|string
     */
    public function repeatAction(Request $request)
    {
        $repeatData = $this->getFacade()->getRepeatData($request);

        if (is_array($repeatData)) {
            TransferServer::getInstance()->activateRepeating();
            $request = Request::createFromGlobals();
            $request->attributes->set('module', ($repeatData['module']));
            $request->attributes->set('controller', ($repeatData['controller']));
            $request->attributes->set('action', ($repeatData['action']));

            $request->request->replace($repeatData);

            return $this->getApplication()->handle($request, HttpKernelInterface::SUB_REQUEST);
        } else {
            return 'No request to repeat.';
        }
    }

}
