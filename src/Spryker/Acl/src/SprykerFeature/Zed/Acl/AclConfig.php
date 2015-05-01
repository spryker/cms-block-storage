<?php

namespace SprykerFeature\Zed\Acl;

use Generated\Zed\Ide\AutoCompletion;
use SprykerEngine\Zed\Kernel\AbstractBundleConfig;
use SprykerFeature\Shared\Acl\AclConfig as AclSharedConfig;
use SprykerFeature\Shared\Library\Config;

class AclConfig extends AbstractBundleConfig
{

    const VALIDATOR_WILDCARD = '*';

    const ACL_SESSION_KEY = 'acl';
    const ACL_CREDENTIALS_KEY = 'credentials';
    const ACL_DEFAULT_KEY = 'default';
    const ACL_DEFAULT_RULES_KEY = 'rules';

    /**
     * @var AutoCompletion
     */
    protected $locator;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @return array
     */
    public function getRules()
    {
        $default = Config::get(AclSharedConfig::ACL_DEFAULT_RULES);

        return array_merge($default, $this->rules);
    }

    /**
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @param string $type
     */
    public function setRules($bundle, $controller, $action, $type)
    {
        $this->rules[] = [
            'bundle' => $bundle,
            'controller' => $controller,
            'action' => $action,
            'type' => $type
        ];
    }

    /**
     * @return array
     */
    public function getCredentials()
    {
        return Config::get(AclSharedConfig::ACL_DEFAULT_CREDENTIALS);
    }

    /**
     * @return string
     */
    public function getAccessDeniedUri()
    {
        return '/acl/denied';
    }

    /**
     * @return array
     */
    public function getInstallerRules()
    {
        return [
            [
                'bundle' => self::VALIDATOR_WILDCARD,
                'controller' => self::VALIDATOR_WILDCARD,
                'action' => self::VALIDATOR_WILDCARD,
                'type' => 'allow',
                'role' => 'root role'
                //this is related to the installer_data only and will not interact with existing data if any
            ]
        ];
    }

    /**
     * @return array
     */
    public function getInstallerRoles()
    {
        return [
            [
                'name' => 'root role',
                'group' => 'root group'
                //this is related to the installer_data only and will not interact with existing data if any
            ]
        ];
    }

    /**
     * @return array
     */
    public function getInstallerGroups()
    {
        return [
            [
                'name' => 'root group',
            ]
        ];
    }

    public function getInstallerUsers()
    {
        return [
            'admin@spryker.com' => [
                'group' => 'root group'
            ]
            //this is related to existent username and will be searched into the database
        ];
    }
}
