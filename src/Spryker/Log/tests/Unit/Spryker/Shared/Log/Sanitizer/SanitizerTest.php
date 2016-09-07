<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Shared\Log\Sanitizer;

use Spryker\Shared\Log\Sanitizer\Sanitizer;
use Spryker\Shared\Log\Sanitizer\SanitizerInterface;

/**
 * @group Unit
 * @group Spryker
 * @group Shared
 * @group Log
 * @group Sanitizer
 * @group SanitizerTest
 */
class SanitizerTest extends \PHPUnit_Framework_TestCase
{

    const SANITIZED_VALUE = '***';

    /**
     * @return void
     */
    public function testInstantiateWithArguments()
    {
        $sanitizer = new Sanitizer([], self::SANITIZED_VALUE);

        $this->assertInstanceOf(SanitizerInterface::class, $sanitizer);
    }

    /**
     * @return void
     */
    public function testSanitizeValueValueShouldNotSanitizeWhenKeysNotMatching()
    {
        $sanitizer = new Sanitizer(['foo'], self::SANITIZED_VALUE);

        $this->assertSame('bar', $sanitizer->sanitizeValue('bar', 'baz'));
    }

    /**
     * @return void
     */
    public function testSanitizeValueShouldReturnSanitizedWhenKeyMatches()
    {
        $sanitizer = new Sanitizer(['sanitize'], self::SANITIZED_VALUE);

        $this->assertSame(static::SANITIZED_VALUE, $sanitizer->sanitizeValue('bar', 'sanitize'));
    }

    /**
     * @return void
     */
    public function testSanitizeShouldReturnNotSanitizedWhenKeysNotMatching()
    {
        $sanitizer = new Sanitizer(['sanitize'], self::SANITIZED_VALUE);

        $input = ['foo' => 'bar'];
        $expected = $input;

        $this->assertSame($expected, $sanitizer->sanitize($input));
    }

    /**
     * @return void
     */
    public function testSanitizeShouldReturnSanitizedWhenKeyMatches()
    {
        $sanitizer = new Sanitizer(['sanitize'], self::SANITIZED_VALUE);

        $input = ['sanitize' => 'sanitize me'];
        $expected = ['sanitize' => static::SANITIZED_VALUE];

        $this->assertSame($expected, $sanitizer->sanitize($input));
    }

    /**
     * @return void
     */
    public function testSanitizeWithInnerArrayShouldReturnSanitizedWhenKeyMatches()
    {
        $sanitizer = new Sanitizer(['sanitize', 'password'], self::SANITIZED_VALUE);

        $input = [
            'foo' => 'bar',
            'bar' => [
                'sanitize' => 'sanitize me'
            ],
            'password' => [
                'pass' => 'my secret password',
                'confirm' => 'my secret password',
            ]
        ];
        $expected = $input;
        $expected['bar']['sanitize'] = static::SANITIZED_VALUE;
        $expected['password'] = static::SANITIZED_VALUE;

        $this->assertSame($expected, $sanitizer->sanitize($input));
    }

    /**
     * @return void
     */
    public function testSanitizeWithIndexedArrayShouldReturnSanitizedWhenKeyMatches()
    {
        $sanitizer = new Sanitizer(['sanitize'], self::SANITIZED_VALUE);

        $input = [
            'foo' => 'bar',
            [
                ['sanitize' => 'sanitize me'],
                ['baz' => 'bat'],
            ]
        ];
        $expected = $input;
        $expected[0][0]['sanitize'] = static::SANITIZED_VALUE;

        $this->assertSame($expected, $sanitizer->sanitize($input));
    }

}
