<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Storage;

use Spryker\Client\Kernel\AbstractClient;
use Spryker\Client\Storage\Redis\Service;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Client\Storage\StorageFactory getFactory()
 */
class StorageClient extends AbstractClient implements StorageClientInterface
{

    const KEY_USED = 'used';
    const KEY_NEW = 'new';
    const KEY_INIT = 'init';

    /**
     * All keys which have been used for the last request with same URL
     *
     * @var array
     */
    protected static $cachedKeys;

    /**
     * Pre-loaded values for this URL from Storage
     *
     * @var array
     */
    protected static $bufferedValues;

    /**
     * @var \Spryker\Client\Storage\StorageClientInterface
     */
    protected static $service;

    /**
     * @api
     *
     * @return \Spryker\Client\Storage\StorageClientInterface $service
     */
    public function getService()
    {
        if (self::$service === null) {
            self::$service = $this->getFactory()->createCachedService();
        }

        return self::$service;
    }

    /**
     * @api
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     *
     * @return void
     */
    public function set($key, $value, $ttl = null)
    {
        $this->getService()->set($key, $value, $ttl);
    }

    /**
     * @api
     *
     * @param array $items
     *
     * @return void
     */
    public function setMulti(array $items)
    {
        $this->getService()->setMulti($items);
    }

    /**
     * @api
     *
     * @param string $key
     *
     * @return void
     */
    public function delete($key)
    {
        $this->getService()->delete($key);
    }

    /**
     * @api
     *
     * @param array $keys
     *
     * @return void
     */
    public function deleteMulti(array $keys)
    {
        $this->getService()->deleteMulti($keys);
    }

    /**
     * @api
     *
     * @return int
     */
    public function deleteAll()
    {
        return $this->getService()->deleteAll();
    }

    /**
     * @api
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (!isset(self::$cachedKeys)) {
            $this->loadKeysFromCache();
        }

        if (!isset(self::$bufferedValues)) {
            $this->loadAllValues();
        }

        if (array_key_exists($key, self::$bufferedValues)) {
            self::$cachedKeys[$key] = self::KEY_USED;
            return self::$bufferedValues[$key];
        }

        self::$cachedKeys[$key] = self::KEY_NEW;

        return $this->getService()->get($key);
    }

    /**
     * @api
     *
     * @param array $keys
     *
     * @return array
     */
    public function getMulti(array $keys)
    {
        return $this->getService()->getMulti($keys);
    }

    /**
     * @api
     *
     * @return array
     */
    public function getStats()
    {
        return $this->getService()->getStats();
    }

    /**
     * @api
     *
     * @return array
     */
    public function getAllKeys()
    {
        return $this->getService()->getAllKeys();
    }

    /**
     * @api
     *
     * @return void
     */
    public function resetAccessStats()
    {
        $this->getService()->resetAccessStats();
    }

    /**
     * @api
     *
     * @return array
     */
    public function getAccessStats()
    {
        return $this->getService()->getAccessStats();
    }

    /**
     * @api
     *
     * @return int
     */
    public function getCountItems()
    {
        return $this->getService()->getCountItems();
    }

    /**
     * @return void
     */
    protected function loadKeysFromCache()
    {
        self::$cachedKeys = [];
        $cacheKey = self::generateCacheKey();

        if (!empty($cacheKey)) {
            $cachedKeys = $this->getService()->get($cacheKey);

            if (!empty($cachedKeys) && is_array($cachedKeys)) {
                foreach ($cachedKeys as $key) {
                    self::$cachedKeys[$key] = self::KEY_INIT;
                }
            }
        }
    }

    /**
     * Pre-Loads all values from storage with mget()
     *
     * @return void
     */
    protected function loadAllValues()
    {
        self::$bufferedValues = [];

        if (!empty(self::$cachedKeys) && is_array(self::$cachedKeys)) {
            $values = $this->getService()->getMulti(array_keys(self::$cachedKeys));

            if (!empty($values) && is_array($values)) {
                foreach ($values as $key => $value) {
                    $keySuffix = substr($key, strlen(Service::KV_PREFIX));
                    self::$bufferedValues[$keySuffix] = $this->jsonDecode($value);
                }
            }
        }
    }

    /**
     * @api
     *
     * @param mixed $pattern
     *
     * @return array
     */
    public function getKeys($pattern = '*')
    {
        return $this->getService()->getKeys($pattern);
    }

    /**
     * @api
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     */
    public function persistCacheForRequest(Request $request)
    {
        static::persistCache($request);
    }

    /**
     * @api
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     *
     * @deprecated Use persistRequestCache() instead.
     */
    public static function persistCache(Request $request = null)
    {
        $cacheKey = static::generateCacheKey($request);
        if (!empty($cacheKey) && is_array(self::$cachedKeys)) {
            $updateCache = false;
            foreach (self::$cachedKeys as $key => $status) {
                if ($status === self::KEY_INIT) {
                    unset(self::$cachedKeys[$key]);
                }

                if ($status !== self::KEY_USED) {
                    $updateCache = true;
                    break;
                }
            }

            if ($updateCache) {
                $ttl = 86400; // TTL = 1 day to avoid artifacts in Storage
                self::$service->set($cacheKey, json_encode(array_keys(self::$cachedKeys)), $ttl);
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected static function generateCacheKey(Request $request = null)
    {
        if ($request) {
            $requestUri = $request->getRequestUri();
            $serverName = $request->server->get('SERVER_NAME');
        } else {
            $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
            $serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
        }

        if ($requestUri === null || $serverName === null) {
            return '';
        }

        return 'StorageClient_' . $serverName . $requestUri;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function jsonDecode($value)
    {
        $result = json_decode($value, true);

        if (json_last_error() === \JSON_ERROR_SYNTAX) {
            return $value;
        }

        return $result;
    }

}
