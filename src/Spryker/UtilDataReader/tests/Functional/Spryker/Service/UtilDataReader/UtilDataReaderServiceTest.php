<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Service\UtilDataReader;

use PHPUnit_Framework_TestCase;
use Spryker\Service\UtilDataReader\Model\BatchIterator\CountableIteratorInterface;
use Spryker\Service\UtilDataReader\UtilDataReaderService;

/**
 * @group Functional
 * @group Spryker
 * @group Service
 * @group UtilDataReader
 * @group UtilDataReaderServiceTest
 */
class UtilDataReaderServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testGetYamlBatchIteratorReturnsCountableIterator()
    {
        $utilDataReaderService = new UtilDataReaderService();
        $yamlBatchIterator = $utilDataReaderService->getYamlBatchIterator('fileName');

        $this->assertInstanceOf(CountableIteratorInterface::class, $yamlBatchIterator);
    }

}
