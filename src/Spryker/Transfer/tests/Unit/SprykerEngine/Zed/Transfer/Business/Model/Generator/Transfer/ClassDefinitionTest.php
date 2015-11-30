<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\SprykerEngine\Zed\Transfer\Business\Model\Generator\Transfer;

use SprykerEngine\Zed\Transfer\Business\Model\Generator\Transfer\ClassDefinition;

/**
 * @group SprykerEngine
 * @group Zed
 * @group Transfer
 * @group Business
 * @group ClassDefinition
 */
class ClassDefinitionTest extends \PHPUnit_Framework_TestCase
{

    public function testGetNameShouldReturnNormalizedTransferName()
    {
        $transferDefinition = [
            'name' => 'name',
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);
        $this->assertSame('NameTransfer', $classDefinition->getName());
    }

    public function testIfOnePropertyIsSetGetPropertiesShouldReturnArrayWithOneProperty()
    {
        $property = $this->getProperty('property1', 'string');
        $transferDefinition = [
            'name' => 'name',
            'property' => [$property],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $properties = $classDefinition->getProperties();
        $this->assertTrue(is_array($properties));

        $given = $properties['property1'];
        $expected = $this->getProperty('property1', 'string');
        $this->assertEquals($expected, $given);
    }

    /**
     * @param $name
     * @param $type
     * @param null $singular
     *
     * @return array
     */
    private function getProperty($name, $type, $singular = null, $return = null)
    {
        $property = [
            'name' => $name,
            'type' => ($return === null) ? $type : $return,
        ];

        if ($singular !== null) {
            $property['singular'] = $singular;
        }

        return $property;
    }

    public function testIfMoreThenOnePropertyIsSetGetPropertiesShouldReturnArrayWithAllProperties()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [
                $this->getProperty('property1', 'string'),
                $this->getProperty('property2', 'string'),
            ],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $properties = $classDefinition->getProperties();
        $this->assertTrue(is_array($properties));

        $givenProperty = $properties['property1'];
        $expectedProperty = $this->getProperty('property1', 'string');
        $this->assertEquals($expectedProperty, $givenProperty);

        $givenProperty = $properties['property2'];
        $expectedProperty = $this->getProperty('property2', 'string');
        $this->assertEquals($expectedProperty, $givenProperty);
    }

