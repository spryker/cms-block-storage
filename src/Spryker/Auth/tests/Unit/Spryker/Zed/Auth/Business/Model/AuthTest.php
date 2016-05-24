<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Auth\Business\Model;

use Generated\Shared\Transfer\UserTransfer;
use Spryker\Client\Session\SessionClient;
use Spryker\Zed\Auth\AuthConfig;
use Spryker\Zed\Auth\Business\Client\StaticToken;
use Spryker\Zed\Auth\Business\Model\Auth;
use Spryker\Zed\Auth\Dependency\Facade\AuthToUserBridge;
use Spryker\Zed\User\Business\UserFacade;

/**
 * @group Auth
 * @group Business
 * @group Model
 * @group Auth
 */
class AuthTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @const string
     */
    const USERNAME = 'test@test.com';

    /**
     * @return void
     */
    public function testSessionRegenerationOnLogin()
    {
        $userTransfer = $this->createUserTransfer(static::USERNAME);

        $userFacade = $this->createFacadeUser();
        $userFacade->expects($this->once())
            ->method('getUserByUsername')
            ->will($this->returnValue($userTransfer));

        $userFacade->expects($this->once())
            ->method('hasActiveUserByUsername')
            ->will($this->returnValue(true));

        $userFacade->expects($this->once())
            ->method('isValidPassword')
            ->will($this->returnValue(true));

        $authModel = $this->prepareSessionRegeneration($userFacade);
        $result = $authModel->authenticate(static::USERNAME, 'test');
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testSessionRegenerationOnLogout()
    {
        $authModel = $this->prepareSessionRegeneration($this->createFacadeUser());
        $authModel->logout();
    }

    /**
     * @param string $userName
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    protected function createUserTransfer($userName)
    {
        $userTransfer = new UserTransfer();
        $userTransfer
            ->setPassword('test')
            ->setIdUser(1)
            ->setFirstName('test')
            ->setLastName('test')
            ->setLastLogin('test')
            ->setUsername($userName);

        return $userTransfer;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Auth\Dependency\Facade\AuthToUserBridge
     */
    protected function createFacadeUser()
    {
        $userFacade = $this->getMock(
            AuthToUserBridge::class,
            ['getUserByUsername', 'hasActiveUserByUsername', 'isValidPassword', 'updateUser'],
            [new UserFacade()]
        );

        return $userFacade;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Client\Session\SessionClient
     */
    protected function createSessionClient()
    {
        $sessionClient = $this->getMock(
            SessionClient::class,
            ['set', 'migrate']
        );

        return $sessionClient;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Auth\Business\Client\StaticToken
     */
    protected function createStaticTokenClient()
    {
        $staticTokenClient = $this->getMock(
            StaticToken::class
        );

        return $staticTokenClient;
    }

    /**
     * @param \Spryker\Zed\Auth\Dependency\Facade\AuthToUserBridge $userFacade
     *
     * @return \Spryker\Zed\Auth\Business\Model\Auth
     */
    protected function prepareSessionRegeneration($userFacade)
    {
        $sessionClient = $this->createSessionClient();
        $authModel = new Auth(
            $sessionClient,
            $userFacade,
            new AuthConfig(),
            $this->createStaticTokenClient()
        );

        $this->checkMigrateIsCalled($sessionClient);

        return $authModel;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $sessionClient
     *
     * @return void
     */
    protected function checkMigrateIsCalled(\PHPUnit_Framework_MockObject_MockObject $sessionClient)
    {
        $sessionClient->expects($this->once())
            ->method('migrate')
            ->will($this->returnValue(true));
    }

}
