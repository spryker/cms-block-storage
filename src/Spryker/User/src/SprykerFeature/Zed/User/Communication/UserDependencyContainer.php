<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\User\Communication;

use Generated\Zed\Ide\FactoryAutoCompletion\UserCommunication;
use SprykerEngine\Zed\Kernel\Communication\AbstractCommunicationDependencyContainer;
use SprykerFeature\Zed\Acl\Business\AclFacade;
use SprykerFeature\Zed\User\Communication\Form\UserCreateForm;
use SprykerFeature\Zed\User\Communication\Form\UserUpdateForm;
use SprykerFeature\Zed\User\Communication\Table\UsersTable;
use SprykerFeature\Zed\User\Communication\Form\ResetPasswordForm;
use SprykerFeature\Zed\User\Persistence\UserQueryContainer;
use SprykerFeature\Zed\User\UserDependencyProvider;

/**
 * @method UserCommunication getFactory()
 * @method UserQueryContainer getQueryContainer()
 */
class UserDependencyContainer extends AbstractCommunicationDependencyContainer
{

    /**
     * @return ResetPasswordForm
     */
    public function createResetPasswordForm()
    {
        return new ResetPasswordForm();
    }

    /**
     * @return UsersTable
     */
    public function createUserTable()
    {
        return new UsersTable(
            $this->getQueryContainer()
        );
    }

    /**
     * @return UserCreateForm
     */
    public function createUserForm()
    {
        return new UserCreateForm();
    }

    /**
     * @param int $idUser
     *
     * @return UserUpdateForm
     */
    public function createUpdateUserForm($idUser)
    {
        return new UserUpdateForm($idUser);
    }

    /**
     * @return AclFacade
     */
    public function createAclFacade()
    {
        return $this->getProvidedDependency(UserDependencyProvider::FACADE_ACL);
    }

}
