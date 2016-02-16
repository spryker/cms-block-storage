<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\Spryker\Zed\Product\Business\Model;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{

    const TEST_MAPPING_ID = 12030;
    const TEST_REVERSED_DIRECTORY_PATH = '/030/21/';
    const PATH_ORIGINAL = 'images/products/original';
    const PATH_PROCESSED = 'images/products/processed';
    const BASE_IMAGE_PATH = '/private/tmp/';

    /**
     * @return \Spryker\Zed\Product\Business\Model\Filesystem
     */
    protected function getFilesystemMock()
    {
        $mock = $this->getMock('Spryker\Zed\Product\Business\Model\Filesystem', ['getConfig']);

        $config = (object)[
            'originalProductImageDirectory' => self::PATH_ORIGINAL,
            'processedProductImageDirectory' => self::PATH_PROCESSED,
        ];

        $mock
            ->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($config));

        return $mock;
    }

}
