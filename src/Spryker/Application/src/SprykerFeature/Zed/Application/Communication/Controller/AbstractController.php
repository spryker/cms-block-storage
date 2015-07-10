<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Application\Communication\Controller;

use Generated\Zed\Ide\AutoCompletion;
use Silex\Application;
use SprykerEngine\Shared\Messenger\Business\Model\MessengerInterface;
use SprykerEngine\Zed\Kernel\Business\AbstractFacade;
use SprykerEngine\Zed\Kernel\Communication\AbstractCommunicationDependencyContainer;
use SprykerEngine\Zed\Kernel\Communication\Factory;
use SprykerEngine\Zed\Kernel\Container;
use SprykerEngine\Zed\Kernel\Locator;
use SprykerEngine\Zed\Kernel\Persistence\AbstractQueryContainer;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class AbstractController
{

    /**
     * @var Application
     */
    private $application;

    /**
     * @var AutoCompletion
     */
    private $locator;

    /**
     * @var AbstractCommunicationDependencyContainer
     */
    private $dependencyContainer;

    /**
     * @var AbstractFacade
     */
    private $facade;

    /**
     * @var AbstractQueryContainer
     */
    private $queryContainer;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Application $application
     * @param Factory $factory
     * @param Locator $locator
     */
    public function __construct(Application $application, Factory $factory, Locator $locator)
    {
        $this->application = $application;
        $this->factory = $factory;
        $this->locator = $locator;

        if ($factory->exists('DependencyContainer')) {
            $this->dependencyContainer = $factory->create('DependencyContainer', $factory, $locator);
        }
    }

    /**
     * @return MessengerInterface
     */
    private function getMessenger()
    {
        return $this->getTwig()->getExtension('TwigMessengerPlugin')->getMessenger();
    }

    /**
     * @param Container $container
     */
    public function setExternalDependencies(Container $container)
    {
        $dependencyContainer = $this->getDependencyContainer();
        if (isset($dependencyContainer)) {
            $this->getDependencyContainer()->setContainer($container);
        }
    }

    /**
     * @return Factory
     */
    protected function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return AbstractCommunicationDependencyContainer
     */
    public function getDependencyContainer()
    {
        return $this->dependencyContainer;
    }

    /**
     * TODO move to constructor
     *
     * @param AbstractFacade $facade
     */
    public function setOwnFacade(AbstractFacade $facade)
    {
        $this->facade = $facade;
    }

    /**
     * TODO move to constructor
     *
     * @param AbstractQueryContainer $queryContainer
     */
    public function setOwnQueryContainer(AbstractQueryContainer $queryContainer)
    {
        $this->queryContainer = $queryContainer;

        $dependencyContainer = $this->getDependencyContainer();
        if (isset($dependencyContainer)) {
            $this->getDependencyContainer()->setQueryContainer($queryContainer);
        }
    }

    /**
     * @return AbstractFacade
     */
    protected function getFacade()
    {
        return $this->facade;
    }

    /**
     * @return AbstractQueryContainer
     */
    protected function getQueryContainer()
    {
        return $this->queryContainer;
    }

    /**
     * @param string $url
     * @param int $status
     * @param array $headers
     *
     * @return RedirectResponse
     */
    protected function redirectResponse($url, $status = 302, $headers = [])
    {
        return new RedirectResponse($url, $status, $headers);
    }

    /**
     * @param array|null $data
     * @param int $status
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function jsonResponse($data = null, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param null $callback
     * @param int $status
     * @param array $headers
     *
     * @return StreamedResponse
     */
    protected function streamedResponse($callback = null, $status = 200, $headers = [])
    {
        $streamedResponse = new StreamedResponse($callback, $status, $headers);
        $streamedResponse->send();

        return $streamedResponse;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function viewResponse(array $data = [])
    {
        return $data;
    }

    /**
     * @param string $message
     *
     * @throws \ErrorException
     *
     * @return $this
     */
    protected function addMessageSuccess($message)
    {
        $this->getMessenger()->success($message);

        return $this;
    }

    /**
     * @param string $message
     *
     * @throws \Exception
     *
     * @return $this
     */
    protected function addMessageWarning($message)
    {
        $this->getMessenger()->warning($message);

        return $this;
    }

    /**
     * @param string $message
     *
     * @throws \ErrorException
     *
     * @return $this
     */
    protected function addMessageError($message)
    {
        $this->getMessenger()->error($message);

        return $this;
    }

    /**
     * @param string $type
     * @param null $data
     * @param array $options
     *
     * @return FormInterface
     */
    protected function createForm($type = 'form', $data = null, array $options = [])
    {
        /** @var FormFactory $formFactory */
        $formFactory = $this->application['form.factory'];

        return $formFactory->create($type, $data, $options);
    }

    /**
     */
    protected function clearBreadcrumbs()
    {
        $this->getTwig()->addGlobal('breadcrumbs', []);
    }

    /**
     * @throws \LogicException
     *
     * @return \Twig_Environment
     */
    private function getTwig()
    {
        $twig = $this->getApplication()['twig'];
        if ($twig === null) {
            throw new \LogicException('Twig environment not set up.');
        }

        return $twig;
    }

    /**
     * @return \Pimple
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * @param string $label
     * @param string $uri
     */
    protected function addBreadcrumb($label, $uri)
    {
        $twig = $this->getTwig();
        $globals = $twig->getGlobals();
        $breadcrumbs = $globals['breadcrumbs'];

        if ($breadcrumbs === null) {
            $breadcrumbs = [];
        }

        $breadcrumbs[] = [
            'label' => $label,
            'uri' => $uri,
        ];

        $twig->addGlobal('breadcrumbs', $breadcrumbs);
    }

    /**
     * @param string $uri
     */
    protected function setMenuHighlight($uri)
    {
        $this->getTwig()->addGlobal('menu_highlight', $uri);
    }

}
