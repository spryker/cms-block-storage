<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Shared\Money\Converter;

use PHPUnit_Framework_TestCase;
use Spryker\Shared\Money\Converter\DecimalToIntegerConverter;
use Spryker\Shared\Money\Exception\InvalidConverterArgumentException;

/**
 * @group Unit
 * @group Spryker
 * @group Shared
 * @group Money
 * @group Converter
 * @group DecimalToIntegerConverterTest
 */
class DecimalToIntegerConverterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider convertValues
     *
     * @param float $input
     * @param int $expected
     *
     * @return void
     */
    public function testConvertShouldReturnInteger($input, $expected)
    {
        $decimalToIntegerConverter = new DecimalToIntegerConverter();

        $this->assertSame($expected, $decimalToIntegerConverter->convert($input));
    }

    /**
     * @return array
     */
    public function convertValues()
    {
        return [
            [10.01, 1001],
            [10.10, 1010],
            [10.00, 1000],
            [1.00, 100],
            [0.10, 10],
            [0.01, 1],
        ];
    }

    /**
     * @return void
     */
    public function testConvertShouldThrowExceptionIfValueNotInt()
    {
        $this->expectException(InvalidConverterArgumentException::class);

        $integerToDecimalConverter = new DecimalToIntegerConverter();
        $integerToDecimalConverter->convert(100);
    }

}
