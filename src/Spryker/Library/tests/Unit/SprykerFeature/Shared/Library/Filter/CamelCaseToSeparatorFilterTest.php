<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\SprykerFeature\Shared\Library\Filter;

use SprykerFeature\Shared\Library\Filter\CamelCaseToSeparatorFilter;

/**
 * @group Filter
 */
class CamelCaseToSeparatorFilterTest extends \PHPUnit_Framework_TestCase
{

    public function dataProvider()
    {
        return [
            ['camelCase', '-', 'camel-case'],
            ['camelCase', '_', 'camel_case'],
            ['camelCase', '\'', 'camel\'case'],
            ['camelCase', '@', 'camel@case'],
            ['camelCase', '$1', 'camel$1case'],
            ['camelCase', 'asd', 'camelasdcase'],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @return void
     */
    public function testWithDifferentSeparator($camelCase, $separator, $expected)
    {
        $filter = new CamelCaseToSeparatorFilter($separator);
        $this->assertEquals($expected, $filter->filter($camelCase));
    }

}