    public function testIfPropertyTypeIsArrayWithNameShouldBeMarkedAsArray()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [$this->getProperty('property1', 'array')],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $properties = $classDefinition->getProperties();
        $givenProperty = $properties['property1'];
        $expectedProperty = $this->getProperty('property1', 'array');
        $this->assertEquals($expectedProperty, $givenProperty);
    }

    public function testIfPropertyNameIsCapitalizedNameShouldBeNormalized()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [$this->getProperty('Property1', 'array')],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $properties = $classDefinition->getProperties();
        $givenProperty = $properties['property1'];
        $expectedProperty = $this->getProperty('property1', 'array');
        $this->assertEquals($expectedProperty, $givenProperty);
    }

    public function testIfPropertyTypeIsCollectionConstructorDefinitionMustContainArrayWithThisEntry()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [$this->getProperty('property1', 'Collection[]')],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $constructorDefinition = $classDefinition->getConstructorDefinition();
        $this->assertArrayHasKey('property1', $constructorDefinition);
        $this->assertSame('\\ArrayObject', $constructorDefinition['property1']);
    }

    public function testIfMoreThanOnePropertyTypeHasSameCollectionTypeUseShouldContainOnlyOneOfThisEntries()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [
                $this->getProperty('property1', 'Collection[]'),
                $this->getProperty('property2', 'Collection[]'),
            ],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $uses = $classDefinition->getUses();
        $this->assertCount(1, $uses);
    }

    public function testIfPropertyTypeIsAReferenceToItselfItMustNotAddAUseForIt()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [
                $this->getProperty('property1', 'name[]'),
            ],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $uses = $classDefinition->getUses();
        $this->assertCount(0, $uses);
    }

    public function testIfPropertyTypeIsCollectionTheReturnTypeShouldBeAnArrayObject()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [$this->getProperty('property1', 'Type[]')],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $properties = $classDefinition->getProperties();
        $givenProperty = $properties['property1'];
        $expectedProperty = $this->getProperty('property1', 'Type[]', null, '\ArrayObject|TypeTransfer[]');
        $this->assertEquals($expectedProperty, $givenProperty);
    }

    public function testSimplePropertyShouldHaveOnlyGetterAndSetter()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [$this->getProperty('property1', 'string')],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $methods = $classDefinition->getMethods();
        $givenSetter = $methods['setProperty1'];
        $expectedSetter = $this->getMethod('setProperty1', 'property1', 'string', '$this');

        $givenGetter = $methods['getProperty1'];
        $expectedGetter = $this->getMethod('getProperty1', 'property1', 'string', 'string');
    }

    public function testSimpleStringPropertyShouldHaveOnlySetterWithoutTypeHint()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [$this->getProperty('property1', 'string')],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $methods = $classDefinition->getMethods();
        $givenSetter = $methods['setProperty1'];
        $expectedSetter = $this->getMethod('setProperty1', 'property1', 'string');
    }

    public function testCollectionPropertyShouldHaveOnlySetterWithTypeAsTypeHint()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [$this->getProperty('property1', 'Type[]')],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $methods = $classDefinition->getMethods();
        $givenSetter = $methods['setProperty1'];
        $expectedSetter = $this->getMethod('setProperty1', 'property1', 'Type', null, 'Type');
    }

    public function testCollectionPropertyShouldHaveGetSetAndAdd()
    {
        $transferDefinition = [
            'name' => 'name',
            'property' => [$this->getProperty('property1', 'Type[]')],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $methods = $classDefinition->getMethods();
        $given = $methods['setProperty1'];
        $expected = $this->getMethod('setProperty1', 'property1', '\\ArrayObject|TypeTransfer[]', null, '\\ArrayObject', 'PROPERTY1');
        $this->assertEquals($expected, $given);

        $given = $methods['getProperty1'];
        $expected = $this->getMethod('getProperty1', 'property1', null, 'TypeTransfer[]', null, 'PROPERTY1');
        $this->assertEquals($expected, $given);

        $given = $methods['addProperty1'];
        $expected = $this->getCollectionMethod('addProperty1', 'property1', 'property1', 'TypeTransfer', null, 'TypeTransfer', 'PROPERTY1');
        $this->assertEquals($expected, $given);
    }

    public function testCollectionPropertyWithSingularDefinitionShouldHaveAddWithDefinedName()
    {
        $property = $this->getProperty('properties', 'Type[]', 'property');

        $transferDefinition = [
            'name' => 'name',
            'property' => [$property],
        ];

        $classDefinition = new ClassDefinition();
        $classDefinition->setDefinition($transferDefinition);

        $methods = $classDefinition->getMethods();
        $given = $methods['setProperties'];
        $expected = $this->getMethod('setProperties', 'properties', '\\ArrayObject|TypeTransfer[]', null, '\\ArrayObject', 'PROPERTIES');
        $this->assertEquals($expected, $given);

        $given = $methods['getProperties'];
        $expected = $this->getMethod('getProperties', 'properties', null, 'TypeTransfer[]', null, 'PROPERTIES');
        $this->assertEquals($expected, $given);

        $given = $methods['addProperty'];
        $expected = $this->getCollectionMethod('addProperty', 'property', 'properties', 'TypeTransfer', null, 'TypeTransfer', 'PROPERTIES');
        $this->assertEquals($expected, $given);
    }

    /**
     * @param string $method
     * @param string $property
     * @param string|null $var
     * @param string|null $return
     * @param string|null $typeHint
     * @param string|null $constant
     *
     * @return array
     */
    private function getMethod($method, $property, $var = null, $return = null, $typeHint = null, $constant = null)
    {
        $method = [
            'name' => $method,
            'property' => $property,
        ];

        if ($var !== null) {
            $method['var'] = $var;
        }

        if ($return !== null) {
            $method['return'] = $return;
        }

        if ($typeHint !== null) {
            $method['typeHint'] = $typeHint;
        }

        if ($constant !== null) {
            $method['propertyConst'] = $constant;
        }

        return $method;
    }

    /**
     * @param string $method
     * @param string $property
     * @param string $parent
     * @param string|null $var
     * @param string|null $return
     * @param string|null $typeHint
     * @param string|null $constant
     *
     * @return array
     */
    private function getCollectionMethod($method, $property, $parent, $var = null, $return = null, $typeHint = null, $constant = null)
    {
        $method = $this->getMethod($method, $property, $var, $return, $typeHint, $constant);
        $method['parent'] = $parent;

        return $method;
    }

}
