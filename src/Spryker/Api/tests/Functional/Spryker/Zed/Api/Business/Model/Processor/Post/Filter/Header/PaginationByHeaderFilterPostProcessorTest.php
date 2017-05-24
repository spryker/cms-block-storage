<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Zed\Api\Business\Model\Processor\Post\Filter\Header;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\ApiPaginationTransfer;
use Generated\Shared\Transfer\ApiRequestTransfer;
use Generated\Shared\Transfer\ApiResponseTransfer;
use Spryker\Zed\Api\ApiConfig;
use Spryker\Zed\Api\Business\Model\Processor\Post\Filter\Header\PaginationByHeaderFilterPostProcessor;
use Spryker\Zed\Api\Business\Model\Processor\Pre\Filter\Header\PaginationByHeaderFilterPreProcessor;

/**
 * @group Functional
 * @group Spryker
 * @group Zed
 * @group Api
 * @group Business
 * @group Model
 * @group Processor
 * @group Post
 * @group Filter
 * @group Header
 * @group PaginationByHeaderFilterPostProcessorTest
 */
class PaginationByHeaderFilterPostProcessorTest extends Test
{

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testProcessWithDefaultsPageOne()
    {
        $config = new ApiConfig();
        $processor = new PaginationByHeaderFilterPostProcessor($config);

        $apiRequestTransfer = new ApiRequestTransfer();
        $apiRequestTransfer->setResource('users');
        $apiRequestTransfer->setHeaderData([
            PaginationByHeaderFilterPreProcessor::RANGE => ['users=19-29'],
        ]);

        $apiResponseTransfer = new ApiResponseTransfer();
        $pagination = new ApiPaginationTransfer();
        $pagination->setPage(2);
        $pagination->setItemsPerPage(10);
        $pagination->setTotal(200);
        $apiResponseTransfer->setPagination($pagination);

        $apiResponseTransfer = $processor->process($apiRequestTransfer, $apiResponseTransfer);
        $expected = [
            'Accept-Ranges' => 'users',
            'Content-Range' => 'users 19-29/200',
        ];
        $this->assertSame($expected, $apiResponseTransfer->getHeaders());
    }

}
