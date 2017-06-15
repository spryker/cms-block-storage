<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Yves\CartVariant\Mapper;

use Spryker\Yves\CartVariant\Mapper\CartItemsAttributeMapper;
use Spryker\Yves\CartVariant\Mapper\CartItemsAvailabilityMapper;

/**
 * @group Unit
 * @group Spryker
 * @group Yves
 * @group CartVariant
 * @group Mapper
 * @group AttributeMapperTest
 */
class AttributeMapperTest extends CartItemsMapperBaseTest
{

    /**
     * @return void
     */
    public function testBuildMap()
    {
        $subject = new CartItemsAttributeMapper(
            $this->buildProductClientMock('attribute.json'),
            new CartItemsAvailabilityMapper($this->buildProductAvailabilityClientMock('availability.json'))
        );
        $result = $subject->buildMap($this->getItems());

        $this->assertArrayHasKey('170_28516206', $result);

        $attributes = $result['170_28516206'];

        $this->assertArrayHasKey('color', $attributes);
        $this->assertSame(3, count($attributes['color']));

        $this->assertSame(1, $this->countSelectedAttributes($attributes['color']));

        $this->assertArrayHasKey('processor_frequency', $attributes);
        $this->assertSame(3, count($attributes['processor_frequency']));

        $this->assertSame(1, $this->countSelectedAttributes($attributes['processor_frequency']));
    }

    /**
     * @return void
     */
    public function testBuildNestedMap()
    {
        $subject = new CartItemsAttributeMapper(
            $this->buildProductClientMock('attributeNested.json'),
            new CartItemsAvailabilityMapper($this->buildProductAvailabilityClientMock('availabilityNested.json'))
        );
        $result = $subject->buildMap($this->getNestedItems());

        $this->assertArrayHasKey('112_312526171', $result);

        $attributes = $result['112_312526171'];

        $this->assertArrayHasKey('chassis_type', $attributes);
        $this->assertSame(1, count($attributes['chassis_type']));

//        $this->assertSame(1, $this->countSelectedAttributes($attributes['color']));
//
//        $this->assertArrayHasKey('processor_frequency', $attributes);
//        $this->assertSame(3, count($attributes['processor_frequency']));
//
//        $this->assertSame(1, $this->countSelectedAttributes($attributes['processor_frequency']));
    }

}
