<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\System\Communication\Controller;

use SprykerFeature\Shared\Library\Config;
use SprykerFeature\Shared\System\SystemConfig;
use Symfony\Component\HttpFoundation\Request;
use SprykerFeature\Zed\System\Business\SystemSettings;
use SprykerFeature\Zed\Application\Communication\Controller\AbstractController;

class IndexController extends AbstractController
{

    const KEY_ZED_COOKIE_NAME = 'zed_cookie_name';
    const KEY_ZED_COOKIE_VALUE = 'zed_cookie_value';
    const KEY_YVES_COOKIE_NAME = 'yves_cookie_name';
    const KEY_YVES_COOKIE_VALUE = 'yves_cookie_value';
    const KEY_ZED_PORT = 'zed_port';
    const KEY_YVES_PORT = 'yves_port';

    public function indexAction()
    {
        $environment = \SprykerFeature_Shared_Library_Environment::isProduction() ? 'production' : 'staging';
        $hosts = $this->facadeSystem->getHosts($environment);
        $mappings = array();

        foreach ($hosts as $host) {
            $mappings[$host[SystemSettings::KEY_HOST]] = array(
                self::KEY_ZED_COOKIE_NAME => $this->facadeSystem->getCookieName($environment, 'zed'),
                self::KEY_ZED_COOKIE_VALUE => $this->facadeSystem->getCookieValueByHost($environment,
                    $host[SystemSettings::KEY_HOST],
                    \SprykerFeature_Zed_System_Business_Model_Loadbalancer_BigIP_IPv4::APPLICATION_NAME_ZED),

                self::KEY_ZED_PORT => $host[SystemSettings::KEY_ZED_PORT],

                self::KEY_YVES_COOKIE_NAME => $this->facadeSystem->getCookieName($environment, 'yves'),
                self::KEY_YVES_COOKIE_VALUE => $this->facadeSystem->getCookieValueByHost($environment,
                    $host[SystemSettings::KEY_HOST],
                    \SprykerFeature_Zed_System_Business_Model_Loadbalancer_BigIP_IPv4::APPLICATION_NAME_YVES),

                self::KEY_YVES_PORT => $host[SystemSettings::KEY_YVES_PORT],
            );
        }

        return $this->viewResponse([
            'mappings' => $mappings,
            'yvesUrl' => Config::get(SystemConfig::HOST_YVES)
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function setCookieAction(Request $request)
    {
        $cookieName = $request->query->get('cookie_name');
        $cookieValue = $request->query->get('cookie_value');
        setcookie($cookieName, $cookieValue, time() + 3600, '/');
        $this->addMessageSuccess('Cookie ' . $cookieName . ' updated with value(' . $cookieValue . ')');

        return $this->redirectResponse('/system/index');
    }


}
