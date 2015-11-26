<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Maintenance\Business;

use Generated\Shared\Maintenance\InstalledPackagesInterface;
use SprykerEngine\Zed\Kernel\Business\AbstractFacade;

/**
 * @method MaintenanceDependencyContainer getDependencyContainer()
 */
class MaintenanceFacade extends AbstractFacade
{

    /**
     * @return InstalledPackagesInterface
     */
    public function getInstalledPackages()
    {
        return $this->getDependencyContainer()->createPackageCollector()->getInstalledPackages();
    }

    /**
     * @param InstalledPackagesInterface $installedPackages
     */
    public function writeInstalledPackagesToMarkDownFile(InstalledPackagesInterface $installedPackages)
    {
        $this->getDependencyContainer()->createMarkDownWriter($installedPackages)->write();
    }

    /**
     * @param string $bundleName
     *
     * @return array
     */
    public function showOutgoingDependenciesForBundle($bundleName)
    {
        return $this->getDependencyContainer()->createDependencyBundleParser()->parseOutgoingDependencies($bundleName);
    }

    /**
     * @param string $bundleName
     *
     * @return array
     */
    public function showIncomingDependenciesForBundle($bundleName)
    {
        return $this->getDependencyContainer()->createDependencyManager()->parseIncomingDependencies($bundleName);
    }

    public function drawDependencyGraph($bundleName)
    {
        return $this->getDependencyContainer()->createDependencyGraph()->draw($bundleName);
    }

    /**
     * @param string|null $bundle
     * @param bool $clear
     *
     * @return void
     */
    public function fixCodeStyle($bundle = null, $clear = false)
    {
        $this->getDependencyContainer()->createBundleCodeStyleFixer()->fixBundleCodeStyle($bundle, $clear);
    }

    /**
     * @todo move this to propel bundle
     *
     * @return bool
     */
    public function cleanPropelMigration()
    {
        return $this->getDependencyContainer()->createPropelMigrationCleaner()->clean();
    }

    /**
     * @return array
     */
    public function getAllBundles()
    {
        return $this->getDependencyContainer()->createDependencyManager()->collectAllBundles();
    }

}
