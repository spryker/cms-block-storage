<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Library\GraphViz;

use Spryker\Zed\Library\GraphViz\Adapter\AdapterInterface;

class GraphViz implements GraphVizInterface
{

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @param AdapterInterface $adapter
     * @param string $name
     * @param array $attributes
     * @param bool $directed
     * @param bool $strict
     */
    public function __construct(AdapterInterface $adapter, $name, array $attributes = [], $directed = true, $strict = true)
    {
        $this->adapter = $adapter;
        $this->adapter->create($name, $attributes, $directed, $strict);
    }

    /**
     * @param string $name
     * @param array $attributes
     * @param string $group
     *
     * @return self
     */
    public function addNode($name, $attributes = [], $group = 'default')
    {
        $this->adapter->addNode($name, $attributes, $group);

        return $this;
    }

    /**
     * @param string $fromNode
     * @param string $toNode
     * @param array $attributes
     *
     * @return $this
     */
    public function addEdge($fromNode, $toNode, $attributes = [])
    {
        $this->adapter->addEdge($fromNode, $toNode, $attributes);

        return $this;
    }

    /**
     * @param string $name
     * @param array $attributes
     *
     * @return self
     */
    public function addCluster($name, $attributes = [])
    {
        $this->adapter->addCluster($name, $attributes);

        return $this;
    }

    /**
     * @param string $type
     * @param null $fileName
     *
     * @return string
     */
    public function render($type, $fileName = null)
    {
        return $this->adapter->render($type, $fileName);
    }

}
