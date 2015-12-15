<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Storage\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method StorageDependencyContainer getDependencyContainer()
 */
class StorageFacade extends AbstractFacade
{

    public function get($key)
    {
        return $this->getDependencyContainer()->createStorage()->get($key);
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getDependencyContainer()->createStorage()->getTotalCount();
    }

    /**
     * @return array
     */
    public function getTimestamps()
    {
        return $this->getDependencyContainer()->createStorage()->getTimestamps();
    }

    /**
     * @return int
     */
    public function deleteAll()
    {
        return $this->getDependencyContainer()->createStorage()->deleteAll();
    }

    /**
     * @param array $keys
     *
     * @return void
     */
    public function deleteMulti(array $keys)
    {
        $this->getDependencyContainer()->createStorage()->deleteMulti($keys);
    }

}
