<?php

namespace SprykerFeature\Zed\Acl\Business\Model;

use Generated\Shared\Transfer\RoleTransfer;
use Generated\Shared\Transfer\RolesTransfer;
use Generated\Shared\Transfer\GroupTransfer;
use Generated\Shared\Transfer\GroupsTransfer;

use SprykerFeature\Zed\Acl\Persistence\Propel\SpyAclGroup;
use SprykerFeature\Zed\Acl\Persistence\Propel\SpyAclGroupsHasRoles;
use SprykerFeature\Zed\Acl\Persistence\Propel\SpyAclUserHasGroup;
use SprykerFeature\Zed\Library\Copy;
use SprykerFeature\Zed\Acl\Business\Exception\EmptyEntityException;
use SprykerFeature\Zed\Acl\Persistence\AclQueryContainer;
use SprykerFeature\Zed\Acl\Business\Exception\GroupNameExistsException;
use SprykerFeature\Zed\Acl\Business\Exception\GroupNotFoundException;
use SprykerFeature\Zed\Acl\Business\Exception\GroupAlreadyHasRoleException;
use SprykerFeature\Zed\Acl\Business\Exception\GroupAlreadyHasUserException;

class Group implements GroupInterface
{
    /**
     * @var AclQueryContainer
     */
    protected $queryContainer;

