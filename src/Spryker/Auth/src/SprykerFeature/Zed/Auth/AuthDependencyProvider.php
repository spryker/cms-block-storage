<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Auth;

use SprykerEngine\Zed\Kernel\AbstractBundleDependencyProvider;
use SprykerEngine\Zed\Kernel\Container;
use SprykerFeature\Zed\Auth\Dependency\Plugin\AuthPasswordResetSenderInterface;

class AuthDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_USER = 'facade user';
    const PASSWORD_RESET_SENDER = 'Password reset sender';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container[self::FACADE_USER] = function (Container $container) {
            return $container->getLocator()->user()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[self::FACADE_USER] = function (Container $container) {
            return $container->getLocator()->user()->facade();
        };

        $container[self::PASSWORD_RESET_SENDER] = function (Container $container) {
            return $this->getPasswordResetNotificationSender($container);
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return AuthPasswordResetSenderInterface|null;
     */
    protected function getPasswordResetNotificationSender(Container $container)
    {
        return null;
    }

}
