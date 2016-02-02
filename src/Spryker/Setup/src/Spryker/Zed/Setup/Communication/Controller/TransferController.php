<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Setup\Communication\Controller;

use Spryker\Zed\Setup\Business\SetupFacade;
use Symfony\Component\HttpFoundation\Request;
use Spryker\Zed\Application\Communication\Plugin\TransferObject\TransferServer;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Spryker\Zed\Application\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method SetupFacade getFacade()
 */
class TransferController extends AbstractController
{

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response|array
     */
    public function repeatAction(Request $request)
    {
        $repeatData = $this->getFacade()->getRepeatData($request);

        if (!is_array($repeatData)) {
            return new Response('No request to repeat.');
        }

        TransferServer::getInstance()->activateRepeating();
        $request = Request::createFromGlobals();
        $request->attributes->set('module', $repeatData['module']);
        $request->attributes->set('controller', $repeatData['controller']);
        $request->attributes->set('action', $repeatData['action']);

        $request->request->replace($repeatData);

        return $this->getApplication()->handle($request, HttpKernelInterface::SUB_REQUEST);
    }

}
