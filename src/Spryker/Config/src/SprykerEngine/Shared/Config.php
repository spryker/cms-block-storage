<?php

namespace SprykerEngine\Shared;

class Config
{

    const CONFIG_FILE_PREFIX = '/config/Shared/config_';
    const CONFIG_FILE_SUFFIX = '.php';

    /**
     * @var null|\ArrayObject
     */
    protected static $config = null;

    /**
     * @var self
     */
    private static $instance;

    /**
     * @return Config
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $key
     * @throws \Exception
     *
     * @return string
     */
    public static function get($key)
    {
        if (empty(self::$config)) {
            self::init();
        }

        if (!self::hasValue($key)) {
            throw new \Exception(sprintf('Could not find config key "%s" in "%s"', $key, __CLASS__));
        }

        return self::$config[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function hasValue($key)
    {
        return isset(self::$config[$key]);
    }

    /**
     * @param null $environment
     */
    public static function init($environment = null)
    {
        if (is_null($environment)) {
            $environment = \SprykerFeature_Shared_Library_Environment::getInstance()->getEnvironment();
        }

        $storeName = \SprykerEngine\Shared\Kernel\Store::getInstance()->getStoreName();

        $config = new \ArrayObject();

        /**
         * e.g. config_default.php
         */
        self::buildConfig('default', $config);

        /**
         * e.g. config_default-production.php
         */
        self::buildConfig('default-' . $environment, $config);

        /**
         * e.g. config_default_DE.php
         */
        self::buildConfig('default_' . $storeName, $config);

        /**
         * e.g. config_default-production_DE.php
         */
        self::buildConfig('default-' . $environment . '_' . $storeName, $config);

        /**
         * e.g. config_local.php
         */
        self::buildConfig('local', $config);

        /**
         * e.g. config_local_DE.php
         */
        self::buildConfig('local_' . $storeName, $config);

        self::$config = $config;
    }

    /**
     * @param string $type
     * @param \ArrayObject $config
     *
     * @return \ArrayObject
     */
    protected static function buildConfig($type = null, \ArrayObject $config)
    {
        assert(is_string($type));

        $fileName = APPLICATION_ROOT_DIR . self::CONFIG_FILE_PREFIX . $type . self::CONFIG_FILE_SUFFIX;
        if (file_exists($fileName)) {
            include $fileName;
        }

        return $config;
    }

}
