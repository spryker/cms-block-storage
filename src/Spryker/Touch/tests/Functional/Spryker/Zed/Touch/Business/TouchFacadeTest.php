<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Zed\Touch\Business;

use Codeception\TestCase\Test;
use Orm\Zed\Touch\Persistence\Map\SpyTouchTableMap;
use Orm\Zed\Touch\Persistence\SpyTouch;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Spryker\Zed\Touch\Business\TouchFacade;

/**
 * @group Spryker
 * @group Zed
 * @group Touch
 * @group Business
 * @group TouchFacade
 */
class TouchFacadeTest extends Test
{

    const ITEM_TYPE = 'test.item';
    const ITEM_ID_1 = 1;
    const ITEM_ID_2 = 2;
    const ITEM_ID_3 = 3;
    const ITEM_ID_FOR_INSERT = 4;

    const ITEM_EVENT_ACTIVE = 'active';
    const ITEM_EVENT_IN_ACTIVE = 'inactive';
    const ITEM_EVENT_DELETED = 'deleted';

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->createTouchEntity(SpyTouchTableMap::COL_ITEM_EVENT_ACTIVE, self::ITEM_ID_1);
        $this->createTouchEntity(SpyTouchTableMap::COL_ITEM_EVENT_INACTIVE, self::ITEM_ID_2);
        $this->createTouchEntity(SpyTouchTableMap::COL_ITEM_EVENT_DELETED, self::ITEM_ID_3);

        sleep(1);
    }

    /**
     * @deprecated This can be removed when all `TouchFacadeInterface::bulkTouch*` methods are removed
     *
     * @dataProvider bulkTouchSetMethodsDataProvider
     *
     * @param string $method
     * @param array $itemIds
     * @param int $expectedAffectedRows
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @return void
     */
    public function testBulkTouchMethods($method, array $itemIds, $expectedAffectedRows)
    {
        $touchFacade = new TouchFacade();
        $affectedRows = $touchFacade->$method(self::ITEM_TYPE, $itemIds);

        $this->assertSame($expectedAffectedRows, $affectedRows);
    }

    /**
     * @return array
     */
    public function bulkTouchMethodsDataProvider()
    {
        return [
            ['bulkTouchActive', [self::ITEM_ID_1], 1],
            ['bulkTouchActive', [self::ITEM_ID_1, self::ITEM_ID_2], 1],
            ['bulkTouchActive', [self::ITEM_ID_1, self::ITEM_ID_FOR_INSERT], 1],

            ['bulkTouchInActive', [self::ITEM_ID_2], 1, self::ITEM_EVENT_IN_ACTIVE],
            ['bulkTouchInActive', [self::ITEM_ID_2, self::ITEM_ID_3], 1, self::ITEM_EVENT_IN_ACTIVE],
            ['bulkTouchInActive', [self::ITEM_ID_2, self::ITEM_ID_FOR_INSERT], 1],

            ['bulkTouchDeleted', [self::ITEM_ID_3], 1, self::ITEM_EVENT_DELETED],
            ['bulkTouchDeleted', [self::ITEM_ID_3, self::ITEM_ID_1], 1, self::ITEM_EVENT_DELETED],
            ['bulkTouchDeleted', [self::ITEM_ID_3, self::ITEM_ID_FOR_INSERT], 1],
        ];
    }

    /**
     * @dataProvider bulkTouchSetMethodsDataProvider
     *
     * @param string $method
     * @param array $itemIds
     * @param int $expectedAffectedRows
     * @param string $expectedItemEvent
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @return void
     */
    public function testBulkTouchSetMethods($method, array $itemIds, $expectedAffectedRows, $expectedItemEvent)
    {
        $touchFacade = new TouchFacade();
        $affectedRows = $touchFacade->$method(self::ITEM_TYPE, $itemIds);

        $this->assertSame($expectedAffectedRows, $affectedRows);

        foreach ($itemIds as $itemId) {
            $touchEntity = $this->getTouchEntityByItemId($itemId);
            $this->assertSame($expectedItemEvent, $touchEntity->getItemEvent());
        }
    }

    /**
     * @return array
     */
    public function bulkTouchSetMethodsDataProvider()
    {
        return [
            ['bulkTouchSetActive', [self::ITEM_ID_1], 1, self::ITEM_EVENT_ACTIVE],
            ['bulkTouchSetActive', [self::ITEM_ID_1, self::ITEM_ID_2], 2, self::ITEM_EVENT_ACTIVE],
            ['bulkTouchSetActive', [self::ITEM_ID_1, self::ITEM_ID_FOR_INSERT], 2, self::ITEM_EVENT_ACTIVE],

            ['bulkTouchSetInActive', [self::ITEM_ID_2], 1, self::ITEM_EVENT_IN_ACTIVE],
            ['bulkTouchSetInActive', [self::ITEM_ID_2, self::ITEM_ID_3], 2, self::ITEM_EVENT_IN_ACTIVE],
            ['bulkTouchSetInActive', [self::ITEM_ID_2, self::ITEM_ID_FOR_INSERT], 2, self::ITEM_EVENT_IN_ACTIVE],

            ['bulkTouchSetDeleted', [self::ITEM_ID_3], 1, self::ITEM_EVENT_DELETED],
            ['bulkTouchSetDeleted', [self::ITEM_ID_3, self::ITEM_ID_1], 2, self::ITEM_EVENT_DELETED],
            ['bulkTouchSetDeleted', [self::ITEM_ID_3, self::ITEM_ID_FOR_INSERT], 2, self::ITEM_EVENT_DELETED],
        ];
    }

    /**
     * @param string $itemEvent
     * @param int $itemId
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @return \Orm\Zed\Touch\Persistence\SpyTouch
     */
    protected function createTouchEntity($itemEvent, $itemId)
    {
        $touchEntity = new SpyTouch();
        $touchEntity->setItemEvent($itemEvent)
            ->setItemId($itemId)
            ->setItemType(self::ITEM_TYPE)
            ->setTouched(new \DateTime());

        $touchEntity->save();

        return $touchEntity;
    }

    /**
     * @param int $itemId
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouch
     */
    protected function getTouchEntityByItemId($itemId)
    {
        $touchQuery = new SpyTouchQuery();

        $touchQuery->filterByItemType(self::ITEM_TYPE)
            ->filterByItemId($itemId);

        return $touchQuery->findOne();
    }

}
