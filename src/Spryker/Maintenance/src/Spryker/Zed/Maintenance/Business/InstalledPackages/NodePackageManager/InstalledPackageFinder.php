<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Maintenance\Business\InstalledPackages\NodePackageManager;

use Generated\Shared\Transfer\InstalledPackagesTransfer;
use Generated\Shared\Transfer\InstalledPackageTransfer;
use Spryker\Zed\Maintenance\Business\InstalledPackages\InstalledPackageFinderInterface;
use Symfony\Component\Process\Process;

class InstalledPackageFinder implements InstalledPackageFinderInterface
{

    /**
     * @var InstalledPackagesTransfer
     */
    private $collection;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var string
     */
    private $path;

    /**
     * @param \Generated\Shared\Transfer\InstalledPackagesTransfer $collection
     * @param \Symfony\Component\Process\Process $process
     * @param string $path
     */
    public function __construct(InstalledPackagesTransfer $collection, Process $process, $path)
    {
        $this->collection = $collection;
        $this->process = $process;
        $this->path = $path;
    }

    /**
     * @return \Generated\Shared\Transfer\InstalledPackagesTransfer
     */
    public function findInstalledPackages()
    {
        $installedPackages = $this->getPackageList();
        $this->addInstalledPackages($installedPackages);

        return $this->collection;
    }

    /**
     * @throws \RuntimeException
     *
     * @return array
     */
    private function getPackageList()
    {
        $this->process->setWorkingDirectory($this->path);
        $this->process->run();

        if (!$this->process->isSuccessful()) {
            throw new \RuntimeException($this->process->getErrorOutput());
        }

        return json_decode($this->process->getOutput(), true);
    }

    /**
     * @param array $packages
     *
     * @return void
     */
    private function addInstalledPackages(array $packages)
    {
        foreach ($packages['dependencies'] as $package) {
            if (!array_key_exists('name', $package)) {
                continue;
            }

            $installedPackage = new InstalledPackageTransfer();
            $installedPackage->setName($package['name']);
            $installedPackage->setVersion($package['version']);

            $licenses = $this->getLicenses($package);
            $installedPackage->setLicense($licenses);

            $url = $this->getUrl($package);
            $installedPackage->setUrl($url);

            $installedPackage->setType('NPM');

            $this->collection->addPackage($installedPackage);

            if (array_key_exists('dependencies', $package) && count($package['dependencies']) > 0) {
                $this->addInstalledPackages($package);
            }
        }
    }

    /**
     * @param array $package
     *
     * @return array
     */
    private function getLicenses(array $package)
    {
        $licenses = [];
        if (array_key_exists('licenses', $package)) {
            $licenses = $this->parseLicenses($package);
        } else {
            $licenses[] = 'n/a';
        }

        return $licenses;
    }

    /**
     * @param array $package
     *
     * @return array
     */
    private function parseLicenses(array $package)
    {
        $licenses = [];
        if (is_array($package['licenses']) && count($package['licenses']) > 0) {
            $licenses = $this->parseLicensesFromArray($package['licenses']);
        } else {
            $licenses[] = $package['licenses'];
        }

        return $licenses;
    }

    /**
     * @param array $licenses
     *
     * @return array
     */
    private function parseLicensesFromArray(array $licenses)
    {
        $parsedLicenses = [];
        if (!isset($licenses[0])) {
            $licenses = [$licenses];
        }
        foreach ($licenses as $key => $license) {
            if (!isset($license['type'])) {  //@todo add suport for different licence formats
                continue;
            }
            $parsedLicenses[] = $license['type'];
        }

        return $parsedLicenses;
    }

    /**
     * @param array $package
     *
     * @return string
     */
    private function getUrl(array $package)
    {
        if (array_key_exists('homepage', $package)) {
            $url = $package['homepage'];
        } else {
            $url = 'n/a';
        }

        return $url;
    }

}
