<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\NewRelic\Communication\Plugin;

use Spryker\Shared\NewRelic\NewRelicApiInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @method \Spryker\Zed\NewRelic\Communication\NewRelicCommunicationFactory getFactory()
 */
class GatewayControllerListener extends AbstractPlugin implements EventSubscriberInterface
{

    /**
     * @var \Spryker\Shared\NewRelic\NewRelicApiInterface
     */
    protected $newRelicApi;

    /**
     * @param \Spryker\Shared\NewRelic\NewRelicApiInterface $newRelicApi
     */
    public function __construct(NewRelicApiInterface $newRelicApi)
    {
        $this->newRelicApi = $newRelicApi;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     *
     * @return void
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if ($request->attributes->has('controller') && $request->attributes->get('controller') === 'gateway') {
            $this->newRelicApi->addCustomParameter('Call_from', 'Yves');
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController'],
        ];
    }

}
