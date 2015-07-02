<?php

namespace SprykerFeature\Zed\ProductOption\Persistence\Propel\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionValuePrice;
use SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionValuePriceQuery;


/**
 * This class defines the structure of the 'spy_product_option_value_price' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class SpyProductOptionValuePriceTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.spryker.spryker.Bundles.ProductOption.src.SprykerFeature.Zed.ProductOption.Persistence.Propel.Map.SpyProductOptionValuePriceTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'zed';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'spy_product_option_value_price';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\SprykerFeature\\Zed\\ProductOption\\Persistence\\Propel\\SpyProductOptionValuePrice';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'vendor.spryker.spryker.Bundles.ProductOption.src.SprykerFeature.Zed.ProductOption.Persistence.Propel.SpyProductOptionValuePrice';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 2;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 2;

    /**
     * the column name for the id_product_option_value_price field
     */
    const COL_ID_PRODUCT_OPTION_VALUE_PRICE = 'spy_product_option_value_price.id_product_option_value_price';

    /**
     * the column name for the price field
     */
    const COL_PRICE = 'spy_product_option_value_price.price';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('IdProductOptionValuePrice', 'Price', ),
        self::TYPE_CAMELNAME     => array('idProductOptionValuePrice', 'price', ),
        self::TYPE_COLNAME       => array(SpyProductOptionValuePriceTableMap::COL_ID_PRODUCT_OPTION_VALUE_PRICE, SpyProductOptionValuePriceTableMap::COL_PRICE, ),
        self::TYPE_FIELDNAME     => array('id_product_option_value_price', 'price', ),
        self::TYPE_NUM           => array(0, 1, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('IdProductOptionValuePrice' => 0, 'Price' => 1, ),
        self::TYPE_CAMELNAME     => array('idProductOptionValuePrice' => 0, 'price' => 1, ),
        self::TYPE_COLNAME       => array(SpyProductOptionValuePriceTableMap::COL_ID_PRODUCT_OPTION_VALUE_PRICE => 0, SpyProductOptionValuePriceTableMap::COL_PRICE => 1, ),
        self::TYPE_FIELDNAME     => array('id_product_option_value_price' => 0, 'price' => 1, ),
        self::TYPE_NUM           => array(0, 1, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('spy_product_option_value_price');
        $this->setPhpName('SpyProductOptionValuePrice');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\SprykerFeature\\Zed\\ProductOption\\Persistence\\Propel\\SpyProductOptionValuePrice');
        $this->setPackage('vendor.spryker.spryker.Bundles.ProductOption.src.SprykerFeature.Zed.ProductOption.Persistence.Propel');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id_product_option_value_price', 'IdProductOptionValuePrice', 'INTEGER', true, null, null);
        $this->addColumn('price', 'Price', 'INTEGER', true, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('SpyProductOptionValue', '\\SprykerFeature\\Zed\\ProductOption\\Persistence\\Propel\\SpyProductOptionValue', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':fk_product_option_value_price',
    1 => ':id_product_option_value_price',
  ),
), 'SET NULL', null, 'SpyProductOptionValues', false);
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to spy_product_option_value_price     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        SpyProductOptionValueTableMap::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('IdProductOptionValuePrice', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('IdProductOptionValuePrice', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('IdProductOptionValuePrice', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? SpyProductOptionValuePriceTableMap::CLASS_DEFAULT : SpyProductOptionValuePriceTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (SpyProductOptionValuePrice object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = SpyProductOptionValuePriceTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = SpyProductOptionValuePriceTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + SpyProductOptionValuePriceTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = SpyProductOptionValuePriceTableMap::OM_CLASS;
            /** @var SpyProductOptionValuePrice $obj */

                /* @var $locator \Generated\Zed\Ide\AutoCompletion */
                $locator = \SprykerEngine\Zed\Kernel\Locator::getInstance();
                $obj = $locator->productOption()->entitySpyProductOptionValuePrice();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            SpyProductOptionValuePriceTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = SpyProductOptionValuePriceTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = SpyProductOptionValuePriceTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var SpyProductOptionValuePrice $obj */

                /* @var $locator \Generated\Zed\Ide\AutoCompletion */
                $locator = \SprykerEngine\Zed\Kernel\Locator::getInstance();
                $obj = $locator->productOption()->entitySpyProductOptionValuePrice();
                $obj->hydrate($row);
                $results[] = $obj;
                SpyProductOptionValuePriceTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(SpyProductOptionValuePriceTableMap::COL_ID_PRODUCT_OPTION_VALUE_PRICE);
            $criteria->addSelectColumn(SpyProductOptionValuePriceTableMap::COL_PRICE);
        } else {
            $criteria->addSelectColumn($alias . '.id_product_option_value_price');
            $criteria->addSelectColumn($alias . '.price');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(SpyProductOptionValuePriceTableMap::DATABASE_NAME)->getTable(SpyProductOptionValuePriceTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(SpyProductOptionValuePriceTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(SpyProductOptionValuePriceTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new SpyProductOptionValuePriceTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a SpyProductOptionValuePrice or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or SpyProductOptionValuePrice object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SpyProductOptionValuePriceTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionValuePrice) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SpyProductOptionValuePriceTableMap::DATABASE_NAME);
            $criteria->add(SpyProductOptionValuePriceTableMap::COL_ID_PRODUCT_OPTION_VALUE_PRICE, (array) $values, Criteria::IN);
        }

        $query = SpyProductOptionValuePriceQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            SpyProductOptionValuePriceTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                SpyProductOptionValuePriceTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the spy_product_option_value_price table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return SpyProductOptionValuePriceQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a SpyProductOptionValuePrice or Criteria object.
     *
     * @param mixed               $criteria Criteria or SpyProductOptionValuePrice object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SpyProductOptionValuePriceTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from SpyProductOptionValuePrice object
        }


        // Set the correct dbName
        $query = SpyProductOptionValuePriceQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // SpyProductOptionValuePriceTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
SpyProductOptionValuePriceTableMap::buildTableMap();
