<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\Spryker\Zed\Transfer\Business\Model\Generator;

use Spryker\Zed\Transfer\Business\Model\Generator\TransferDefinitionMerger;

/**
 * @group Spryker
 * @group Zed
 * @group Transfer
 * @group Business
 * @group TransferDefinitionMerger
 */
class TransferDefinitionMergerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testMergeShouldReturnMergedTransferDefinition()
    {
        $helper = new TransferDefinitionMergerHelper();
        $transferDefinitions = [
            $helper->getTransferDefinition1(),
            $helper->getTransferDefinition2(),
        ];

        $expected = [];
        $expected['Transfer'] = $helper->getExpectedTransfer();

        $merger = new TransferDefinitionMerger();
        $this->assertEquals($expected, $merger->merge($transferDefinitions));
    }

    /**
     * @return void
     */
    public function testMergeShouldThrowExceptionIfTwoPropertiesWithSameNameDefineDifferentAttributes()
    {
        $helper = new TransferDefinitionMergerHelper();
        $property1 = $helper->getTransferDefinition1();

        $property1['property'] = [
            [
                'name' => 'propertyA',
                'type' => 'int',
            ],
        ];

        $transferDefinitions = [
            $property1,
            $helper->getTransferDefinition2(),
        ];

        $expected = [];
        $expected['Transfer'] = $helper->getExpectedTransfer();

        $merger = new TransferDefinitionMerger();

        $this->setExpectedException('\Exception', 'Property \'propertyA\' defined more than once with different attributes! To fix this, search for \'property name="propertyA"\' in the code base and fix the wrong one.');
        $merger->merge($transferDefinitions);
    }

}
