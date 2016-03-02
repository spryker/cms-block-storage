<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Session\Business\Model;

use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Library\Environment;
use Spryker\Shared\NewRelic\Api;
use Spryker\Shared\Session\Business\Handler\SessionHandlerCouchbase;
use Spryker\Shared\Session\Business\Handler\SessionHandlerFile;
use Spryker\Shared\Session\Business\Handler\SessionHandlerMysql;
use Spryker\Shared\Session\Business\Handler\SessionHandlerRedis;

abstract class SessionFactory
{

    const BUCKET_NAME_POSTFIX = 'sessions';
    const PASSWORD = 'password';
    const USER = 'user';

    /**
     * @param string $savePath e.g. '10.10.10.1:8091;10.10.10.2:8091'
     *
     * @return \Spryker\Shared\Session\Business\Handler\SessionHandlerCouchbase
     */
    public function registerCouchbaseSessionHandler($savePath)
    {
        // get the credentials from the first defined couchbase host
        $credentials = $this->getCredentialsFromSavePathSegment(explode(';', $savePath)[0]);
        $user = !empty($credentials) && array_key_exists(self::USER, $credentials) ? $credentials[self::USER] : null;
        $password = !empty($credentials) && array_key_exists(self::PASSWORD, $credentials) ? $credentials[self::PASSWORD] : null;
        $hosts = $this->getHostsFromSavePath($savePath);
        $lifetime = $this->getSessionLifetime();

        $handler = new SessionHandlerCouchbase($this->getNewRelicApi(), $hosts, $user, $password, $this->getBucketName(), true, $lifetime);
        $this->setSessionSaveHandler($handler);

        return $handler;
    }

    /**
     * @param string $savePath e.g. '10.10.10.1:3306;10.10.10.2:3306'
     *
     * @return \Spryker\Shared\Session\Business\Handler\SessionHandlerMysql
     */
    public function registerMysqlSessionHandler($savePath)
    {
        $credentials = $this->getCredentialsFromSavePathSegment(explode(';', $savePath)[0]);
        $user = !empty($credentials) && array_key_exists(self::USER, $credentials) ? $credentials[self::USER] : null;
        $password = !empty($credentials) && array_key_exists(self::PASSWORD, $credentials) ? $credentials[self::PASSWORD] : null;
        $hosts = $this->getHostsFromSavePath($savePath);
        $lifetime = $this->getSessionLifetime();

        $handler = new SessionHandlerMysql($this->getNewRelicApi(), $hosts, $user, $password, $lifetime);
        $this->setSessionSaveHandler($handler);

        return $handler;
    }

    /**
     * @param string $savePath
     *
     * @return \Spryker\Shared\Session\Business\Handler\SessionHandlerRedis
     */
    public function registerRedisSessionHandler($savePath)
    {
        $lifetime = $this->getSessionLifetime();
        $handler = new SessionHandlerRedis($savePath, $lifetime, $this->getNewRelicApi());
        $this->setSessionSaveHandler($handler);

        return $handler;
    }

    /**
     * @param string $savePath
     *
     * @return \Spryker\Shared\Session\Business\Handler\SessionHandlerFile
     */
    public function registerFileSessionHandler($savePath)
    {
        $lifetime = $this->getSessionLifetime();
        $handler = new SessionHandlerFile($savePath, $lifetime, $this->getNewRelicApi());
        $this->setSessionSaveHandler($handler);

        return $handler;
    }

    /**
     * @return int
     */
    abstract protected function getSessionLifetime();

    /**
     * @param \SessionHandlerInterface $handler
     *
     * @return void
     */
    protected function setSessionSaveHandler($handler)
    {
        session_set_save_handler(
            [$handler, 'open'],
            [$handler, 'close'],
            [$handler, 'read'],
            [$handler, 'write'],
            [$handler, 'destroy'],
            [$handler, 'gc']
        );
    }

    /**
     * @return string
     */
    protected function getBucketName()
    {
        $storeName = Store::getInstance()->getStoreName();
        $environment = Environment::getInstance()->getEnvironment();

        return $storeName . '_' . $environment . '_' . self::BUCKET_NAME_POSTFIX;
    }

    /**
     * @param string $savePathSegment
     *
     * @return array
     */
    protected function getCredentialsFromSavePathSegment($savePathSegment)
    {
        if (strstr($savePathSegment, '@')) {
            $credentials = explode('@', $savePathSegment)[0];

            if (strstr($credentials, ':')) {
                $parts = explode(':', $credentials);

                return [
                    self::USER => $parts[0],
                    self::PASSWORD => $parts[1],
                ];
            } else {
                return [self::USER => $credentials];
            }
        }

        return [];
    }

    /**
     * @param string $savePath
     *
     * @return array
     */
    protected function getHostsFromSavePath($savePath)
    {
        $hosts = [];
        $hostsInfo = explode(';', $savePath);

        foreach ($hostsInfo as $hostInfo) {
            if (strstr($hostInfo, '@')) {
                $hosts[] = explode('@', $hostInfo)[1];
            } else {
                $hosts[] = $hostInfo;
            }
        }

        return $hosts;
    }

    /**
     * @return \Spryker\Shared\NewRelic\Api
     */
    protected function getNewRelicApi()
    {
        return new Api();
    }

}
