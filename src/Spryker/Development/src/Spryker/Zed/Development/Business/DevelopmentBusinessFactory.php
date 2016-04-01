<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business;

use Spryker\Zed\Development\Business\CodeBuilder\Bridge\BridgeBuilder;
use Spryker\Zed\Development\Business\CodeStyleSniffer\CodeStyleSniffer;
use Spryker\Zed\Development\Business\CodeTest\CodeTester;
use Spryker\Zed\Development\Business\Composer\ComposerJsonFinder;
use Spryker\Zed\Development\Business\Composer\ComposerJsonUpdater;
use Spryker\Zed\Development\Business\Composer\Updater\BranchAliasUpdater;
use Spryker\Zed\Development\Business\Composer\Updater\ComposerUpdaterComposite;
use Spryker\Zed\Development\Business\Composer\Updater\DescriptionUpdater;
use Spryker\Zed\Development\Business\Composer\Updater\LicenseUpdater;
use Spryker\Zed\Development\Business\Composer\Updater\RequireExternalUpdater;
use Spryker\Zed\Development\Business\Composer\Updater\RequireUpdater;
use Spryker\Zed\Development\Business\Composer\Updater\StabilityUpdater;
use Spryker\Zed\Development\Business\DependencyTree\AdjacencyMatrixBuilder;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\BundleToViewFilter;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\ClassNameFilter;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\ConstantsToForeignConstantsFilter;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\DependencyFilter;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\EngineBundleFilter;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\ExternalDependencyFilter;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\ForeignEngineBundleFilter;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\InternalDependencyFilter;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\InvalidForeignBundleFilter;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\TreeFilter;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFinder\ExternalDependency;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFinder\LocatorClient;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFinder\LocatorFacade;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFinder\LocatorQueryContainer;
use Spryker\Zed\Development\Business\DependencyTree\DependencyFinder\UseStatement;
use Spryker\Zed\Development\Business\DependencyTree\DependencyGraphBuilder;
use Spryker\Zed\Development\Business\DependencyTree\DependencyGraph\DetailedGraphBuilder;
use Spryker\Zed\Development\Business\DependencyTree\DependencyGraph\ExternalGraphBuilder;
use Spryker\Zed\Development\Business\DependencyTree\DependencyGraph\SimpleGraphBuilder;
use Spryker\Zed\Development\Business\DependencyTree\DependencyHydrator\DependencyHydrator;
use Spryker\Zed\Development\Business\DependencyTree\DependencyHydrator\PackageNameHydrator;
use Spryker\Zed\Development\Business\DependencyTree\DependencyHydrator\PackageVersionHydrator;
use Spryker\Zed\Development\Business\DependencyTree\DependencyTree;
use Spryker\Zed\Development\Business\DependencyTree\DependencyTreeBuilder;
use Spryker\Zed\Development\Business\DependencyTree\DependencyTreeReader\JsonDependencyTreeReader;
use Spryker\Zed\Development\Business\DependencyTree\DependencyTreeWriter\JsonDependencyTreeWriter;
use Spryker\Zed\Development\Business\DependencyTree\FileInfoExtractor;
use Spryker\Zed\Development\Business\DependencyTree\Finder;
use Spryker\Zed\Development\Business\DependencyTree\ViolationChecker\DependencyViolationChecker;
use Spryker\Zed\Development\Business\DependencyTree\ViolationFinder\BundleUsesConnector;
use Spryker\Zed\Development\Business\DependencyTree\ViolationFinder\UseForeignConstants;
use Spryker\Zed\Development\Business\DependencyTree\ViolationFinder\UseForeignException;
use Spryker\Zed\Development\Business\DependencyTree\ViolationFinder\ViolationFinder;
use Spryker\Zed\Development\Business\Dependency\BundleParser;
use Spryker\Zed\Development\Business\Dependency\Manager;
use Spryker\Zed\Development\Business\PhpMd\PhpMdRunner;
use Spryker\Zed\Development\DevelopmentDependencyProvider;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Symfony\Component\Finder\Finder as SfFinder;

/**
 * @method \Spryker\Zed\Development\DevelopmentConfig getConfig()
 */
class DevelopmentBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @return \Spryker\Zed\Development\Business\CodeStyleSniffer\CodeStyleSniffer
     */
    public function createCodeStyleSniffer()
    {
        return new CodeStyleSniffer(
            $this->getConfig()->getPathToRoot(),
            $this->getConfig()->getBundleDirectory(),
            $this->getConfig()->getCodingStandard()
        );
    }

    /**
     * @return \Spryker\Zed\Development\Business\CodeTest\CodeTester
     */
    public function createCodeTester()
    {
        return new CodeTester(
            $this->getConfig()->getPathToRoot(),
            $this->getConfig()->getBundleDirectory()
        );
    }

    /**
     * @return \Spryker\Zed\Development\Business\PhpMd\PhpMdRunner
     */
    public function createPhpMdRunner()
    {
        return new PhpMdRunner(
            $this->getConfig()->getPathToRoot(),
            $this->getConfig()->getBundleDirectory(),
            $this->getConfig()->getArchitectureStandard()
        );
    }

    /**
     * @return \Spryker\Zed\Development\Business\CodeBuilder\Bridge\BridgeBuilder
     */
    public function createBridgeBuilder()
    {
        return new BridgeBuilder(
            $this->getConfig()->getBundleDirectory()
        );
    }

    /**
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     *
     * @return \Spryker\Zed\Graph\Communication\Plugin\GraphPlugin
     */
    protected function getGraph()
    {
        return $this->getProvidedDependency(DevelopmentDependencyProvider::PLUGIN_GRAPH);
    }

    /**
     * @return \Spryker\Zed\Development\Business\Dependency\BundleParser
     */
    public function createDependencyBundleParser()
    {
        $config = $this->getConfig();

        return new BundleParser($config);
    }

    /**
     * @return \Spryker\Zed\Development\Business\Dependency\Manager
     */
    public function createDependencyManager()
    {
        $bundleParser = $this->createDependencyBundleParser();

        return new Manager($bundleParser, $this->getConfig()->getBundleDirectory());
    }

    /**
     * @param string $application
     * @param string $bundle
     * @param string $layer
     *
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyTreeBuilder
     */
    public function createDependencyTreeBuilder($application, $bundle, $layer)
    {
        $finder = $this->createDependencyTreeFinder($application, $bundle, $layer);
        $report = $this->createDependencyTree();
        $writer = $this->createDependencyTreeWriter();

        $dependencyTreeBuilder = new DependencyTreeBuilder($finder, $report, $writer);
        $dependencyTreeBuilder->addDependencyChecker($this->createDependencyTreeChecker());

        return $dependencyTreeBuilder;
    }

    /**
     * @param string $application
     * @param string $bundle
     * @param string $layer
     *
     * @return \Spryker\Zed\Development\Business\DependencyTree\Finder
     */
    protected function createDependencyTreeFinder($application, $bundle, $layer)
    {
        $finder = new Finder(
            $this->getConfig()->getBundleDirectory(),
            $application,
            $bundle,
            $layer
        );

        return $finder;
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyTree
     */
    protected function createDependencyTree()
    {
        $fileInfoExtractor = $this->createDependencyTreeFileInfoExtractor();
        $engineBundleList = $this->getEngineBundleList();

        return new DependencyTree($fileInfoExtractor, $engineBundleList);
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\FileInfoExtractor
     */
    protected function createDependencyTreeFileInfoExtractor()
    {
        return new FileInfoExtractor();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyTreeWriter\JsonDependencyTreeWriter
     */
    protected function createDependencyTreeWriter()
    {
        return new JsonDependencyTreeWriter($this->getConfig()->getPathToJsonDependencyTree());
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyTreeReader\JsonDependencyTreeReader
     */
    public function createDependencyTreeReader()
    {
        return new JsonDependencyTreeReader($this->getConfig()->getPathToJsonDependencyTree());
    }

    /**
     * @return array
     */
    protected function createDependencyTreeChecker()
    {
        return [
            $this->createUseStatementChecker(),
            $this->createLocatorFacadeChecker(),
            $this->createLocatorQueryContainerChecker(),
            $this->createLocatorClientChecker(),
            $this->createExternalDependencyChecker(),
        ];
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFinder\UseStatement
     */
    protected function createUseStatementChecker()
    {
        return new UseStatement();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFinder\LocatorFacade
     */
    protected function createLocatorFacadeChecker()
    {
        return new LocatorFacade();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFinder\LocatorQueryContainer
     */
    protected function createLocatorQueryContainerChecker()
    {
        return new LocatorQueryContainer();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFinder\LocatorClient
     */
    protected function createLocatorClientChecker()
    {
        return new LocatorClient();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFinder\ExternalDependency
     */
    protected function createExternalDependencyChecker()
    {
        return new ExternalDependency();
    }

    /**
     * @param string|bool $bundleToView
     *
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyGraphBuilder
     */
    public function createDetailedDependencyGraphBuilder($bundleToView)
    {
        $dependencyTreeFilter = $this->createDetailedDependencyTreeFilter($bundleToView);
        $dependencyTreeReader = $this->createDependencyTreeReader();

        $dependencyGraphBuilder = new DependencyGraphBuilder(
            $this->createDetailedGraphBuilder(),
            $dependencyTreeFilter->filter($dependencyTreeReader->read())
        );

        return $dependencyGraphBuilder;
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyGraph\DetailedGraphBuilder
     */
    protected function createDetailedGraphBuilder()
    {
        return new DetailedGraphBuilder($this->getGraph()->init('Dependency Tree'));
    }

    /**
     * @param string|bool $bundleToView
     *
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\TreeFilter
     */
    protected function createDetailedDependencyTreeFilter($bundleToView)
    {
        $treeFilter = $this->createDependencyTreeFilter();
        $treeFilter
            ->addFilter($this->createDependencyTreeForeignEngineBundleFilter())
            ->addFilter($this->createDependencyTreeClassNameFilter('/\\Dependency\\\(.*?)Interface/'));

        if (is_string($bundleToView)) {
            $treeFilter->addFilter($this->createDependencyTreeBundleToViewFilter($bundleToView));
        }

        return $treeFilter;
    }

    /**
     * @param bool $showEngineBundle
     * @param string|bool $bundleToView
     *
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyGraphBuilder
     */
    public function createSimpleDependencyGraphBuilder($showEngineBundle, $bundleToView)
    {
        $dependencyTreeFilter = $this->createSimpleGraphDependencyTreeFilter($showEngineBundle, $bundleToView);
        $dependencyTreeReader = $this->createDependencyTreeReader();

        $dependencyGraphBuilder = new DependencyGraphBuilder(
            $this->createSimpleGraphBuilder(),
            $dependencyTreeFilter->filter($dependencyTreeReader->read())
        );

        return $dependencyGraphBuilder;
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyGraph\DetailedGraphBuilder
     */
    protected function createSimpleGraphBuilder()
    {
        return new SimpleGraphBuilder($this->getGraph()->init('Dependency Tree'));
    }

    /**
     * @param bool $showEngineBundle
     * @param string|bool $bundleToView
     *
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\TreeFilter
     */
    protected function createSimpleGraphDependencyTreeFilter($showEngineBundle, $bundleToView)
    {
        $treeFilter = $this->createDependencyTreeFilter();
        $treeFilter
            ->addFilter($this->createDependencyTreeInvalidForeignBundleFilter())
            ->addFilter($this->createDependencyTreeExternalDependencyFilter())
            ->addFilter($this->createDependencyTreeClassNameFilter('/\\Dependency\\\(.*?)Interface/'));

        if (!$showEngineBundle) {
            $treeFilter->addFilter($this->createDependencyTreeForeignEngineBundleFilter());
            $treeFilter->addFilter($this->createDependencyTreeEngineBundleFilter());
        }

        if (is_string($bundleToView)) {
            $treeFilter->addFilter($this->createDependencyTreeBundleToViewFilter($bundleToView));
        }

        return $treeFilter;
    }

    /**
     * @param string|null $bundleToView
     *
     * @return array
     */
    public function createExternalDependencyTree($bundleToView = null)
    {
        $treeFilter = $this->createDependencyTreeFilter();
        $treeFilter
            ->addFilter($this->createDependencyTreeInternalDependencyFilter());

        if (is_string($bundleToView)) {
            $treeFilter->addFilter($this->createDependencyTreeBundleToViewFilter($bundleToView));
        }

        $composerLock = json_decode(file_get_contents(APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR . 'composer.lock'), true);
        $packageVersionHydrator = new PackageVersionHydrator(array_merge($composerLock['packages'], $composerLock['packages-dev']));

        $treeHydrator = new DependencyHydrator();
        $treeHydrator->addHydrator(new PackageNameHydrator());
        $treeHydrator->addHydrator($packageVersionHydrator);

        $dependencyTreeReader = $this->createDependencyTreeReader();

        $dependencyTree = $treeFilter->filter($dependencyTreeReader->read());

        return $treeHydrator->hydrate($dependencyTree);
    }

    /**
     * @param string $bundleToView
     *
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyGraphBuilder
     */
    public function createExternalDependencyGraphBuilder($bundleToView)
    {
        $dependencyGraphBuilder = new DependencyGraphBuilder(
            $this->createExternalGraphBuilder(),
            $this->createExternalDependencyTree($bundleToView)
        );

        return $dependencyGraphBuilder;
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyGraph\DetailedGraphBuilder
     */
    protected function createExternalGraphBuilder()
    {
        return new ExternalGraphBuilder($this->getGraph()->init('Dependency Tree'));
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\AdjacencyMatrixBuilder
     */
    public function createAdjacencyMatrixBuilder()
    {
        $adjacencyMatrixBuilder = new AdjacencyMatrixBuilder(
            $this->createDependencyManager()->collectAllBundles(),
            $this->createDependencyTreeReader(),
            $this->createAdjacencyMatrixDependencyTreeFilter()
        );

        return $adjacencyMatrixBuilder;
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\TreeFilter
     */
    protected function createAdjacencyMatrixDependencyTreeFilter()
    {
        $treeFilter = $this->createDependencyTreeFilter();
        $treeFilter
            ->addFilter($this->createDependencyTreeInvalidForeignBundleFilter());

        return $treeFilter;
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\ViolationChecker\DependencyViolationChecker
     */
    public function createDependencyViolationChecker()
    {
        return new DependencyViolationChecker(
            $this->createDependencyTreeReader(),
            $this->createViolationFinder(),
            $this->createDependencyViolationFilter()
        );
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\ViolationFinder\ViolationFinder
     */
    protected function createViolationFinder()
    {
        $violationFinder = new ViolationFinder();
        $violationFinder
            ->addViolationFinder($this->createViolationFinderUseForeignConstants())
            ->addViolationFinder($this->createViolationFinderUseForeignException())
            ->addViolationFinder($this->createViolationFinderBundleUsesConnector());

        return $violationFinder;
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\DependencyFilter
     */
    protected function createDependencyViolationFilter()
    {
        $dependencyFilter = new DependencyFilter();
        $dependencyFilter
            ->addFilter($this->createDependencyTreeConstantsToForeignConstantsFilter())
            ->addFilter($this->createDependencyTreeForeignEngineBundleFilter());

        return $dependencyFilter;
    }

    /**
     * @param string $pattern
     *
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\ClassNameFilter
     */
    protected function createDependencyTreeClassNameFilter($pattern)
    {
        return new ClassNameFilter($pattern);
    }

    /**
     * @param string $bundleToView
     *
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\BundleToViewFilter
     */
    protected function createDependencyTreeBundleToViewFilter($bundleToView)
    {
        return new BundleToViewFilter($bundleToView);
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\ViolationFinder\UseForeignConstants
     */
    protected function createViolationFinderUseForeignConstants()
    {
        return new UseForeignConstants();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\ViolationFinder\UseForeignException
     */
    protected function createViolationFinderUseForeignException()
    {
        return new UseForeignException();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\ViolationFinder\BundleUsesConnector
     */
    protected function createViolationFinderBundleUsesConnector()
    {
        return new BundleUsesConnector();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\ConstantsToForeignConstantsFilter
     */
    protected function createDependencyTreeConstantsToForeignConstantsFilter()
    {
        return new ConstantsToForeignConstantsFilter();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\ForeignEngineBundleFilter
     */
    protected function createDependencyTreeForeignEngineBundleFilter()
    {
        return new ForeignEngineBundleFilter(
            $this->getConfig()->getPathToBundleConfig()
        );
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\EngineBundleFilter
     */
    protected function createDependencyTreeEngineBundleFilter()
    {
        return new EngineBundleFilter(
            $this->getConfig()->getPathToBundleConfig()
        );
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\InvalidForeignBundleFilter
     */
    protected function createDependencyTreeInvalidForeignBundleFilter()
    {
        return new InvalidForeignBundleFilter(
            $this->createDependencyManager()->collectAllBundles()
        );
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\ExternalDependencyFilter
     */
    protected function createDependencyTreeExternalDependencyFilter()
    {
        return new ExternalDependencyFilter();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\InternalDependencyFilter
     */
    protected function createDependencyTreeInternalDependencyFilter()
    {
        return new InternalDependencyFilter();
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\TreeFilter
     */
    protected function createDependencyTreeFilter()
    {
        return new TreeFilter();
    }

    /**
     * @return array
     */
    public function getEngineBundleList()
    {
        $bundleList = json_decode(file_get_contents($this->getConfig()->getPathToBundleConfig()), true);

        return array_keys($bundleList);
    }

    /**
     * @return \Spryker\Zed\Development\Business\Composer\ComposerJsonUpdater
     */
    public function createComposerJsonUpdater()
    {
        return new ComposerJsonUpdater(
            $this->createComposerJsonFinder(),
            $this->createComposerJsonUpdaterComposite()
        );
    }

    /**
     * @return \Spryker\Zed\Development\Business\Composer\ComposerJsonFinder
     */
    protected function createComposerJsonFinder()
    {
        $composerJsonFinder = new ComposerJsonFinder(
            $this->createFinder(),
            $this->getConfig()->getBundleDirectory()
        );

        return $composerJsonFinder;
    }

    /**
     * @return \Spryker\Zed\Development\Business\Composer\Updater\ComposerUpdaterComposite
     */
    protected function createComposerJsonUpdaterComposite()
    {
        $updaterComposite = new ComposerUpdaterComposite();
        $updaterComposite
            ->addUpdater($this->createComposerJsonDescriptionUpdater())
            ->addUpdater($this->createComposerJsonLicenseUpdater())
            ->addUpdater($this->createComposerJsonRequireUpdater())
            ->addUpdater($this->createComposerJsonRequireExternalUpdater())
            ->addUpdater($this->createComposerJsonStabilityUpdater())
            ->addUpdater($this->createComposerJsonBranchAliasUpdater());

        return $updaterComposite;
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     */
    protected function createFinder()
    {
        return new SfFinder();
    }

    /**
     * @return \Spryker\Zed\Development\Business\Composer\Updater\DescriptionUpdater
     */
    protected function createComposerJsonDescriptionUpdater()
    {
        return new DescriptionUpdater();
    }

    /**
     * @return \Spryker\Zed\Development\Business\Composer\Updater\LicenseUpdater
     */
    protected function createComposerJsonLicenseUpdater()
    {
        return new LicenseUpdater('proprietary');
    }

    /**
     * @return \Spryker\Zed\Development\Business\Composer\Updater\RequireUpdater
     */
    protected function createComposerJsonRequireUpdater()
    {
        return new RequireUpdater(
            $this->createDependencyTreeReader(),
            $this->createComposerJsonRequireUpdaterTreeFilter()
        );
    }

    /**
     * @return \Spryker\Zed\Development\Business\Composer\Updater\RequireUpdater
     */
    protected function createComposerJsonRequireExternalUpdater()
    {
        return new RequireExternalUpdater(
            $this->createExternalDependencyTree(),
            $this->getConfig()->getExternalToInternalMap(),
            $this->getConfig()->getIgnorableDependencies()
        );
    }

    /**
     * @return \Spryker\Zed\Development\Business\Composer\Updater\StabilityUpdater
     */
    protected function createComposerJsonStabilityUpdater()
    {
        return new StabilityUpdater('stable');
    }

    /**
     * @return \Spryker\Zed\Development\Business\Composer\Updater\BranchAliasUpdater
     */
    protected function createComposerJsonBranchAliasUpdater()
    {
        return new BranchAliasUpdater('2.0.x-dev');
    }

    /**
     * @return \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\TreeFilter
     */
    protected function createComposerJsonRequireUpdaterTreeFilter()
    {
        $treeFilter = new TreeFilter();
        $treeFilter
            ->addFilter($this->createDependencyTreeClassNameFilter('/\\Dependency\\\(.*?)Interface/'))
            ->addFilter($this->createDependencyTreeInvalidForeignBundleFilter());

        return $treeFilter;
    }

}
