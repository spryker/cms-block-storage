<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\User\Communication\Controller;

use SprykerFeature\Zed\Application\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use SprykerFeature\Zed\Url\Communication\UrlDependencyContainer;

/**
 * @method UrlDependencyContainer getDependencyContainer
 */
class IndexController extends AbstractController
{
    /**
     * indexAction
     */
    public function indexAction()
    {

    }

    public function detailsAction(Request $request)
    {
        $userId = $request->query->get('id');



        return [
            'user_id' => $userId,
        ];
    }
}
