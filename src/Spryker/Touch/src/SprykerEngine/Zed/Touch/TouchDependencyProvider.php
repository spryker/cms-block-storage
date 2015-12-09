<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerEngine\Zed\Touch;

use SprykerEngine\Zed\Propel\Communication\Plugin\Connection;
use SprykerEngine\Zed\Kernel\AbstractBundleDependencyProvider;
use SprykerEngine\Zed\Kernel\Container;

class TouchDependencyProvider extends AbstractBundleDependencyProvider
{

    const PLUGIN_PROPEL_CONNECTION = 'propel connection plugin';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[self::PLUGIN_PROPEL_CONNECTION] = function () {
            return (new Connection())->get();
        };

        return $container;
    }

}
