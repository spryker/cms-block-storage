<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Application\Communication\Plugin\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use SprykerEngine\Shared\Kernel\Store;
use Silex\Provider\TranslationServiceProvider as SilexTranslationServiceProvider;
use SprykerFeature\Zed\Library\Translate;

class TranslationServiceProvider extends SilexTranslationServiceProvider implements ServiceProviderInterface
{

    /**
     * @param Application $app
     *
     * @return void
     */
    public function register(Application $app)
    {
        parent::register($app);
    }

    /**
     * @param Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $currentLanguage = Store::getInstance()->getCurrentLanguage();
        $pathToLanguageFile = APPLICATION_ROOT_DIR . '/config/Zed/language/' . $currentLanguage . '/lang.csv';

        $translator = new Translate(
            [
                'adapter' => 'csv',
                'content' => $pathToLanguageFile,
                'locale' => Store::getInstance()->getCurrentLocale(),
            ]
        );
        \Zend_Registry::set('Zend_Translate', $translator);
    }

}