    /**
     * @param AclQueryContainer $queryContainer
     */
    public function __construct(AclQueryContainer $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param string $name
     *
     * @return GroupTransfer
     */
    public function addGroup($name)
    {
        $data = new GroupTransfer();
        $data->setName($name);
        $this->assertGroupHasName($data);

        return $this->save($data);
    }

    /**
     * @param GroupTransfer $group
     *
     * @return GroupTransfer
     */
    public function updateGroup(GroupTransfer $group)
    {
        $original = $this->getGroupById($group->getIdAclGroup());

        if ($group->getName() !== $original->getName()) {
            $this->assertGroupHasName($group);
        }

        return $this->save($group);
    }

    /**
     * @param GroupTransfer $group
     *
     * @return GroupTransfer
     */
    public function save(GroupTransfer $group)
    {
        $this->assertGroupExists($group);

        if ($group->getIdAclGroup() !== null) {
            $entity = $this->getEntityGroupById($group->getIdAclGroup());
        } else {
            $entity = new SpyAclGroup();
        }

        $entity->setName($group->getName());
        $entity->save();

        $transfer = new GroupTransfer();
        $transfer = Copy::entityToTransfer($transfer, $entity);

        return $transfer;
    }

    /**
     * @param int $id
     *
     * @return SpyAclGroup
     * @throws GroupNotFoundException
     */
    public function getEntityGroupById($id)
    {
        $entity = $this->queryContainer->queryGroupById($id)->findOne();

        if ($entity === null) {
            throw new GroupNotFoundException();
        }

        return $entity;
    }

    /**
     * @param int $idGroup
     *
     * @return bool
     */
    public function hasGroup($idGroup)
    {
        $amount = $this->queryContainer->queryGroupById($idGroup)->count();

        return $amount > 0;
    }

    /**
     * @param int $name
     *
     * @return bool
     */
    public function hasGroupName($name)
    {
        $amount = $this->queryContainer->queryGroupByName($name)->count();

        return $amount > 0;
    }

    /**
     * @param int $idGroup
     * @param int $idRole
     *
     * @return bool
     */
    public function hasRole($idGroup, $idRole)
    {
        $amount = $this->queryContainer->queryGroupHasRoleById($idGroup, $idRole)->count();

        return $amount > 0;
    }

    /**
     * @param int $idGroup
     * @param int $idUser
     *
     * @return bool
     */
    public function hasUser($idGroup, $idUser)
    {
        $amount = $this->queryContainer->queryUserHasGroupById($idGroup, $idUser)->count();

        return $amount > 0;
    }

    /**
     * @param int $idUser
     *
     * @return GroupTransfer
     */
    public function getUserGroup($idUser)
    {
        $entity = $this->queryContainer->queryUserGroupByIdUser($idUser)->findOne();

        $transfer = new GroupTransfer();
        $transfer = Copy::entityToTransfer($transfer, $entity);

        return $transfer;
    }

    /**
     * @param int $idRole
     * @param int $idGroup
     *
     * @return int
     * @throws GroupAlreadyHasRoleException
     */
    public function addRoleToGroup($idRole, $idGroup)
    {
        if ($this->hasRole($idGroup, $idRole)) {
            throw new GroupAlreadyHasRoleException();
        }

        $entity = new SpyAclGroupsHasRoles();

        $entity->setFkAclGroup($idGroup)
            ->setFkAclRole($idRole);

        return $entity->save();
    }

    /**
     * @param int $idGroup
     * @param int $idUser
     *
     * @return int
     * @throws GroupAlreadyHasUserException
     */
    public function addUser($idGroup, $idUser)
    {
        if ($this->hasUser($idGroup, $idUser)) {
            return;
            throw new GroupAlreadyHasUserException();
        }

        $entity = new SpyAclUserHasGroup();

        $entity->setFkAclGroup($idGroup)
            ->setFkUserUser($idUser);

        return $entity->save();
    }

    /**
     * @param int $idGroup
     * @param int $idUser
     */
    public function removeUser($idGroup, $idUser)
    {
        $entity = $this->queryContainer->queryUserHasGroupById($idGroup, $idUser)->findOne();

        //TODO guard against NPE
        $entity->delete();
    }

    /**
     * @return GroupTransfer
     */
    public function getAllGroups()
    {
        $collection = new GroupsTransfer();

        $results = $this->queryContainer
            ->queryGroup()
            ->find();

        foreach ($results as $result) {
            $transfer = new GroupTransfer();
            $collection->addGroup(Copy::entityToTransfer($transfer, $result));
        }

        return $collection;
    }

    /**
     * @param string $name
     *
     * @return GroupTransfer
     */
    public function getByName($name)
    {
        $entity = $this->queryContainer->queryGroupByName($name)->findOne();

        $transfer = new GroupTransfer();

        $transfer = Copy::entityToTransfer($transfer, $entity);

        return $transfer;
    }

    /**
     * @param int $id
     *
     * @return GroupTransfer
     * @throws GroupNotFoundException
     */
    public function getGroupById($id)
    {
        $entity = $this->getGroupEntityById($id);

        $transfer = new GroupTransfer();

        $transfer = Copy::entityToTransfer($transfer, $entity);

        return $transfer;
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws GroupNotFoundException
     */
    public function removeGroupById($id)
    {
        $entity = $this->queryContainer
            ->queryGroupById($id)
            ->delete();

        if ($entity <= 0) {
            throw new GroupNotFoundException();
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return SpyAclGroup
     * @throws EmptyEntityException
     */
    protected function getGroupEntityById($id)
    {
        $entity = $this->queryContainer->queryGroupById($id)->findOne();

        if ($entity === null) {
            throw new EmptyEntityException();
        }

        return $entity;
    }

    /**
     * @param int $idGroup
     *
     * @return RoleTransfer
     * @throws GroupNotFoundException
     */
    public function getRoles($idGroup)
    {
        $results = $this->queryContainer
            ->queryGroupRoles($idGroup)
            ->find();

        $collection = new RolesTransfer();

        foreach ($results as $result) {
            $transfer = new RoleTransfer();
            Copy::entityToTransfer($transfer, $result);
            $collection->addRole($transfer);
        }

        return $collection;
    }

    /**
     * @param GroupTransfer $group
     *
     * @throws GroupNameExistsException
     */
    public function assertGroupHasName(GroupTransfer $group)
    {
        if ($this->hasGroupName($group->getName()) === true) {
            throw new GroupNameExistsException();
        }
    }

    /**
     * @param GroupTransfer $group
     *
     * @throws GroupNotFoundException
     */
    public function assertGroupExists(GroupTransfer $group)
    {
        if ($group->getIdAclGroup() !== null && $this->hasGroup($group->getIdAclGroup()) === false) {
            throw new GroupNotFoundException();
        }
    }
}
