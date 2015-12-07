<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Maintenance\Business\InstalledPackages;

use Generated\Shared\Transfer\InstalledPackagesTransfer;

class MarkDownWriter implements MarkDownWriterInterface
{

    const SEPARATOR = ';';

    /**
     * @var InstalledPackagesTransfer
     */
    private $installedPackages;

    /**
     * @var string
     */
    private $path;

    /**
     * @param InstalledPackagesTransfer $installedPackages
     * @param string $path
     */
    public function __construct(InstalledPackagesTransfer $installedPackages, $path)
    {
        $this->installedPackages = $installedPackages;
        $this->path = $path;
    }

    /**
     * @return void
     */
    public function write()
    {
        $markDownLines = [];
        $header = ['Name', 'Version', 'License', 'Url', 'Type'];
        $markDownLines[] = implode(self::SEPARATOR, $header) . self::SEPARATOR;

        foreach ($this->installedPackages->getPackages() as $package) {
            $markDownLines[] = $package->getName() . self::SEPARATOR
                . $package->getVersion() . self::SEPARATOR
                . implode(', ', (array) $package->getLicense()) . self::SEPARATOR
                . $package->getUrl() . self::SEPARATOR
                . $package->getType() . self::SEPARATOR;
        }

        file_put_contents($this->path, implode("\n", $markDownLines));
    }

}
