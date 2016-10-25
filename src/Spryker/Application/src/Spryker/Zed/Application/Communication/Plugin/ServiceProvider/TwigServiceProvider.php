<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Application\Communication\Plugin\ServiceProvider;

use FilesystemIterator;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Application\Business\Model\Twig\RouteResolver;
use Spryker\Zed\Gui\Communication\Form\Type\Extension\NoValidateTypeExtension;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Library\Twig\Loader\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig_Loader_Chain;

/**
 * @method \Spryker\Zed\Application\Business\ApplicationFacade getFacade()
 * @method \Spryker\Zed\Application\Communication\ApplicationCommunicationFactory getFactory()
 * @method \Spryker\Zed\Application\ApplicationConfig getConfig()
 */
class TwigServiceProvider extends AbstractPlugin implements ServiceProviderInterface
{

    /**
     * @var \Spryker\Yves\Application\Application
     */
    private $app;

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    public function register(Application $app)
    {
        $this->app = $app;

        $this->provideFormTypeExtension();
        $this->provideFormTypeTemplates();

        $app['twig.loader.zed'] = $app->share(function () {
            $namespaces = Config::get(ApplicationConstants::PROJECT_NAMESPACES);

            $storeName = Store::getInstance()->getStoreName();

            $paths = [];
            foreach ($namespaces as $namespace) {
                $paths[] = APPLICATION_SOURCE_DIR . '/' . $namespace . '/Zed/%s' . $storeName . '/Presentation/';
                $paths[] = APPLICATION_SOURCE_DIR . '/' . $namespace . '/Zed/%s/Presentation/';
            }
            $paths[] = $this->getConfig()->getBundlesDirectory() . '/%2$s/src/Spryker/Zed/%1$s/Presentation/';

            return new Filesystem($paths);
        });

        $app['twig.loader'] = $app->share(function ($app) {
            return new Twig_Loader_Chain(
                [
                    $app['twig.loader.zed'],
                    $app['twig.loader.filesystem'],
                ]
            );
        });

        $app['twig.options'] = Config::get(ApplicationConstants::ZED_TWIG_OPTIONS);

        $app['twig.global.variables'] = $app->share(function () {
            return [];
        });

        $app['twig.global.variables'] = $app->share(function () {
             return [];
        });

        $app['twig'] = $app->share(
            $app->extend(
                'twig',
                function (\Twig_Environment $twig) use ($app) {
                    foreach ($app['twig.global.variables'] as $name => $value) {
                        $twig->addGlobal($name, $value);
                    }

                    return $twig;
                }
            )
        );
    }

    /**
     * Handles string responses.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event The event to handle
     *
     * @return void
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();

        if (empty($response) || is_array($response)) {
            $response = $this->render((array)$response);
            if ($response instanceof Response) {
                $event->setResponse($response);
            }
        }
    }

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $app['dispatcher']->addListener(KernelEvents::VIEW, [$this, 'onKernelView']);
    }

    /**
     * Renders the template for the current controller/action
     *
     * @param array $parameters
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function render(array $parameters = [])
    {
        $request = $this->app['request_stack']->getCurrentRequest();
        $controller = $request->attributes->get('_controller');

        if (!is_string($controller) || empty($controller)) {
            return;
        }

        if (isset($parameters['alternativeRoute'])) {
            $route = (string)$parameters['alternativeRoute'];
        } else {
            $route = (new RouteResolver())
                ->buildRouteFromControllerServiceName($controller);
        }

        return $this->app->render('@' . $route . '.twig', $parameters);
    }

    /**
     * @return void
     */
    protected function provideFormTypeExtension()
    {
        $this->app['form.type.extensions'] = $this->app->share(function () {
            return [
                new NoValidateTypeExtension(),
            ];
        });
    }

    /**
     * @return void
     */
    protected function provideFormTypeTemplates()
    {
        $guiDirectory = $path = $this->getConfig()->getBundlesDirectory() . '/Gui';
        if (!is_dir($guiDirectory)) {
            $guiDirectory = $path = $this->getConfig()->getBundlesDirectory() . '/gui';
        }
        $path = $guiDirectory . '/src/Spryker/Zed/Gui/Presentation/Form/Type';

        $this->app['twig.loader.filesystem']->addPath(
            $path
        );

        $files = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS | FilesystemIterator::KEY_AS_PATHNAME);

        $typeTemplates = [];
        foreach ($files as $file) {
            $typeTemplates[] = $file->getFilename();
        }

        $this->app['twig.form.templates'] = array_merge([
            'bootstrap_3_layout.html.twig',
        ], $typeTemplates);
    }

}
