<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\Spryker\Shared\Graph;

use Spryker\Shared\Graph\Adapter\PhpDocumentorGraphAdapter;
use Spryker\Shared\Graph\PhpDocumentorGraph;

/**
 * @group Spryker
 * @group Tool
 * @group GraphPhpDocumentor
 */
class PhpDocumentorGraphAdapterTest extends \PHPUnit_Framework_TestCase
{

    const GRAPH_NAME = 'graph name';
    const NODE_A = 'node A';
    const NODE_B = 'node B';
    const GROUP_NAME = 'group name';
    const CLUSTER_NAME = 'cluster name';
    const ATTRIBUTES = ['attribute' => 'value', 'html attribute' => '<h1>Html Value</h1>'];

    /**
     * @return void
     */
    public function testCreate()
    {
        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $this->getAdapter()->create(self::GRAPH_NAME));
    }

    /**
     * @return void
     */
    public function testCreateWithAttributes()
    {
        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $this->getAdapter()->create(self::GRAPH_NAME, self::ATTRIBUTES));
    }

    /**
     * @return void
     */
    public function testCreateUnDirectedGraph()
    {
        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $this->getAdapter()->create(self::GRAPH_NAME, [], false));
    }

    /**
     * @return void
     */
    public function testCreateTolerantGraph()
    {
        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $this->getAdapter()->create(self::GRAPH_NAME, [], true, false));
    }

    /**
     * @return void
     */
    public function testAddNode()
    {
        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $this->getGraph()->addNode(self::NODE_A));
    }

    /**
     * @return void
     */
    public function testAddNodeWithAttributes()
    {
        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $this->getGraph()->addNode(self::NODE_A, self::ATTRIBUTES));
    }

    /**
     * @return void
     */
    public function testAddNodeWithGroup()
    {
        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $this->getGraph()->addNode(self::NODE_A, [], self::GROUP_NAME));
    }

    /**
     * @return void
     */
    public function testAddEdge()
    {
        $adapter = $this->getGraph();
        $adapter->addNode(self::NODE_A);
        $adapter->addNode(self::NODE_B);

        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $adapter->addEdge(self::NODE_A, self::NODE_B));
    }

    /**
     * @return void
     */
    public function testAddEdgeWithAttributes()
    {
        $adapter = $this->getGraph();
        $adapter->addNode(self::NODE_A);
        $adapter->addNode(self::NODE_B);

        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $adapter->addEdge(self::NODE_A, self::NODE_B, self::ATTRIBUTES));
    }

    /**
     * @return void
     */
    public function testAddCluster()
    {
        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $this->getGraph()->addCluster(self::CLUSTER_NAME));
    }

    /**
     * @return void
     */
    public function testAddClusterWithAttributes()
    {
        $this->assertInstanceOf(PhpDocumentorGraphAdapter::class, $this->getGraph()->addCluster(self::CLUSTER_NAME, self::ATTRIBUTES));
    }

    /**
     * @return void
     */
    public function testRender()
    {
        $phpDocumentorMock = $this->getMock(PhpDocumentorGraph::class, ['export']);
        $adapter = new PhpDocumentorGraphAdapter($phpDocumentorMock);
        $adapter->create(self::GRAPH_NAME);

        $this->assertInternalType('string', $adapter->render('svg'));
    }

    /**
     * @return void
     */
    public function testRenderWithFileName()
    {
        $phpDocumentorMock = $this->getMock(PhpDocumentorGraph::class, ['export']);
        $adapter = new PhpDocumentorGraphAdapter($phpDocumentorMock);
        $adapter->create(self::GRAPH_NAME);

        $this->assertInternalType('string', $adapter->render('svg', sys_get_temp_dir() . '/filename'));
    }

    /**
     * @return \Spryker\Shared\Graph\Adapter\PhpDocumentorGraphAdapter
     */
    private function getAdapter()
    {
        $adapter = new PhpDocumentorGraphAdapter();

        return $adapter;
    }

    /**
     * @return \Spryker\Shared\Graph\Adapter\PhpDocumentorGraphAdapter
     */
    private function getGraph()
    {
        return $this->getAdapter()->create(self::GRAPH_NAME);
    }

}
