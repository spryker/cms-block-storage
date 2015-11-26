<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Maintenance\Communication\Controller;

use SprykerFeature\Zed\Application\Communication\Controller\AbstractController;
use SprykerFeature\Zed\Maintenance\Business\MaintenanceFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @method MaintenanceFacade getFacade()
 */
class DependencyController extends AbstractController
{

    /**
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $bundles = $this->getFacade()->getAllBundles();

        return $this->viewResponse([
            'bundles' => $bundles,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function outgoingAction(Request $request)
    {
        $bundleName = $request->query->get('bundle', 'Glossary');

        $dependencies = $this->getFacade()->showOutgoingDependenciesForBundle($bundleName);

        return $this->viewResponse([
            'bundle' => $bundleName,
            'dependencies' => $dependencies,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function incomingAction(Request $request)
    {
        $bundleName = $request->query->get('bundle', 'Glossary');

        $dependencies = $this->getFacade()->showIncomingDependenciesForBundle($bundleName);

        return $this->viewResponse([
            'bundle' => $bundleName,
            'dependencies' => $dependencies,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return StreamedResponse
     */
    public function graphAction(Request $request)
    {
        $bundleName = $request->query->get('bundle', 'Glossary');
        $response = $this->getFacade()->drawDependencyGraph($bundleName);

        $callback = function () use ($response) {
            echo $response;
        };

        return $this->streamedResponse($callback);
    }

}
