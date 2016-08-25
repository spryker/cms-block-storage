<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Propel\Business\Model;

use Spryker\Zed\Propel\Business\Model\PropelGroupedSchemaFinder;
use Spryker\Zed\Propel\Business\Model\PropelSchemaFinder;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Propel
 * @group Business
 * @group Model
 * @group PropelGroupedSchemaFinderTest
 */
class PropelGroupedSchemaFinderTest extends AbstractPropelSchemaTest
{

    const NAME_OF_SCHEMA_FILE_GROUP = 'spy_foo.schema.xml';

    /**
     * @return void
     */
    public function testGetSchemasShouldReturnArrayWithOneEntryGroupedByFileNameIfFileWithSameNameOnlyExistsOnce()
    {
        $schemaFinder = new PropelSchemaFinder(
            [$this->getFixtureDirectory()]
        );

        $schemaGrouper = new PropelGroupedSchemaFinder(
            $schemaFinder
        );

        $groupedSchemaFiles = $schemaGrouper->getGroupedSchemaFiles();
        $this->assertInternalType('array', $groupedSchemaFiles);
        $this->assertArrayHasKey(self::NAME_OF_SCHEMA_FILE_GROUP, $groupedSchemaFiles);
        $this->assertCount(1, $groupedSchemaFiles[self::NAME_OF_SCHEMA_FILE_GROUP]);
    }

    /**
     * @return void
     */
    public function testGetSchemasShouldReturnArrayWithTwoEntriesGroupedByFileNameIfFileWithSameNameExistsMoreThenOnce()
    {
        $subDirectory = $this->getFixtureDirectory() . DIRECTORY_SEPARATOR . 'subDir';
        if (!is_dir($subDirectory)) {
            mkdir($subDirectory);
        }
        touch($subDirectory . DIRECTORY_SEPARATOR . self::NAME_OF_SCHEMA_FILE_GROUP);

        $schemaFinder = new PropelSchemaFinder(
            [$this->getFixtureDirectory(), $subDirectory]
        );

        $schemaGrouper = new PropelGroupedSchemaFinder(
            $schemaFinder
        );

        $groupedSchemaFiles = $schemaGrouper->getGroupedSchemaFiles();
        $this->assertInternalType('array', $groupedSchemaFiles);
        $this->assertArrayHasKey(self::NAME_OF_SCHEMA_FILE_GROUP, $groupedSchemaFiles);
        $this->assertCount(2, $groupedSchemaFiles[self::NAME_OF_SCHEMA_FILE_GROUP]);
    }

}
