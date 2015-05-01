<?php

namespace SprykerFeature\Zed\User\Business\Model;

use Generated\Zed\Ide\AutoCompletion;
use SprykerEngine\Shared\Kernel\LocatorLocatorInterface;
use SprykerFeature\Zed\User\Persistence\UserQueryContainer;
use SprykerFeature\Zed\User\UserConfig;

class Installer implements InstallerInterface
{
    /**
     * @var UserQueryContainer
     */
    protected $queryContainer;

    /**
     * @var AutoCompletion
     */
    protected $locator;

    /**
     * @var UserConfig
     */
    protected $settings;

    /**
     * @param UserQueryContainer $queryContainer
     * @param LocatorLocatorInterface $locator
     * @param UserConfig $settings
     */
    public function __construct(
        UserQueryContainer $queryContainer,
        LocatorLocatorInterface $locator,
        UserConfig $settings
    ) {
        $this->queryContainer = $queryContainer;
        $this->locator = $locator;
        $this->settings = $settings;
    }

    /**
     * Main Installer Method
     */
    public function install()
    {
        $this->addUsers($this->settings->getInstallerUsers());

    }

    protected function addUsers(array $usersArray)
    {
        foreach ($usersArray as $user) {
            if ($this->queryContainer->queryUserByUsername($user['username'])->count() > 0) {
                continue;
            }

            $this->locator->user()
                          ->facade()
                          ->addUser($user['firstName'], $user['lastName'], $user['username'], $user['password']);
        }
    }
}
