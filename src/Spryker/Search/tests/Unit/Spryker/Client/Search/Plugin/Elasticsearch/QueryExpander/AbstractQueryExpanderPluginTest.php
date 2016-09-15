<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Client\Search\Plugin\Elasticsearch\QueryExpander;

use Elastica\Aggregation\Nested;
use Elastica\Aggregation\Stats;
use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Generated\Shared\Search\PageIndexMap;
use Generated\Shared\Transfer\FacetConfigTransfer;
use Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface;
use Spryker\Client\Search\Plugin\Config\FacetConfigBuilder;
use Spryker\Client\Search\Plugin\Config\PaginationConfigBuilder;
use Spryker\Client\Search\Plugin\Config\SearchConfig;
use Spryker\Client\Search\Plugin\Config\SortConfigBuilder;
use Spryker\Client\Search\SearchFactory;
use Unit\Spryker\Client\Search\Plugin\Elasticsearch\Fixtures\BaseQueryPlugin;

/**
 * @group Unit
 * @group Spryker
 * @group Client
 * @group Search
 * @group Plugin
 * @group Elasticsearch
 * @group QueryExpander
 * @group AbstractQueryExpanderPluginTest
 */
abstract class AbstractQueryExpanderPluginTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\QueryInterface
     */
    protected function createBaseQueryPlugin()
    {
        return new BaseQueryPlugin();
    }

    /**
     * @return \Elastica\Query
     */
    protected function createBaseQuery()
    {
        $baseQuery = (new Query())
            ->setQuery(new BoolQuery());

        return $baseQuery;
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface
     */
    protected function createSearchConfigMock()
    {
        $searchConfigMock = $this->getMockBuilder(SearchConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFacetConfigBuilder', 'getSortConfigBuilder', 'getPaginationConfigBuilder'])
            ->getMock();

        $searchConfigMock
            ->method('getFacetConfigBuilder')
            ->willReturn(new FacetConfigBuilder());

        $searchConfigMock
            ->method('getSortConfigBuilder')
            ->willReturn(new SortConfigBuilder());

        $searchConfigMock
            ->method('getPaginationConfigBuilder')
            ->willReturn(new PaginationConfigBuilder());

        return $searchConfigMock;
    }

    /**
     * @param \Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface $searchConfig
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Client\Search\SearchFactory
     */
    protected function createSearchFactoryMockedWithSearchConfig(SearchConfigInterface $searchConfig)
    {
        /** @var \Spryker\Client\Search\SearchFactory|\PHPUnit_Framework_MockObject_MockObject $searchFactoryMock */
        $searchFactoryMock = $this->getMockBuilder(SearchFactory::class)
            ->setMethods(['getSearchConfig'])
            ->getMock();
        $searchFactoryMock
            ->method('getSearchConfig')
            ->willReturn($searchConfig);
        return $searchFactoryMock;
    }

    /**
     * @return \Elastica\Aggregation\AbstractAggregation
     */
    protected function getExpectedStringFacetAggregation()
    {
        return (new Nested(PageIndexMap::STRING_FACET, PageIndexMap::STRING_FACET))
            ->addAggregation((new Terms(PageIndexMap::STRING_FACET . '-name'))
                ->setField(PageIndexMap::STRING_FACET_FACET_NAME)
                ->addAggregation((new Terms(PageIndexMap::STRING_FACET . '-value'))
                    ->setField(PageIndexMap::STRING_FACET_FACET_VALUE)));
    }

    /**
     * @return \Elastica\Aggregation\AbstractAggregation
     */
    protected function getExpectedIntegerFacetAggregation()
    {
        return (new Nested(PageIndexMap::INTEGER_FACET, PageIndexMap::INTEGER_FACET))
            ->addAggregation((new Terms(PageIndexMap::INTEGER_FACET . '-name'))
                ->setField(PageIndexMap::INTEGER_FACET_FACET_NAME)
                ->addAggregation((new Stats(PageIndexMap::INTEGER_FACET . '-stats'))
                    ->setField(PageIndexMap::INTEGER_FACET_FACET_VALUE)));
    }

    /**
     * @return \Elastica\Aggregation\AbstractAggregation
     */
    protected function getExpectedCategoryFacetAggregation()
    {
        return (new Terms(PageIndexMap::CATEGORY_ALL_PARENTS))
            ->setField(PageIndexMap::CATEGORY_ALL_PARENTS);
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface
     */
    protected function createStringSearchConfig()
    {
        $searchConfig = $this->createSearchConfigMock();
        $searchConfig->getFacetConfigBuilder()
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('foo')
                    ->setParameterName('foo')
                    ->setFieldName(PageIndexMap::STRING_FACET)
                    ->setType(FacetConfigBuilder::TYPE_ENUMERATION)
            );

        return $searchConfig;
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface
     */
    protected function createMultiStringSearchConfig()
    {
        $searchConfig = $this->createSearchConfigMock();
        $searchConfig->getFacetConfigBuilder()
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('foo')
                    ->setParameterName('foo')
                    ->setFieldName(PageIndexMap::STRING_FACET)
                    ->setType(FacetConfigBuilder::TYPE_ENUMERATION)
            )
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('bar')
                    ->setParameterName('bar')
                    ->setFieldName(PageIndexMap::STRING_FACET)
                    ->setType(FacetConfigBuilder::TYPE_ENUMERATION)
            )
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('baz')
                    ->setParameterName('baz')
                    ->setFieldName(PageIndexMap::STRING_FACET)
                    ->setType(FacetConfigBuilder::TYPE_BOOL)
            );

        return $searchConfig;
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface
     */
    protected function createIntegerSearchConfig()
    {
        $searchConfig = $this->createSearchConfigMock();
        $searchConfig->getFacetConfigBuilder()
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('foo')
                    ->setParameterName('foo')
                    ->setFieldName(PageIndexMap::INTEGER_FACET)
                    ->setType(FacetConfigBuilder::TYPE_ENUMERATION)
            );

        return $searchConfig;
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface
     */
    protected function createMultiIntegerSearchConfig()
    {
        $searchConfig = $this->createSearchConfigMock();
        $searchConfig->getFacetConfigBuilder()
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('foo')
                    ->setParameterName('foo')
                    ->setFieldName(PageIndexMap::INTEGER_FACET)
                    ->setType(FacetConfigBuilder::TYPE_ENUMERATION)
            )
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('bar')
                    ->setParameterName('bar')
                    ->setFieldName(PageIndexMap::INTEGER_FACET)
                    ->setType(FacetConfigBuilder::TYPE_ENUMERATION)
            )
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('baz')
                    ->setParameterName('baz')
                    ->setFieldName(PageIndexMap::INTEGER_FACET)
                    ->setType(FacetConfigBuilder::TYPE_RANGE)
            );

        return $searchConfig;
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface
     */
    protected function createCategorySearchConfig()
    {
        $searchConfig = $this->createSearchConfigMock();
        $searchConfig->getFacetConfigBuilder()
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('foo')
                    ->setParameterName('foo')
                    ->setFieldName(PageIndexMap::CATEGORY_ALL_PARENTS)
                    ->setType(FacetConfigBuilder::TYPE_CATEGORY)
            );

        return $searchConfig;
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface
     */
    protected function createMultiCategorySearchConfig()
    {
        $searchConfig = $this->createSearchConfigMock();
        $searchConfig->getFacetConfigBuilder()
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('foo')
                    ->setParameterName('foo')
                    ->setFieldName(PageIndexMap::CATEGORY_ALL_PARENTS)
                    ->setType(FacetConfigBuilder::TYPE_CATEGORY)
            )
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('bar')
                    ->setParameterName('bar')
                    ->setFieldName(PageIndexMap::CATEGORY_ALL_PARENTS)
                    ->setType(FacetConfigBuilder::TYPE_CATEGORY)
            )
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('baz')
                    ->setParameterName('baz')
                    ->setFieldName(PageIndexMap::CATEGORY_ALL_PARENTS)
                    ->setType(FacetConfigBuilder::TYPE_CATEGORY)
            );

        return $searchConfig;
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface
     */
    protected function createMixedSearchConfig()
    {
        $searchConfig = $this->createSearchConfigMock();
        $searchConfig->getFacetConfigBuilder()
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('foo')
                    ->setParameterName('foo')
                    ->setFieldName(PageIndexMap::STRING_FACET)
                    ->setType(FacetConfigBuilder::TYPE_ENUMERATION)
            )
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('bar')
                    ->setParameterName('bar')
                    ->setFieldName(PageIndexMap::INTEGER_FACET)
                    ->setType(FacetConfigBuilder::TYPE_ENUMERATION)
            )
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('baz')
                    ->setParameterName('baz')
                    ->setFieldName(PageIndexMap::CATEGORY_ALL_PARENTS)
                    ->setType(FacetConfigBuilder::TYPE_CATEGORY)
            );

        return $searchConfig;
    }

}
