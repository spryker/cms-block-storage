<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Assertion\Business\Model;

use PHPUnit_Framework_TestCase;
use Spryker\Zed\Assertion\Business\Exception\InvalidArgumentException;
use Spryker\Zed\Assertion\Business\Model\Assertion;
use stdClass;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Assertion
 * @group Business
 * @group Model
 * @group AssertionTest
 */
class AssertionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider numeric()
     *
     * @param mixed $value
     * @param bool $isValid
     *
     * @return void
     */
    public function testAssertNumeric($value, $isValid)
    {
        $this->doTest($value, $isValid, 'assertNumeric');
    }

    /**
     * @return array
     */
    public function numeric()
    {
        return [
            [0, true],
            [-2, true],
            [0.23, true],
            ['1', true],
            ['1.1', true],
            ['1foo', false],
            [[], false],
            [new stdClass(), false],
        ];
    }

    /**
     * @dataProvider numericNotZero()
     *
     * @param mixed $value
     * @param bool $isValid
     *
     * @return void
     */
    public function testAssertNumericNotZero($value, $isValid)
    {
        $this->doTest($value, $isValid, 'assertNumericNotZero');
    }

    /**
     * @return array
     */
    public function numericNotZero()
    {
        return [
            [0, false],
            [-1, true],
            [1, true],
            ['1', true],
            ['1.1', true],
            ['1foo', false],
            [[], false],
            [new stdClass(), false],
        ];
    }

    /**
     * @dataProvider alphaNumeric()
     *
     * @param mixed $value
     * @param bool $isValid
     *
     * @return void
     */
    public function testAssertAlphaNumeric($value, $isValid)
    {
        $this->doTest($value, $isValid, 'assertAlphaNumeric');
    }

    /**
     * @return array
     */
    public function alphaNumeric()
    {
        return [
            [0, false],
            ['0A', true],
            ['A0', true],
            [1, false],
            ['1A', true],
            ['1', true],
            ['1.1', false],
            ['foo1bar', true],
            ['1foo', true],
            [new stdClass(), false],
            [[], false],
            ['foo.bar', false],
        ];
    }

    /**
     * @dataProvider alpha()
     *
     * @param mixed $value
     * @param bool $isValid
     *
     * @return void
     */
    public function testAssertAlpha($value, $isValid)
    {
        $this->doTest($value, $isValid, 'assertAlpha');
    }

    /**
     * @return array
     */
    public function alpha()
    {
        return [
            [0, false],
            ['1', false],
            ['1.1', false],
            ['foo1bar', false],
            [new stdClass(), false],
            ['foo.bar', false],
            ['foobar', true],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     * @param string $method
     *
     * @return void
     */
    private function doTest($value, $isValid, $method)
    {
        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        $assertion = new Assertion();
        $assertion->$method($value);
    }

}
