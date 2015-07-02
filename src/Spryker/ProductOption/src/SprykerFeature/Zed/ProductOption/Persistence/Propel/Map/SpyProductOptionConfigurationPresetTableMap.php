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
use SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPreset;
use SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPresetQuery;


/**
 * This class defines the structure of the 'spy_product_option_configuration_preset' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class SpyProductOptionConfigurationPresetTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.spryker.spryker.Bundles.ProductOption.src.SprykerFeature.Zed.ProductOption.Persistence.Propel.Map.SpyProductOptionConfigurationPresetTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'zed';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'spy_product_option_configuration_preset';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\SprykerFeature\\Zed\\ProductOption\\Persistence\\Propel\\SpyProductOptionConfigurationPreset';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'vendor.spryker.spryker.Bundles.ProductOption.src.SprykerFeature.Zed.ProductOption.Persistence.Propel.SpyProductOptionConfigurationPreset';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 4;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 4;

    /**
     * the column name for the id_product_option_configuration_preset field
     */
    const COL_ID_PRODUCT_OPTION_CONFIGURATION_PRESET = 'spy_product_option_configuration_preset.id_product_option_configuration_preset';

    /**
     * the column name for the is_default field
     */
    const COL_IS_DEFAULT = 'spy_product_option_configuration_preset.is_default';

    /**
     * the column name for the sequence field
     */
    const COL_SEQUENCE = 'spy_product_option_configuration_preset.sequence';

    /**
     * the column name for the fk_product field
     */
    const COL_FK_PRODUCT = 'spy_product_option_configuration_preset.fk_product';

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
        self::TYPE_PHPNAME       => array('IdProductOptionConfigurationPreset', 'IsDefault', 'Sequence', 'FkProduct', ),
        self::TYPE_CAMELNAME     => array('idProductOptionConfigurationPreset', 'isDefault', 'sequence', 'fkProduct', ),
        self::TYPE_COLNAME       => array(SpyProductOptionConfigurationPresetTableMap::COL_ID_PRODUCT_OPTION_CONFIGURATION_PRESET, SpyProductOptionConfigurationPresetTableMap::COL_IS_DEFAULT, SpyProductOptionConfigurationPresetTableMap::COL_SEQUENCE, SpyProductOptionConfigurationPresetTableMap::COL_FK_PRODUCT, ),
        self::TYPE_FIELDNAME     => array('id_product_option_configuration_preset', 'is_default', 'sequence', 'fk_product', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('IdProductOptionConfigurationPreset' => 0, 'IsDefault' => 1, 'Sequence' => 2, 'FkProduct' => 3, ),
        self::TYPE_CAMELNAME     => array('idProductOptionConfigurationPreset' => 0, 'isDefault' => 1, 'sequence' => 2, 'fkProduct' => 3, ),
        self::TYPE_COLNAME       => array(SpyProductOptionConfigurationPresetTableMap::COL_ID_PRODUCT_OPTION_CONFIGURATION_PRESET => 0, SpyProductOptionConfigurationPresetTableMap::COL_IS_DEFAULT => 1, SpyProductOptionConfigurationPresetTableMap::COL_SEQUENCE => 2, SpyProductOptionConfigurationPresetTableMap::COL_FK_PRODUCT => 3, ),
        self::TYPE_FIELDNAME     => array('id_product_option_configuration_preset' => 0, 'is_default' => 1, 'sequence' => 2, 'fk_product' => 3, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, )
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
        $this->setName('spy_product_option_configuration_preset');
        $this->setPhpName('SpyProductOptionConfigurationPreset');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\SprykerFeature\\Zed\\ProductOption\\Persistence\\Propel\\SpyProductOptionConfigurationPreset');
        $this->setPackage('vendor.spryker.spryker.Bundles.ProductOption.src.SprykerFeature.Zed.ProductOption.Persistence.Propel');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id_product_option_configuration_preset', 'IdProductOptionConfigurationPreset', 'INTEGER', true, null, null);
        $this->addColumn('is_default', 'IsDefault', 'BOOLEAN', true, 1, null);
        $this->addColumn('sequence', 'Sequence', 'INTEGER', false, null, null);
        $this->addForeignKey('fk_product', 'FkProduct', 'INTEGER', 'spy_product', 'id_product', true, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('SpyProduct', '\\SprykerFeature\\Zed\\Product\\Persistence\\Propel\\SpyProduct', RelationMap::MANY_TO_ONE, array('fk_product' => 'id_product', ), 'CASCADE', null);
        $this->addRelation('SpyProductOptionConfigurationPresetValue', '\\SprykerFeature\\Zed\\ProductOption\\Persistence\\Propel\\SpyProductOptionConfigurationPresetValue', RelationMap::ONE_TO_MANY, array('id_product_option_configuration_preset' => 'fk_product_option_configuration_preset', ), 'CASCADE', null, 'SpyProductOptionConfigurationPresetValues');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to spy_product_option_configuration_preset     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        SpyProductOptionConfigurationPresetValueTableMap::clearInstancePool();
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
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('IdProductOptionConfigurationPreset', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('IdProductOptionConfigurationPreset', TableMap::TYPE_PHPNAME, $indexType)];
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
                : self::translateFieldName('IdProductOptionConfigurationPreset', TableMap::TYPE_PHPNAME, $indexType)
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
        return $withPrefix ? SpyProductOptionConfigurationPresetTableMap::CLASS_DEFAULT : SpyProductOptionConfigurationPresetTableMap::OM_CLASS;
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
     * @return array           (SpyProductOptionConfigurationPreset object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = SpyProductOptionConfigurationPresetTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = SpyProductOptionConfigurationPresetTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + SpyProductOptionConfigurationPresetTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = SpyProductOptionConfigurationPresetTableMap::OM_CLASS;
            /** @var SpyProductOptionConfigurationPreset $obj */

                /* @var $locator \Generated\Zed\Ide\AutoCompletion */
                $locator = \SprykerEngine\Zed\Kernel\Locator::getInstance();
                $obj = $locator->productOption()->entitySpyProductOptionConfigurationPreset();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            SpyProductOptionConfigurationPresetTableMap::addInstanceToPool($obj, $key);
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
            $key = SpyProductOptionConfigurationPresetTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = SpyProductOptionConfigurationPresetTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var SpyProductOptionConfigurationPreset $obj */

                /* @var $locator \Generated\Zed\Ide\AutoCompletion */
                $locator = \SprykerEngine\Zed\Kernel\Locator::getInstance();
                $obj = $locator->productOption()->entitySpyProductOptionConfigurationPreset();
                $obj->hydrate($row);
                $results[] = $obj;
                SpyProductOptionConfigurationPresetTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(SpyProductOptionConfigurationPresetTableMap::COL_ID_PRODUCT_OPTION_CONFIGURATION_PRESET);
            $criteria->addSelectColumn(SpyProductOptionConfigurationPresetTableMap::COL_IS_DEFAULT);
            $criteria->addSelectColumn(SpyProductOptionConfigurationPresetTableMap::COL_SEQUENCE);
            $criteria->addSelectColumn(SpyProductOptionConfigurationPresetTableMap::COL_FK_PRODUCT);
        } else {
            $criteria->addSelectColumn($alias . '.id_product_option_configuration_preset');
            $criteria->addSelectColumn($alias . '.is_default');
            $criteria->addSelectColumn($alias . '.sequence');
            $criteria->addSelectColumn($alias . '.fk_product');
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
        return Propel::getServiceContainer()->getDatabaseMap(SpyProductOptionConfigurationPresetTableMap::DATABASE_NAME)->getTable(SpyProductOptionConfigurationPresetTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(SpyProductOptionConfigurationPresetTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(SpyProductOptionConfigurationPresetTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new SpyProductOptionConfigurationPresetTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a SpyProductOptionConfigurationPreset or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or SpyProductOptionConfigurationPreset object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SpyProductOptionConfigurationPresetTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \SprykerFeature\Zed\ProductOption\Persistence\Propel\SpyProductOptionConfigurationPreset) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SpyProductOptionConfigurationPresetTableMap::DATABASE_NAME);
            $criteria->add(SpyProductOptionConfigurationPresetTableMap::COL_ID_PRODUCT_OPTION_CONFIGURATION_PRESET, (array) $values, Criteria::IN);
        }

        $query = SpyProductOptionConfigurationPresetQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            SpyProductOptionConfigurationPresetTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                SpyProductOptionConfigurationPresetTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the spy_product_option_configuration_preset table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return SpyProductOptionConfigurationPresetQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a SpyProductOptionConfigurationPreset or Criteria object.
     *
     * @param mixed               $criteria Criteria or SpyProductOptionConfigurationPreset object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SpyProductOptionConfigurationPresetTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from SpyProductOptionConfigurationPreset object
        }


        // Set the correct dbName
        $query = SpyProductOptionConfigurationPresetQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // SpyProductOptionConfigurationPresetTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
SpyProductOptionConfigurationPresetTableMap::buildTableMap();
