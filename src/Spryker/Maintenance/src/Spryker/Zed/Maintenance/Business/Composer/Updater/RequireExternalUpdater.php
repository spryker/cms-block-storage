<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Maintenance\Business\Composer\Updater;

use Spryker\Zed\Maintenance\Business\DependencyTree\DependencyFilter\TreeFilterInterface;
use Spryker\Zed\Maintenance\Business\DependencyTree\DependencyTree;
use Spryker\Zed\Maintenance\Business\DependencyTree\DependencyTreeReader\DependencyTreeReaderInterface;
use Zend\Filter\Word\CamelCaseToDash;
use Zend\Filter\Word\DashToCamelCase;

class RequireExternalUpdater implements UpdaterInterface
{

    const KEY_REQUIRE = 'require';
    const RELEASE_OPERATOR = '^';
    const KEY_NAME = 'name';

    /**
     * @var array
     */
    private $externalDependencyTree;

    /**
     * @var array
     */
    private $externalToInternalMap;

    /**
     * @var array
     */
    private $ignorableDependencies;

    /**
     * @param array $externalDependencyTree
     * @param array $externalToInternalMap
     * @param array $ignorableDependencies
     */
    public function __construct(array $externalDependencyTree, array $externalToInternalMap, array $ignorableDependencies)
    {
        $this->externalDependencyTree = $externalDependencyTree;
        $this->externalToInternalMap = $externalToInternalMap;
        $this->ignorableDependencies = $ignorableDependencies;
    }


    /**
     * @param array $composerJson
     *
     * @return array
     */
    public function update(array $composerJson)
    {
        $bundleName = $this->getBundleName($composerJson);

        $dependentBundles = $this->getExternalBundles($bundleName);

        foreach ($dependentBundles as $dependentBundle) {
            if (empty($dependentBundle) || $dependentBundle === $composerJson[self::KEY_NAME]) {
                continue;
            }
            $filter = new CamelCaseToDash();
            $dependentBundle = strtolower($filter->filter($dependentBundle));

            $composerJson[self::KEY_REQUIRE][$dependentBundle] = self::RELEASE_OPERATOR . '1.0.0';
        }

        return $composerJson;
    }

    /**
     * @param array $composerJsonData
     *
     * @return string
     */
    private function getBundleName(array $composerJsonData)
    {
        $nameParts = explode('/', $composerJsonData[self::KEY_NAME]);
        $bundleName = array_pop($nameParts);
        $filter = new DashToCamelCase();

        return $filter->filter($bundleName);
    }

    /**
     * @param string $bundleName
     *
     * @return array
     */
    private function getExternalBundles($bundleName)
    {
        $dependentBundles = [];
        foreach ($this->externalDependencyTree as $dependency) {
            if ($dependency[DependencyTree::META_BUNDLE] === $bundleName
                && !in_array($dependency[DependencyTree::META_COMPOSER_NAME], $this->ignorableDependencies)
            ) {
                $dependentBundles[] = $this->mapExternalToInternal($dependency[DependencyTree::META_COMPOSER_NAME]);
            }
        }
        $dependentBundles = array_unique($dependentBundles);
        sort($dependentBundles);

        return $dependentBundles;
    }

    /**
     * @param string $composerName
     *
     * @return string
     */
    private function mapExternalToInternal($composerName)
    {
        foreach ($this->externalToInternalMap as $external => $internal) {
            if ($external[0] === '/') {
                if (preg_match($external, $composerName)) {
                    return $internal;
                }
            } elseif ($external === $composerName) {
                return $internal;
            }
        }
    }

}
