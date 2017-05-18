<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Composer;

use RuntimeException;
use Spryker\Zed\Development\Business\Composer\Updater\UpdaterInterface;
use Symfony\Component\Finder\SplFileInfo;
use Zend\Filter\Word\CamelCaseToDash;

class ComposerJsonUpdater implements ComposerJsonUpdaterInterface
{

    const REPLACE_4_WITH_2_SPACES = '/^(  +?)\\1(?=[^ ])/m';
    const KEY_REQUIRE = 'require';
    const KEY_REQUIRE_DEV = 'require-dev';

    /**
     * @var \Spryker\Zed\Development\Business\Composer\ComposerJsonFinderInterface
     */
    protected $finder;

    /**
     * @var \Spryker\Zed\Development\Business\Composer\Updater\UpdaterInterface
     */
    protected $updater;

    /**
     * @param \Spryker\Zed\Development\Business\Composer\ComposerJsonFinderInterface $finder
     * @param \Spryker\Zed\Development\Business\Composer\Updater\UpdaterInterface $updater
     */
    public function __construct(ComposerJsonFinderInterface $finder, UpdaterInterface $updater)
    {
        $this->finder = $finder;
        $this->updater = $updater;
    }

    /**
     * @param array $bundles
     *
     * @return array
     */
    public function update(array $bundles)
    {
        $composerJsonFiles = $this->finder->find();

        $processed = [];
        foreach ($composerJsonFiles as $composerJsonFile) {
            if ($this->shouldSkip($composerJsonFile, $bundles)) {
                continue;
            }

            $this->updateComposerJsonFile($composerJsonFile);

            $processed[] = $composerJsonFile->getRelativePath();
        }

        return $processed;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $composerJsonFile
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function updateComposerJsonFile(SplFileInfo $composerJsonFile)
    {
        exec('./composer.phar validate ' . $composerJsonFile->getPathname(), $output, $return);
        if ($return !== 0) {
            throw new RuntimeException('Invalid composer file ' . $composerJsonFile->getPathname() . ': ' . print_r($output, true));
        }

        $composerJson = json_decode($composerJsonFile->getContents(), true);

        $this->assertCorrectName($composerJson['name'], $composerJsonFile->getRelativePath());

        $composerJson = $this->updater->update($composerJson, $composerJsonFile);

        $composerJson = $this->clean($composerJson);

        $composerJson = $this->order($composerJson);

        $composerJson = json_encode($composerJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $composerJson = preg_replace(static::REPLACE_4_WITH_2_SPACES, '$1', $composerJson) . PHP_EOL;

        file_put_contents($composerJsonFile->getPathname(), $composerJson);
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $composerJsonFile
     * @param array $bundles
     *
     * @return bool
     */
    protected function shouldSkip(SplFileInfo $composerJsonFile, array $bundles)
    {
        if (!$bundles) {
            return false;
        }

        $folder = $composerJsonFile->getRelativePath();
        return !in_array($folder, $bundles);
    }

    /**
     * @param array $composerJson
     *
     * @return array
     */
    protected function clean(array $composerJson)
    {
        if  (!empty($composerJson[static::KEY_REQUIRE])) {
            ksort($composerJson[static::KEY_REQUIRE]);
        } elseif (isset($composerJson[static::KEY_REQUIRE])) {
            unset($composerJson[static::KEY_REQUIRE]);
        }

        if  (!empty($composerJson[static::KEY_REQUIRE_DEV])) {
            ksort($composerJson[static::KEY_REQUIRE_DEV]);
        } elseif (isset($composerJson[static::KEY_REQUIRE_DEV])) {
            unset($composerJson[static::KEY_REQUIRE_DEV]);
        }

        $composerJson['config']['sort-packages'] = true;

        return $composerJson;
    }

    /**
     * @param array $composerJson
     *
     * @return array
     */
    protected function order(array $composerJson)
    {
        $map = [
            'name',
            'type',
            'description',
            'homepage',
            'license',
            'require',
            'require-dev',
            'suggest',
            'autoload',
            'autoload-dev',
            'minimum-stability',
            'prefer-stable',
            'scripts',
            'repositories',
            'extra',
            'config',
        ];

        $callable = function($a, $b) use ($map) {
            $keyA = in_array($a, $map) ? array_search($a, $map) : 999;
            $keyB = in_array($b, $map) ? array_search($b, $map) : 999;

            if ($keyA === $keyB) {
                return 0;
            }
            return $keyA > $keyB;
        };

        uksort($composerJson, $callable);

        return $composerJson;
    }

    /**
     * @param string $vendorName
     * @param string $bundleName
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function assertCorrectName($vendorName, $bundleName)
    {
        $filter = new CamelCaseToDash();
        $bundleName = strtolower($filter->filter($bundleName));

        $expected = 'spryker/' . $bundleName;
        if ($vendorName !== $expected) {
            throw new RuntimeException(sprintf('Invalid composer name, expected %s, got %s', $expected, $vendorName));
        }
    }

}
