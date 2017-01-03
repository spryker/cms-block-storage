<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Client\ZedRequest\Client;

use PHPUnit_Framework_TestCase;
use Unit\Spryker\Client\ZedRequest\Client\Fixture\CommunicationObject;

/**
 * @group Unit
 * @group Spryker
 * @group Client
 * @group ZedRequest
 * @group Client
 * @group AbstractObjectTest
 */
class AbstractObjectTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testConstructorWithoutParameters()
    {
        $object = new CommunicationObject();

        $this->assertEquals([
            'test1' => null,
            'test2' => null,
            'test3' => [],
            'test4' => [null],
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testConstructorWithParameters()
    {
        $data = ['test1' => 1];

        $object = new CommunicationObject($data);

        $this->assertEquals([
            'test1' => 1,
            'test2' => null,
            'test3' => [],
            'test4' => [null],
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testFromArray()
    {
        $data = ['test1' => 1, 'test2' => 'hund'];

        $object = new CommunicationObject();
        $object->fromArray($data);

        $this->assertEquals([
            'test1' => 1,
            'test2' => 'hund',
            'test3' => [],
            'test4' => [null],
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testInvalidData()
    {
        $data = ['test6' => 4, 'test5' => 'data'];

        $object = new CommunicationObject();
        $object->fromArray($data);

        $this->assertEquals([
            'test1' => null,
            'test2' => null,
            'test3' => [],
            'test4' => [null],
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testInvalidAndValidData()
    {
        $data = ['test6' => 4, 'test5' => 'data', 'test2' => 3];

        $object = new CommunicationObject();
        $object->fromArray($data);

        $this->assertEquals([
            'test1' => null,
            'test2' => 3,
            'test3' => [],
            'test4' => [null],
        ], $object->toArray());
    }

}
