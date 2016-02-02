<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Client\Catalog\Model;

use Elastica\Filter\Term;
use Elastica\Index;
use Elastica\Query;
use Elastica\Query\Filtered;
use Spryker\Client\Catalog\Model\Builder\FacetAggregationBuilderInterface;
use Spryker\Client\Catalog\Model\Builder\NestedFilterBuilderInterface;
use Spryker\Client\Catalog\Model\Extractor\AggregationExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 */
class FacetSearch extends AbstractSearch
{

    /**
     * @var NestedFilterBuilderInterface
     */
    protected $filterBuilder;

    /**
     * @var array
     */
    protected $category;

    /**
     * @param Request $request
     * @param FacetConfig $facetConfig
     * @param Index $searchIndex
     * @param FacetAggregationBuilderInterface $facetAggregation
     * @param FacetFilterHandlerInterface $facetFilterHandler
     * @param AggregationExtractorInterface $facetExtractor
     * @param AggregationExtractorInterface $rangeExtractor
     * @param CatalogInterface $catalogModel
     * @param array $category
     */
    public function __construct(
        Request $request,
        FacetConfig $facetConfig,
        Index $searchIndex,
        FacetAggregationBuilderInterface $facetAggregation,
        FacetFilterHandlerInterface $facetFilterHandler,
        AggregationExtractorInterface $facetExtractor,
        AggregationExtractorInterface $rangeExtractor,
        CatalogInterface $catalogModel,
        array $category
    ) {
        parent::__construct(
            $request,
            $facetConfig,
            $searchIndex,
            $facetAggregation,
            $facetFilterHandler,
            $facetExtractor,
            $rangeExtractor,
            $catalogModel
        );
        $this->category = $category;
    }

    /**
     * @param Request $request
     *
     * @return \Elastica\Query
     */
    protected function createSearchQuery(Request $request)
    {
        $searchQuery = new Query();
        $this->addCategoryFilterToQuery($searchQuery);
        $this->addSortingToQuery($searchQuery);
        $this->addFacetAggregationToQuery($searchQuery);
        $this->addFacetFiltersToQuery($searchQuery, $request);
        $this->addPaginationToQuery($searchQuery);

        $searchQuery->setSource(['search-result-data']);

        return $searchQuery;
    }

    /**
     * @param Query $query
     *
     * @return void
     */
    protected function addCategoryFilterToQuery(Query $query)
    {
        $query->setQuery(
            (new Filtered())
                ->setFilter(new Term([
                    'category.all-parents' => (int) $this->category['node_id'],
                ]))
        );
    }

}
