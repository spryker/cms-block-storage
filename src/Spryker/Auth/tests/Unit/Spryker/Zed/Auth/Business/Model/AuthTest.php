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
    public function testNoReferenceSavedInSession()
    {
        $sessionClient = $this->createSessionClient();
        $authModel = new Auth(
            $sessionClient,
            $this->createFacadeUser(),
            new AuthConfig(),
            $this->createStaticTokenClient()
        );

        $userTransfer = $this->createUserTransfer(static::USERNAME);

        $sessionClient->expects($this->once())
            ->method('get')
            ->will($this->returnValue($userTransfer));

        $userFromSession = $authModel->getUserFromSession('testtoken')->setUsername('test3434');
        $this->assertNotEquals($userTransfer, $userFromSession);
    }

    /**
     * @return void
     */
    public function testAuthorise()
    {
        $sessionClient = $this->createSessionClient();
        $userFacade = $this->createFacadeUser();

        $authModel = new Auth(
            $sessionClient,
            $userFacade,
            new AuthConfig(),
            $this->createStaticTokenClient()
        );

        $userTransfer = $this->createUserTransfer(static::USERNAME);

        $userFacade->expects($this->once())
            ->method('getUserByUsername')
            ->will($this->returnValue($userTransfer));

        $userFacade->expects($this->once())
            ->method('hasActiveUserByUsername')
            ->will($this->returnValue(true));

        $userFacade->expects($this->once())
            ->method('isValidPassword')
            ->will($this->returnValue(true));

        // Check that session receives exactly the TO, which was passed.
        $sessionClient->expects($this->once())
            ->method('set')
            ->with($this->stringContains('auth'), $this->identicalTo($userTransfer));

        // Test that object is cloned inside authenticate.
        $userFacade->expects($this->once())
            ->method('updateUser')
            ->with(
                $this->logicalAnd(
                    $this->equalTo($userTransfer),
                    $this->logicalNot($this->identicalTo($userTransfer))
                )
            );

        $result = $authModel->authenticate(static::USERNAME, 'test');
        $this->assertTrue($result);
    }

    /**
     * @param string $userName
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    protected function createUserTransfer($userName)
    {
        $userTransfer = new UserTransfer();
        $userTransfer->setPassword('test')->setIdUser(1)->setFirstName('test')->setLastName('test')->setLastLogin('test')->setUsername($userName);
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
            ['get', 'set']
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

}
