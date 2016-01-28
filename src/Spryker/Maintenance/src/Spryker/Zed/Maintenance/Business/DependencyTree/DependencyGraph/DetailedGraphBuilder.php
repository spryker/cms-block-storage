<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Maintenance\Business\DependencyTree\DependencyGraph;

use Spryker\Zed\Maintenance\Business\DependencyTree\DependencyFinder\LocatorClient;
use Spryker\Zed\Maintenance\Business\DependencyTree\DependencyFinder\LocatorFacade;
use Spryker\Zed\Maintenance\Business\DependencyTree\DependencyFinder\LocatorQueryContainer;
use Spryker\Zed\Maintenance\Business\DependencyTree\DependencyFinder\UseStatement;
use Spryker\Zed\Maintenance\Business\DependencyTree\DependencyTree;
use Spryker\Zed\Library\Service\GraphViz;

class DetailedGraphBuilder implements GraphBuilderInterface
{

    /**
     * @var GraphViz
     */
    private $graph;

    /**
     * @var array
     */
    protected $clusterAttributes = [
        'fontname' => 'Verdana',
        'fillcolor' => '#cfcfcf',
        'style' => 'filled',
        'color' => '#ffffff',
        'fontsize' => 12,
        'fontcolor' => 'black',
    ];

    /**
     * @var array
     */
    protected $layerAttributes = [
        'fillcolor' => '#cfcfcf',
        'style' => 'filled',
        'color' => '#999999',
        'fontsize' => 12,
    ];

    /**
     * @var array
     */
    protected $rootFileAttributes = [
        'fillcolor' => '#cfcfcf',
        'shape' => 'diamond',
        'fontsize' => 8,
    ];

    public function __construct()
    {
        $this->graph = new GraphViz(true, [], 'Bundle Dependencies', false, true);
    }

    /**
     * @param array $dependencyTree
     *
     * @return bool
     */
    public function build(array $dependencyTree)
    {
        $this->buildGraph($dependencyTree);

        return $this->graph->image('svg', 'dot');
    }

    /**
     * @param array $dependencyTree
     *
     * @return void
     */
    private function buildGraph(array $dependencyTree)
    {
        foreach ($dependencyTree as $bundle => $dependencies) {
            $this->buildBundleGraph($bundle, $dependencies);
        }
    }

    /**
     * @param string $bundle
     * @param array $foreignBundles
     *
     * @return void
     */
    private function buildBundleGraph($bundle, array $foreignBundles)
    {
        foreach ($foreignBundles as $foreignBundle => $dependencyInformationCollection) {
            $this->buildBundleDependencyGraph($bundle, $foreignBundle, $dependencyInformationCollection);
        }
    }

    /**
     * @param $bundle
     * @param $foreignBundle
     * @param $dependencyInformationCollection
     *
     * @return void
     */
    private function buildBundleDependencyGraph($bundle, $foreignBundle, $dependencyInformationCollection)
    {
        $group = $this->getGroup($bundle, $foreignBundle);
        $rootNodeId = $this->getRootNodeId($bundle);
        $this->graph->addNode($rootNodeId, [], $group);
        foreach ($dependencyInformationCollection as $dependencyInformation) {
            $this->addRootBundleLayer($dependencyInformation, $group);
            $this->addRootFile($dependencyInformation, $group);
            $this->addForeignBundleLayer($dependencyInformation, $group);
        }
    }

    /**
     * @param $bundle
     * @param $foreignBundle
     *
     * @return string
     */
    private function getGroup($bundle, $foreignBundle)
    {
        return implode(':', [$bundle, $foreignBundle]);
    }

    /**
     * @param string $bundle
     *
     * @return string
     */
    private function getRootNodeId($bundle)
    {
        return $bundle;
    }

    /**
     * @param array $dependencyInformation
     * @param string $group
     *
     * @return void
     */
    private function addRootBundleLayer(array $dependencyInformation, $group)
    {
        $rootBundleLayerNodeId = $this->getRootBundleLayerNodeId($dependencyInformation);
        if ($this->graph->hasNode($rootBundleLayerNodeId, $group)) {
            return;
        }
        $label = $dependencyInformation[DependencyTree::META_LAYER];
        $this->layerAttributes['label'] = $label;
        $this->graph->addNode($rootBundleLayerNodeId, $this->layerAttributes, $group);
        $this->graph->addEdge([
            $this->getRootNodeId($dependencyInformation[DependencyTree::META_BUNDLE]) => $rootBundleLayerNodeId, ]
        );
    }

    /**
     * @param array $dependencyInformation
     *
     * @return string
     */
    private function getRootBundleLayerNodeId(array $dependencyInformation)
    {
        return $dependencyInformation[DependencyTree::META_LAYER];
    }

    /**
     * @param array $dependencyInformation
     * @param $group
     *
     * @return void
     */
    private function addRootFile(array $dependencyInformation, $group)
    {
        $rootFileNodeId = $this->getRootFileNodeId($dependencyInformation);
        if ($this->graph->hasNode($rootFileNodeId, $group)) {
            return;
        }
        $this->graph->addNode($rootFileNodeId, $this->rootFileAttributes, $group);
        $this->graph->addEdge([
            $this->getRootBundleLayerNodeId($dependencyInformation) => $rootFileNodeId, ]
        );
    }

    /**
     * @param array $dependencyInformation
     *
     * @return string
     */
    private function getRootFileNodeId(array $dependencyInformation)
    {
        return substr($dependencyInformation[DependencyTree::META_FILE], 0, -4);
    }

    /**
     * @param array $dependencyInformation
     * @param $group
     *
     * @return void
     */
    private function addForeignBundleLayer(array $dependencyInformation, $group)
    {
        $foreignBundleLayerNodeId = $this->getForeignBundleLayerNodeId($dependencyInformation);
        $label = $dependencyInformation[DependencyTree::META_FOREIGN_BUNDLE]  . ' ' . $dependencyInformation[DependencyTree::META_FOREIGN_LAYER];
        $attributes = $this->layerAttributes;
        $attributes['label'] = $label;
        $this->graph->addNode($foreignBundleLayerNodeId, $attributes, $group);

        $this->graph->addEdge(
            [$this->getRootFileNodeId($dependencyInformation) => $foreignBundleLayerNodeId],
            [
                'label' => $this->getForeignUsage($dependencyInformation[DependencyTree::META_FINDER]),
                'fontsize' => 8,
            ],
            $group
        );
    }

    /**
     * @param array $dependencyInformation
     *
     * @return string
     */
    private function getForeignBundleLayerNodeId(array $dependencyInformation)
    {
        return implode(
            ':',
            [
                $dependencyInformation[DependencyTree::META_FOREIGN_BUNDLE],
                $dependencyInformation[DependencyTree::META_FOREIGN_LAYER],
            ]
        );
    }

    /**
     * @param string $finderName
     *
     * @return string
     */
    private function getForeignUsage($finderName)
    {
        $mapped = [
            LocatorClient::class => 'Client',
            LocatorFacade::class => 'Facade',
            LocatorQueryContainer::class => 'QueryContainer',
            UseStatement::class => 'Use',
        ];

        return $mapped[$finderName];
    }

}
