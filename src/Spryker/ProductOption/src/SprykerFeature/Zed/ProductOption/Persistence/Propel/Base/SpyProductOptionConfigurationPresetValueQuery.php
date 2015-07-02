<?php

namespace SprykerFeature\Zed\ProductOption\Persistence\Propel\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPresetValue as ChildSpyProductOptionConfigurationPresetValue;
use SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPresetValueQuery as ChildSpyProductOptionConfigurationPresetValueQuery;
use SprykerFeature\Zed\ProductOption\Persistence\Propel\Map\SpyProductOptionConfigurationPresetValueTableMap;

/**
 * Base class that represents a query for the 'spy_product_option_configuration_preset_value' table.
 *
 *
 *
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery orderByFkProductOptionConfigurationPreset($order = Criteria::ASC) Order by the fk_product_option_configuration_preset column
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery orderByFkProductOptionValueUsage($order = Criteria::ASC) Order by the fk_product_option_value_usage column
 *
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery groupByFkProductOptionConfigurationPreset() Group by the fk_product_option_configuration_preset column
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery groupByFkProductOptionValueUsage() Group by the fk_product_option_value_usage column
 *
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery leftJoinSpyProductOptionConfigurationPreset($relationAlias = null) Adds a LEFT JOIN clause to the query using the SpyProductOptionConfigurationPreset relation
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery rightJoinSpyProductOptionConfigurationPreset($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SpyProductOptionConfigurationPreset relation
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery innerJoinSpyProductOptionConfigurationPreset($relationAlias = null) Adds a INNER JOIN clause to the query using the SpyProductOptionConfigurationPreset relation
 *
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery leftJoinSpyProductOptionValueUsage($relationAlias = null) Adds a LEFT JOIN clause to the query using the SpyProductOptionValueUsage relation
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery rightJoinSpyProductOptionValueUsage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SpyProductOptionValueUsage relation
 * @method     ChildSpyProductOptionConfigurationPresetValueQuery innerJoinSpyProductOptionValueUsage($relationAlias = null) Adds a INNER JOIN clause to the query using the SpyProductOptionValueUsage relation
 *
 * @method     \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPresetQuery|\SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionValueUsageQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSpyProductOptionConfigurationPresetValue findOne(ConnectionInterface $con = null) Return the first ChildSpyProductOptionConfigurationPresetValue matching the query
 * @method     ChildSpyProductOptionConfigurationPresetValue findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSpyProductOptionConfigurationPresetValue matching the query, or a new ChildSpyProductOptionConfigurationPresetValue object populated from the query conditions when no match is found
 *
 * @method     ChildSpyProductOptionConfigurationPresetValue findOneByFkProductOptionConfigurationPreset(int $fk_product_option_configuration_preset) Return the first ChildSpyProductOptionConfigurationPresetValue filtered by the fk_product_option_configuration_preset column
 * @method     ChildSpyProductOptionConfigurationPresetValue findOneByFkProductOptionValueUsage(int $fk_product_option_value_usage) Return the first ChildSpyProductOptionConfigurationPresetValue filtered by the fk_product_option_value_usage column
 *
 * @method     ChildSpyProductOptionConfigurationPresetValue[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSpyProductOptionConfigurationPresetValue objects based on current ModelCriteria
 * @method     ChildSpyProductOptionConfigurationPresetValue[]|ObjectCollection findByFkProductOptionConfigurationPreset(int $fk_product_option_configuration_preset) Return ChildSpyProductOptionConfigurationPresetValue objects filtered by the fk_product_option_configuration_preset column
 * @method     ChildSpyProductOptionConfigurationPresetValue[]|ObjectCollection findByFkProductOptionValueUsage(int $fk_product_option_value_usage) Return ChildSpyProductOptionConfigurationPresetValue objects filtered by the fk_product_option_value_usage column
 * @method     ChildSpyProductOptionConfigurationPresetValue[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SpyProductOptionConfigurationPresetValueQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \SprykerFeature\Zed\ProductOption\Persistence\Propel\Base\SpyProductOptionConfigurationPresetValueQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'zed', $modelName = '\\SprykerFeature\\Zed\\ProductOption\\Persistence\\Propel\\SpyProductOptionConfigurationPresetValue', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSpyProductOptionConfigurationPresetValueQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSpyProductOptionConfigurationPresetValueQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSpyProductOptionConfigurationPresetValueQuery) {
            return $criteria;
        }
        $query = new ChildSpyProductOptionConfigurationPresetValueQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$fk_product_option_configuration_preset, $fk_product_option_value_usage] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildSpyProductOptionConfigurationPresetValue|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SpyProductOptionConfigurationPresetValueTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SpyProductOptionConfigurationPresetValueTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSpyProductOptionConfigurationPresetValue A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT fk_product_option_configuration_preset, fk_product_option_value_usage FROM spy_product_option_configuration_preset_value WHERE fk_product_option_configuration_preset = :p0 AND fk_product_option_value_usage = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildSpyProductOptionConfigurationPresetValue $obj */

            /* @var $locator \Generated\Zed\Ide\AutoCompletion */
            $locator = \SprykerEngine\Zed\Kernel\Locator::getInstance();
            $obj = $locator->productOption()->entitySpyProductOptionConfigurationPresetValue();

            $obj->hydrate($row);
            SpyProductOptionConfigurationPresetValueTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildSpyProductOptionConfigurationPresetValue|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildSpyProductOptionConfigurationPresetValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_CONFIGURATION_PRESET, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSpyProductOptionConfigurationPresetValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_CONFIGURATION_PRESET, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the fk_product_option_configuration_preset column
     *
     * Example usage:
     * <code>
     * $query->filterByFkProductOptionConfigurationPreset(1234); // WHERE fk_product_option_configuration_preset = 1234
     * $query->filterByFkProductOptionConfigurationPreset(array(12, 34)); // WHERE fk_product_option_configuration_preset IN (12, 34)
     * $query->filterByFkProductOptionConfigurationPreset(array('min' => 12)); // WHERE fk_product_option_configuration_preset > 12
     * </code>
     *
     * @see       filterBySpyProductOptionConfigurationPreset()
     *
     * @param     mixed $fkProductOptionConfigurationPreset The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSpyProductOptionConfigurationPresetValueQuery The current query, for fluid interface
     */
    public function filterByFkProductOptionConfigurationPreset($fkProductOptionConfigurationPreset = null, $comparison = null)
    {
        if (is_array($fkProductOptionConfigurationPreset)) {
            $useMinMax = false;
            if (isset($fkProductOptionConfigurationPreset['min'])) {
                $this->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_CONFIGURATION_PRESET, $fkProductOptionConfigurationPreset['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fkProductOptionConfigurationPreset['max'])) {
                $this->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_CONFIGURATION_PRESET, $fkProductOptionConfigurationPreset['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_CONFIGURATION_PRESET, $fkProductOptionConfigurationPreset, $comparison);
    }

    /**
     * Filter the query on the fk_product_option_value_usage column
     *
     * Example usage:
     * <code>
     * $query->filterByFkProductOptionValueUsage(1234); // WHERE fk_product_option_value_usage = 1234
     * $query->filterByFkProductOptionValueUsage(array(12, 34)); // WHERE fk_product_option_value_usage IN (12, 34)
     * $query->filterByFkProductOptionValueUsage(array('min' => 12)); // WHERE fk_product_option_value_usage > 12
     * </code>
     *
     * @see       filterBySpyProductOptionValueUsage()
     *
     * @param     mixed $fkProductOptionValueUsage The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSpyProductOptionConfigurationPresetValueQuery The current query, for fluid interface
     */
    public function filterByFkProductOptionValueUsage($fkProductOptionValueUsage = null, $comparison = null)
    {
        if (is_array($fkProductOptionValueUsage)) {
            $useMinMax = false;
            if (isset($fkProductOptionValueUsage['min'])) {
                $this->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE, $fkProductOptionValueUsage['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fkProductOptionValueUsage['max'])) {
                $this->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE, $fkProductOptionValueUsage['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE, $fkProductOptionValueUsage, $comparison);
    }

    /**
     * Filter the query by a related \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPreset object
     *
     * @param \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPreset|ObjectCollection $spyProductOptionConfigurationPreset The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSpyProductOptionConfigurationPresetValueQuery The current query, for fluid interface
     */
    public function filterBySpyProductOptionConfigurationPreset($spyProductOptionConfigurationPreset, $comparison = null)
    {
        if ($spyProductOptionConfigurationPreset instanceof \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPreset) {
            return $this
                ->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_CONFIGURATION_PRESET, $spyProductOptionConfigurationPreset->getIdProductOptionConfigurationPreset(), $comparison);
        } elseif ($spyProductOptionConfigurationPreset instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_CONFIGURATION_PRESET, $spyProductOptionConfigurationPreset->toKeyValue('PrimaryKey', 'IdProductOptionConfigurationPreset'), $comparison);
        } else {
            throw new PropelException('filterBySpyProductOptionConfigurationPreset() only accepts arguments of type \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPreset or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SpyProductOptionConfigurationPreset relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSpyProductOptionConfigurationPresetValueQuery The current query, for fluid interface
     */
    public function joinSpyProductOptionConfigurationPreset($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SpyProductOptionConfigurationPreset');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'SpyProductOptionConfigurationPreset');
        }

        return $this;
    }

    /**
     * Use the SpyProductOptionConfigurationPreset relation SpyProductOptionConfigurationPreset object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPresetQuery A secondary query class using the current class as primary query
     */
    public function useSpyProductOptionConfigurationPresetQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSpyProductOptionConfigurationPreset($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SpyProductOptionConfigurationPreset', '\SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPresetQuery');
    }

    /**
     * Filter the query by a related \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionValueUsage object
     *
     * @param \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionValueUsage|ObjectCollection $spyProductOptionValueUsage The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSpyProductOptionConfigurationPresetValueQuery The current query, for fluid interface
     */
    public function filterBySpyProductOptionValueUsage($spyProductOptionValueUsage, $comparison = null)
    {
        if ($spyProductOptionValueUsage instanceof \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionValueUsage) {
            return $this
                ->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE, $spyProductOptionValueUsage->getIdProductOptionValueUsage(), $comparison);
        } elseif ($spyProductOptionValueUsage instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE, $spyProductOptionValueUsage->toKeyValue('PrimaryKey', 'IdProductOptionValueUsage'), $comparison);
        } else {
            throw new PropelException('filterBySpyProductOptionValueUsage() only accepts arguments of type \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionValueUsage or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SpyProductOptionValueUsage relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSpyProductOptionConfigurationPresetValueQuery The current query, for fluid interface
     */
    public function joinSpyProductOptionValueUsage($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SpyProductOptionValueUsage');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'SpyProductOptionValueUsage');
        }

        return $this;
    }

    /**
     * Use the SpyProductOptionValueUsage relation SpyProductOptionValueUsage object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionValueUsageQuery A secondary query class using the current class as primary query
     */
    public function useSpyProductOptionValueUsageQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSpyProductOptionValueUsage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SpyProductOptionValueUsage', '\SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionValueUsageQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSpyProductOptionConfigurationPresetValue $spyProductOptionConfigurationPresetValue Object to remove from the list of results
     *
     * @return $this|ChildSpyProductOptionConfigurationPresetValueQuery The current query, for fluid interface
     */
    public function prune($spyProductOptionConfigurationPresetValue = null)
    {
        if ($spyProductOptionConfigurationPresetValue) {
            $this->addCond('pruneCond0', $this->getAliasedColName(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_CONFIGURATION_PRESET), $spyProductOptionConfigurationPresetValue->getFkProductOptionConfigurationPreset(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE), $spyProductOptionConfigurationPresetValue->getFkProductOptionValueUsage(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the spy_product_option_configuration_preset_value table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SpyProductOptionConfigurationPresetValueTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SpyProductOptionConfigurationPresetValueTableMap::clearInstancePool();
            SpyProductOptionConfigurationPresetValueTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SpyProductOptionConfigurationPresetValueTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SpyProductOptionConfigurationPresetValueTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            SpyProductOptionConfigurationPresetValueTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SpyProductOptionConfigurationPresetValueTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // SpyProductOptionConfigurationPresetValueQuery
