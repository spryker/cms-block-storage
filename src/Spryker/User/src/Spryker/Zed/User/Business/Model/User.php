<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\User\Business\Model;

use Generated\Shared\Transfer\CollectionTransfer;
use Spryker\Zed\Library\Copy;
use Propel\Runtime\Collection\ObjectCollection;
use Generated\Shared\Transfer\UserTransfer;
use Orm\Zed\User\Persistence\Map\SpyUserTableMap;
use Spryker\Zed\User\UserConfig;
use Orm\Zed\User\Persistence\SpyUser;
use Spryker\Zed\User\Persistence\UserQueryContainer;
use Spryker\Zed\User\Business\Exception\UserNotFoundException;
use Spryker\Zed\User\Business\Exception\UsernameExistsException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class User implements UserInterface
{

    const USER_BUNDLE_SESSION_KEY = 'user';

    /**
     * @var \Spryker\Zed\User\Persistence\UserQueryContainer
     */
    protected $queryContainer;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * @var \Spryker\Zed\User\UserConfig
     */
    protected $settings;

    /**
     * @param \Spryker\Zed\User\Persistence\UserQueryContainer $queryContainer
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Spryker\Zed\User\UserConfig $settings
     */
    public function __construct(
        UserQueryContainer $queryContainer,
        SessionInterface $session,
        UserConfig $settings
    ) {
        $this->queryContainer = $queryContainer;
        $this->session = $session;
        $this->settings = $settings;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string $password
     *
     * @throws \Spryker\Zed\User\Business\Exception\UsernameExistsException
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function addUser($firstName, $lastName, $username, $password)
    {
        $userCheck = $this->hasUserByUsername($username);

        if ($userCheck === true) {
            throw new UsernameExistsException();
        }

        $transferUser = new UserTransfer();
        $transferUser->setFirstName($firstName);
        $transferUser->setLastName($lastName);
        $transferUser->setUsername($username);
        $transferUser->setPassword($password);

        return $this->save($transferUser);
    }

    /**
     * @param string $password
     *
     * @return string
     */
    public function encryptPassword($password)
    {
        return base64_encode(password_hash($password, PASSWORD_BCRYPT));
    }

    /**
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function validatePassword($password, $hash)
    {
        return password_verify($password, base64_decode($hash));
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function save(UserTransfer $userTransfer)
    {
        if ($userTransfer->getIdUser() !== null) {
            $userEntity = $this->getEntityUserById($userTransfer->getIdUser());
        } else {
            $userEntity = new SpyUser();
        }

        $userEntity->setFirstName($userTransfer->getFirstName());
        $userEntity->setLastName($userTransfer->getLastName());
        $userEntity->setUsername($userTransfer->getUsername());
        if ($userTransfer->getStatus() !== null) {
            $userEntity->setStatus($userTransfer->getStatus());
        }

        if ($userTransfer->getLastLogin() !== null) {
            $userEntity->setLastLogin($userTransfer->getLastLogin());
        }

        $password = $userTransfer->getPassword();
        if (!empty($password) && $this->isRawPassword($userTransfer->getPassword())) {
            $userEntity->setPassword($this->encryptPassword($userTransfer->getPassword()));
        }

        $userEntity->save();
        $userTransfer = $this->entityToTransfer($userEntity);

        return $userTransfer;
    }

    /**
     * @param int $idUser
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function removeUser($idUser)
    {
        $user = $this->getUserById($idUser);
        $user->setStatus('deleted');

        return $this->save($user);
    }

    /**
     * @throws \Spryker\Zed\User\Business\Exception\UserNotFoundException
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getUsers()
    {
        $results = $this->queryContainer->queryUsers()->find();

        if (($results instanceof ObjectCollection) === false) {
            throw new UserNotFoundException();
        }

        $collection = new TransferArrayObject();

        foreach ($results as $result) {
            $transfer = new UserTransfer();
            $collection->add(Copy::entityToTransfer($transfer, $result));
        }

        return $collection;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    private function isRawPassword($password)
    {
        $passwordInfo = password_get_info($password);

        return $passwordInfo['algo'] === 0;
    }

    /**
     * @param string $username
     *
     * @return bool
     */
    public function hasUserByUsername($username)
    {
        $amount = $this->queryContainer->queryUserByUsername($username)->count();

        return $amount > 0;
    }

    /**
     * @param int $idUser
     *
     * @return bool
     */
    public function hasUserById($idUser)
    {
        $amount = $this->queryContainer->queryUserById($idUser)->count();

        return $amount > 0;
    }

    /**
     * @param string $username
     *
     * @throws \Spryker\Zed\User\Business\Exception\UserNotFoundException
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getUserByUsername($username)
    {
        $entity = $this->queryContainer->queryUserByUsername($username)->findOne();

        if ($entity === null) {
            throw new UserNotFoundException();
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * @param int $id
     *
     * @throws \Spryker\Zed\User\Business\Exception\UserNotFoundException
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getUserById($id)
    {
        $entity = $this->queryContainer
            ->queryUserById($id)
            ->findOne();

        if ($entity === null) {
            throw new UserNotFoundException();
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * @param int $id
     *
     * @throws \Spryker\Zed\User\Business\Exception\UserNotFoundException
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getActiveUserById($id)
    {
        $entity = $this->queryContainer
            ->queryUserById($id)
            ->filterByStatus(SpyUserTableMap::COL_STATUS_ACTIVE)
            ->findOne();

        if ($entity === null) {
            throw new UserNotFoundException();
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * @param int $id
     *
     * @throws \Spryker\Zed\User\Business\Exception\UserNotFoundException
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getEntityUserById($id)
    {
        $entity = $this->queryContainer->queryUserById($id)->findOne();

        if ($entity === null) {
            throw new UserNotFoundException();
        }

        return $entity;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $user
     *
     * @return mixed
     */
    public function setCurrentUser(UserTransfer $user)
    {
        $key = sprintf('%s:currentUser', self::USER_BUNDLE_SESSION_KEY);

        return $this->session->set($key, serialize($user));
    }

    /**
     * @return bool
     */
    public function hasCurrentUser()
    {
        $key = sprintf('%s:currentUser', self::USER_BUNDLE_SESSION_KEY);
        $user = unserialize($this->session->get($key));

        return $user !== false;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $user
     *
     * @return bool
     */
    public function isSystemUser(UserTransfer $user)
    {
        $systemUsers = $this->settings->getSystemUsers();

        return in_array($user->getUsername(), $systemUsers);
    }

    /**
     * @return \Generated\Shared\Transfer\CollectionTransfer
     */
    public function getSystemUsers()
    {
        $systemUser = $this->settings->getSystemUsers();
        $collection = new CollectionTransfer();

        foreach ($systemUser as $username) {
            $transferUser = new UserTransfer();

            // TODO why setting the id? why is everything the username?
            $transferUser->setIdUser(0);

            $transferUser->setFirstName($username)
                ->setLastName($username)
                ->setUsername($username)
                ->setPassword($username);

            $collection->addUser($transferUser);
        }

        return $collection;
    }

    /**
     * @throws \Spryker\Zed\User\Business\Exception\UserNotFoundException
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getCurrentUser()
    {
        $key = sprintf('%s:currentUser', self::USER_BUNDLE_SESSION_KEY);
        $user = unserialize($this->session->get($key));

        if ($user === false) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * @param \Orm\Zed\User\Persistence\SpyUser $entity
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    protected function entityToTransfer(SpyUser $entity)
    {
        $transfer = new UserTransfer();
        $transfer = Copy::entityToTransfer($transfer, $entity);

        return $transfer;
    }

    /**
     * @param int $idUser
     *
     * @return bool
     */
    public function activateUser($idUser)
    {
        $userEntity = $this->queryContainer->queryUserById($idUser)->findOne();
        $userEntity->setStatus(SpyUserTableMap::COL_STATUS_ACTIVE);
        $rowsAffected = $userEntity->save();

        return $rowsAffected > 0;
    }

    /**
     * @param int $idUser
     *
     * @return bool
     */
    public function deactivateUser($idUser)
    {
        $userEntity = $this->queryContainer->queryUserById($idUser)->findOne();
        $userEntity->setStatus(SpyUserTableMap::COL_STATUS_BLOCKED);
        $rowsAffected = $userEntity->save();

        return $rowsAffected > 0;
    }

}
