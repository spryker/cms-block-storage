<?php

namespace SprykerFeature\Zed\Acl\Business;

use Generated\Shared\Transfer\RolesTransfer;
use Generated\Shared\Transfer\RulesTransfer;
use SprykerEngine\Zed\Kernel\Business\AbstractFacade;
use Generated\Shared\Transfer\GroupTransfer;
use Generated\Shared\Transfer\RoleTransfer;
use Generated\Shared\Transfer\RuleTransfer;
use Generated\Shared\Transfer\UserTransfer;

/**
 * @method AclDependencyContainer getDependencyContainer()
 */
class AclFacade extends AbstractFacade
{
    /**
     * Main Installer Method
     */
    public function install()
    {
        $this->getDependencyContainer()->createInstallerModel()->install();
    }

    /**
     * @param string $name
     *
     * @return GroupTransfer
     */
    public function addGroup($name)
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->addGroup($name);
    }

    /**
     * @param GroupTransfer $data
     *
     * @return GroupTransfer
     */
    public function updateGroup(GroupTransfer $data)
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->updateGroup($data);
    }

    /**
     * @param int $id
     *
     * @return GroupTransfer
     */
    public function getGroup($id)
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->getGroupById($id);
    }

    /**
     * @param $name
     *
     * @return GroupTransfer
     */
    public function getGroupByName($name)
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->getByName($name);
    }

    /**
     * @return GroupTransfer
     */
    public function getAllGroups()
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->getAllGroups();
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function existsRoleByName($name)
    {
        return $this->getDependencyContainer()
            ->createRoleModel()
            ->hasRoleName($name)
        ;
    }

    /**
     * @param int $id
     *
     * @return RoleTransfer
     */
    public function getRoleById($id)
    {
        return $this->getDependencyContainer()
            ->createRoleModel()
            ->getRoleById($id);
    }

    /**
     * @param $name
     *
     * @return RoleTransfer
     */
    public function getRoleByName($name)
    {
        return $this->getDependencyContainer()
            ->createRoleModel()
            ->getByName($name);
    }

    /**
     * @param string $name
     * @param int $idGroup
     *
     * @return RoleTransfer
     */
    public function addRole($name, $idGroup)
    {
        return $this->getDependencyContainer()
            ->createRoleModel()
            ->addRole($name, $idGroup);
    }

    /**
     * @param int $id
     *
     * @return RuleTransfer
     */
    public function getRule($id)
    {
        return $this->getDependencyContainer()
            ->createRuleModel()
            ->getRuleById($id);
    }

    /**
     * @param int $idRole
     * @param int $idGroup
     *
     * @return int
     */
    public function addRoleToGroup($idRole, $idGroup)
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->addRole($idGroup, $idRole);
    }

    /**
     * @param int $idUser
     * @param int $idGroup
     *
     * @return int
     */
    public function addUserToGroup($idUser, $idGroup)
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->addUser($idGroup, $idUser);
    }

    /**
     * @param int $idUser
     * @param int $idGroup
     *
     * @return bool
     */
    public function userHasGroupId($idUser, $idGroup)
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->hasUser($idGroup, $idUser);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasGroupByName($name)
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->hasGroupName($name);
    }

    /**
     * @param $idUser
     *
     * @return GroupTransfer
     */
    public function getUserGroup($idUser)
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->getUserGroup($idUser);
    }

    /**
     * @param int $idUser
     * @param int $idGroup
     */
    public function removeUserFromGroup($idUser, $idGroup)
    {
        $this->getDependencyContainer()
            ->createGroupModel()
            ->removeUser($idGroup, $idUser);
    }

    /**
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @param string $idRole
     * @param string $type
     *
     * @return RuleTransfer
     */
    public function addRule($bundle, $controller, $action, $idRole, $type = 'allow')
    {
        return $this->getDependencyContainer()
            ->createRuleModel()
            ->addRule($bundle, $controller, $action, $idRole, $type);
    }

    /**
     * @param int $idGroup
     *
     * @return RolesTransfer
     */
    public function getGroupRoles($idGroup)
    {
        return $this->getDependencyContainer()
            ->createRoleModel()
            ->getGroupRoles($idGroup);
    }

    /**
     * @param int $idGroup
     *
     * @return RulesTransfer
     */
    public function getGroupRules($idGroup)
    {
        return $this->getDependencyContainer()
            ->createRuleModel()
            ->findByGroupId($idGroup);
    }

    /**
     * @param int $idRole
     *
     * @return RulesTransfer
     */
    public function getRoleRules($idRole)
    {
        return $this->getDependencyContainer()
            ->createRuleModel()
            ->getRoleRules($idRole);
    }

    /**
     * @param int $idAclRole
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    public function existsRoleRule($idAclRole, $bundle, $controller, $action, $type)
    {
        return $this->getDependencyContainer()
            ->createRuleModel()
            ->existsRoleRule($idAclRole, $bundle, $controller, $action, $type);
    }

    /**
     * @param int $idUser
     *
     * @return RoleTransfer
     */
    public function getUserRoles($idUser)
    {
        return $this->getDependencyContainer()
            ->createRoleModel()
            ->getUserRoles($idUser);
    }

    /**
     * @param int $idGroup
     *
     * @return bool
     */
    public function removeGroup($idGroup)
    {
        return $this->getDependencyContainer()
            ->createGroupModel()
            ->removeGroupById($idGroup);
    }

    /**
     * @param int $idRole
     *
     * @return bool
     */
    public function removeRole($idRole)
    {
        return $this->getDependencyContainer()
            ->createRoleModel()
            ->removeRoleById($idRole);
    }

    /**
     * @param int $idRule
     *
     * @return bool
     */
    public function removeRule($idRule)
    {
        return $this->getDependencyContainer()
            ->createRuleModel()
            ->removeRuleById($idRule);
    }

    /**
     * @param UserTransfer $user
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    public function checkAccess(UserTransfer $user, $bundle, $controller, $action)
    {
        return $this->getDependencyContainer()
            ->createRuleModel()
            ->isAllowed($user, $bundle, $controller, $action);
    }

    /**
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    public function isIgnorable($bundle, $controller, $action)
    {
        return $this->getDependencyContainer()
            ->createRuleModel()
            ->isIgnorable($bundle, $controller, $action);
    }
}
