<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Shared\Library;

class System
{

    /**
     * @var string
     */
    protected static $hostname;

    /**
     * @return string
     */
    public static function getHostname()
    {
        if (!isset(self::$hostname)) {
            self::$hostname = (gethostname()) ?: php_uname('n');
        }

        return self::$hostname;
    }

}
