<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Session\Communication\Plugin\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use SprykerEngine\Zed\Kernel\Communication\AbstractPlugin;
use SprykerFeature\Client\Session\Service\SessionClientInterface;
use SprykerFeature\Shared\Library\Config;
use SprykerFeature\Shared\Session\SessionConfig;
use SprykerFeature\Shared\Application\ApplicationConfig;
use SprykerFeature\Zed\Session\Business\Model\SessionFactory;

class SessionServiceProvider extends AbstractPlugin implements ServiceProviderInterface
{

    /**
     * @var SessionClientInterface
     */
    private $client;

    /**
     * @param SessionClientInterface $client
     *
     * @return void
     */
    public function setClient(SessionClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param Application $app
     *
     * @return void
     */
    public function register(Application $app)
    {
        $app['session.test'] = Config::get(SessionConfig::SESSION_IS_TEST, false);

        $app['session.storage.options'] = [
            'cookie_lifetime' => Config::get(ApplicationConfig::ZED_STORAGE_SESSION_TIME_TO_LIVE),
            'name' => Config::get(ApplicationConfig::ZED_STORAGE_SESSION_COOKIE_NAME),
        ];

        $this->client->setContainer($app['session']);
    }

    /**
     * @param Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        $saveHandler = Config::get(ApplicationConfig::ZED_SESSION_SAVE_HANDLER);
        $savePath = $this->getSavePath($saveHandler);

        $sessionHelper = new SessionFactory();

        switch ($saveHandler) {
            case SessionConfig::SESSION_HANDLER_COUCHBASE:
                $savePath = isset($savePath) && !empty($savePath) ? $savePath : null;

                $sessionHelper->registerCouchbaseSessionHandler($savePath);
                break;

            case SessionConfig::SESSION_HANDLER_MYSQL:
                $savePath = isset($savePath) && !empty($savePath) ? $savePath : null;
                $sessionHelper->registerMysqlSessionHandler($savePath);
                break;

            case SessionConfig::SESSION_HANDLER_REDIS:
                $savePath = isset($savePath) && !empty($savePath) ? $savePath : null;
                $sessionHelper->registerRedisSessionHandler($savePath);
                break;

            case SessionConfig::SESSION_HANDLER_FILE:
                $savePath = isset($savePath) && !empty($savePath) ? $savePath : null;
                $sessionHelper->registerFileSessionHandler($savePath);
                break;

            default:
                if (isset($saveHandler) && !empty($saveHandler)) {
                    ini_set('session.save_handler', $saveHandler);
                }
                if (isset($savePath) && !empty($savePath)) {
                    session_save_path($savePath);
                }
        }

        ini_set('session.auto_start', false);
    }

    /**
     * @param string $saveHandler
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function getSavePath($saveHandler)
    {
        $path = null;

        if (SessionConfig::SESSION_HANDLER_REDIS === $saveHandler) {
            $path = Config::get(ApplicationConfig::ZED_STORAGE_SESSION_REDIS_PROTOCOL)
                . '://' . Config::get(ApplicationConfig::ZED_STORAGE_SESSION_REDIS_HOST)
                . ':' . Config::get(ApplicationConfig::ZED_STORAGE_SESSION_REDIS_PORT);
        }

        if (SessionConfig::SESSION_HANDLER_FILE === $saveHandler) {
            $path = Config::get(ApplicationConfig::ZED_STORAGE_SESSION_FILE_PATH);
        }

        return $path;
    }

}
