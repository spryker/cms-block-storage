<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Auth\Communication\Controller;

use Spryker\Zed\Application\Communication\Controller\AbstractController;
use Spryker\Zed\Auth\Business\AuthFacade;
use Spryker\Zed\Auth\Communication\AuthDependencyContainer;
use Spryker\Zed\Auth\Communication\Form\LoginForm;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method AuthDependencyContainer getDependencyContainer()
 * @method AuthFacade getFacade()
 */
class LoginController extends AbstractController
{

    /**
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $form = $this->getDependencyContainer()->createLoginForm();
        $form->handleRequest();

        if ($request->isMethod(Request::METHOD_POST) && $form->isValid()) {
            $formData = $form->getData();

            $isLogged = $this->getFacade()->login($formData[LoginForm::USERNAME], $formData[LoginForm::PASSWORD]);

            if ($isLogged) {
                return $this->redirectResponse('/');
            } else {
                $this->addErrorMessage('Authentication failed!');
            }
        }

        return $this->viewResponse([
            'form' => $form->createView(),
        ]);
    }

}
