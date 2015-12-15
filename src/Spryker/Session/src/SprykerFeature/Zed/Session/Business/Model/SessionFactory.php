<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Session\Business\Model;

use Spryker\Shared\Config;
use Spryker\Shared\Session\Business\Model\SessionFactory as SharedSessionFactory;
use Spryker\Shared\Application\ApplicationConstants;

class SessionFactory extends SharedSessionFactory
{

    /**
     * @return int
     */
    public function getSessionLifetime()
    {
        $lifetime = (int) Config::get(ApplicationConstants::ZED_STORAGE_SESSION_TIME_TO_LIVE);

        return $lifetime;
    }

}
