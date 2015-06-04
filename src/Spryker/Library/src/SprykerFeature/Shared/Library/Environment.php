<?php

class SprykerFeature_Shared_Library_Environment
{

    const DEFAULT_ENVIRONMENT = 'production';

    const PRODUCTION = 'production';
    const STAGING = 'staging';
    const DEVELOPMENT = 'development';
    const TESTING = 'testing';

    /**
     * @var string
     */
    protected static $environment = APPLICATION_ENV;

    /**
     * @var SprykerFeature_Shared_Library_Environment
     */
    protected static $instance;

    /**
     * @return SprykerFeature_Shared_Library_Environment
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return bool
     */
    public static function isProduction()
    {
        return (self::$environment === self::PRODUCTION);
    }

    /**
     * @return bool
     */
    public static function isNotProduction()
    {
        return (self::$environment !== self::PRODUCTION);
    }

    /**
     * @return bool
     */
    public static function isStaging()
    {
        return (self::$environment === self::STAGING);
    }

    /**
     * @return bool
     */
    public static function isNotStaging()
    {
        return (self::$environment !== self::STAGING);
    }

    /**
     * @return bool
     */
    public static function isDevelopment()
    {
        return (self::$environment === self::DEVELOPMENT);
    }

    /**
     * @return bool
     */
    public static function isNotDevelopment()
    {
        return (self::$environment !== self::DEVELOPMENT);
    }

    /**
     * @return bool
     */
    public static function isTesting()
    {
        return (self::$environment === self::TESTING);
    }

    /**
     * @return bool
     */
    public static function isNotTesting()
    {
        return (self::$environment !== self::TESTING);
    }

    /**
     * @return string
     */
    public static function getEnvironment()
    {
        return self::$environment;
    }

    /**
     * @param string $environment
     *
     * @return string
     */
    public static function setEnvironment($environment)
    {
        self::$environment = $environment;
    }
}
