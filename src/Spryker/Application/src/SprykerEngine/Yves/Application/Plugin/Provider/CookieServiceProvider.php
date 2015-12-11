<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerEngine\Yves\Application\Plugin\Provider;

use SprykerEngine\Yves\Application\Application as YvesApplication;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CookieServiceProvider implements ServiceProviderInterface
{

    /**
     * @var YvesApplication
     */
    private $app;

    /**
     * @param Application $app
     *
     * @return void
     */
    public function register(Application $app)
    {
        $this->app = $app;
        $app['cookies'] = $app->share(function () {
            return new \ArrayObject();
        });
    }

    /**
     * Handles transparent Cookie insertion
     *
     * @param FilterResponseEvent $event The event to handle
     *
     * @return void
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        foreach ($this->app['cookies'] as $cookie) {
            $response->headers->setCookie($cookie);
        }
    }

    /**
     * @param Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $app['dispatcher']->addListener(KernelEvents::RESPONSE, [$this, 'onKernelResponse'], -255);
    }

}
