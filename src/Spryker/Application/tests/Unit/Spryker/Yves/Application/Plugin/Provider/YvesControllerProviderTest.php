<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Yves\Application\Plugin\Provider;

use Silex\Application;
use Silex\Controller;
use Unit\Spryker\Yves\Application\Plugin\Provider\Fixtures\ControllerProviderMock;

/**
 * @group Unit
 * @group Spryker
 * @group Yves
 * @group Application
 * @group Plugin
 * @group Provider
 * @group YvesControllerProviderTest
 */
class YvesControllerProviderTest extends \PHPUnit_Framework_TestCase
{

    const METHOD_REQUIRE_HTTP = 'requireHttp';
    const METHOD_REQUIRE_HTTPS = 'requireHttps';

    /**
     * @return void
     */
    public function testWithoutSslConfigurationRequireHttpIsNotCalled()
    {
        $app = new Application();
        $controllerMock = $this->getControllerMock(self::METHOD_REQUIRE_HTTP, $this->never());
        $controllerProviderMock = $this->createControllerProviderMock(null, $controllerMock);
        $controllerProviderMock->defineControllers($app);
    }

    /**
     * @return void
     */
    public function testWithoutSslConfigurationRequireHttpsIsNotCalled()
    {
        $app = new Application();
        $controllerMock = $this->getControllerMock(self::METHOD_REQUIRE_HTTPS, $this->never());
        $controllerProviderMock = $this->createControllerProviderMock(null, $controllerMock);
        $controllerProviderMock->defineControllers($app);
    }

    /**
     * @return void
     */
    public function testWhenSslEnabledFalseRequireHttpIsCalled()
    {
        $app = new Application();
        $controllerMock = $this->getControllerMock(self::METHOD_REQUIRE_HTTP, $this->once());
        $controllerProviderMock = $this->createControllerProviderMock(false, $controllerMock);
        $controllerProviderMock->defineControllers($app);
    }

    /**
     * @return void
     */
    public function testWhenSslEnabledTrueRequireHttpsIsCalled()
    {
        $app = new Application();
        $controllerMock = $this->getControllerMock(self::METHOD_REQUIRE_HTTPS, $this->once());
        $controllerProviderMock = $this->createControllerProviderMock(true, $controllerMock);
        $controllerProviderMock->defineControllers($app);
    }

    /**
     * @return void
     */
    public function testWhenSslEnabledTrueRequireHttpsWithExcludedUrlIsNotCalled()
    {
        $app = new Application();
        $controllerMock = $this->getControllerMock(self::METHOD_REQUIRE_HTTPS, $this->never());
        $controllerProviderMock = $this->createControllerProviderMock(true, $controllerMock, ['foo' => '/foo']);
        $controllerProviderMock->defineControllers($app);
    }

    /**
     * @param bool $ssl
     * @param \Silex\Controller $controller
     * @param array $urls
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Unit\Spryker\Yves\Application\Plugin\Provider\Fixtures\ControllerProviderMock
     */
    protected function createControllerProviderMock($ssl, $controller, array $urls = [])
    {
        $controllerProviderMock = $this->getMockBuilder(ControllerProviderMock::class)->setMethods(['getService', 'getController', 'getExcludedUrls'])->setConstructorArgs([$ssl])->getMock();
        $controllerProviderMock->method('getService')->willReturn('');
        $controllerProviderMock->method('getController')->willReturn($controller);
        $controllerProviderMock->method('getExcludedUrls')->willReturn($urls);

        return $controllerProviderMock;
    }

    /**
     * @param string $methodName
     * @param \PHPUnit_Framework_MockObject_Matcher_InvokedCount $callTimes
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Silex\Controller
     */
    private function getControllerMock($methodName, $callTimes)
    {
        $controllerMock = $this->getMock(Controller::class, [], [], '', false);
        $controllerMock
            ->expects($callTimes)
            ->method('__call')
            ->with(
                $this->equalTo($methodName),
                $this->equalTo([])
            );

        return $controllerMock;
    }

}
