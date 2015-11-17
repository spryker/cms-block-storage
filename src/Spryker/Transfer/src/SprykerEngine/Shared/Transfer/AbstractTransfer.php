<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerEngine\Shared\Transfer;

use SprykerEngine\Shared\Transfer\Exception\RequiredTransferPropertyException;
use Zend\Filter\Word\UnderscoreToCamelCase;

abstract class AbstractTransfer extends \ArrayObject implements TransferInterface
{

    /**
     * @var array
     */
    private $modifiedProperties = [];

    /**
     * @var array
     */
    protected $transferMetadata = [];

    /**
     * @var UnderscoreToCamelCase
     */
    private static $filterUnderscoreToCamelCase;

    /**
     * @param bool $recursive
     *
     * @return array
     */
    public function toArray($recursive = true)
    {
        $values = [];
        $propertyNames = $this->getPropertyNames();

        $recursive = true;
        foreach ($propertyNames as $property) {
            $value = $this->callGetMethod($property);

            $arrayKey = $this->transformUnderscoreArrayKey($property);

            if (is_object($value)) {
                if ($recursive && $value instanceof TransferInterface) {
                    $values[$arrayKey] = $value->toArray($recursive);
                } elseif ($recursive && $this->isCollection($property) && count($value) >= 1) {
                    $values = $this->addValuesToCollection($recursive, $value, $values, $arrayKey);
                } else {
                    $values[$arrayKey] = $value;
                }
                continue;
            }

            $values[$arrayKey] = $value;
        }

        return $values;
    }

    /**
     * @return array
     */
    private function getPropertyNames()
    {
        $classVars = get_class_vars(get_class($this));

        unset($classVars['modifiedProperties']);
        unset($classVars['transferMetadata']);
        unset($classVars['filterUnderscoreToCamelCase']);

        return array_keys($classVars);
    }

    /**
     * @param bool $recursive
     *
     * @return array
     */
    public function modifiedToArray($recursive = true)
    {
        $returnData = [];
        foreach ($this->modifiedProperties as $modifiedProperty) {
            $key = $modifiedProperty;
            $getterName = 'get' . ucfirst($modifiedProperty);
            $value = $this->$getterName();
            if (is_object($value)) {
                if ($recursive && $value instanceof TransferInterface) {
                    $returnData[$key] = $value->modifiedToArray($recursive);
                } else {
                    $returnData[$key] = $value;
                }
            } else {
                $returnData[$key] = $value;
            }
        }

        return $returnData;
    }

    /**
     * @param array $data
     * @param bool $ignoreMissingProperty
     *
     * @return $this
     */
    public function fromArray(array $data, $ignoreMissingProperty = false)
    {
        $allProperties = $this->getPropertyNames();
        foreach ($data as $property => $value) {
            $property = $this->filterPropertyUnderscoreToCamelCase($property);

            if ($this->hasProperty($property, $allProperties, $ignoreMissingProperty) === false) {
                continue;
            }

            if ($this->isCollection($property)) {
                $value = $this->processCollection($value, $property, $ignoreMissingProperty);
            } elseif ($this->isTransferClass($property)) {
                $value = $this->initializeNestedTransferObject($property, $value);
            }

            $this->callSetMethod($property, $value, $ignoreMissingProperty);
        }

        return $this;
    }

    /**
     * @param string $elementType
     * @param array|\ArrayObject $arrayObject
     * @param bool $ignoreMissingProperty
     *
     * @return \ArrayObject
     */
    protected function processArrayObject($elementType, $arrayObject, $ignoreMissingProperty = false)
    {
        $transferObjectsArray = new \ArrayObject();
        foreach ($arrayObject as $arrayElement) {
            if (is_array($arrayElement)) {
                if ($this->isAssociativeArray($arrayElement)) {
                    $transferObject = $this->createInstance($elementType);
                    $transferObject->fromArray($arrayElement, $ignoreMissingProperty);
                    $transferObjectsArray->append($transferObject);
                } else {
                    foreach ($arrayElement as $arrayElementItem) {
                        $transferObject = $this->createInstance($elementType);
                        $transferObject->fromArray($arrayElementItem, $ignoreMissingProperty);
                        $transferObjectsArray->append($transferObject);
                    }
                }
            } else {
                $transferObjectsArray->append(new $elementType());
            }
        }

        return $transferObjectsArray;
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    private function isAssociativeArray(array $array)
    {
        return array_values($array) !== $array;
    }

    /**
     * @param string $property
     *
     * @return bool
     */
    private function isCollection($property)
    {
        return $this->transferMetadata[$property]['is_collection'];
    }

    /**
     * @param string $property
     *
     * @return bool
     */
    private function isTransferClass($property)
    {
        return $this->transferMetadata[$property]['is_transfer'];
    }

    /**
     * @param string $property
     */
    protected function addModifiedProperty($property)
    {
        if (!in_array($property, $this->modifiedProperties)) {
            $this->modifiedProperties[] = $property;
        }
    }

    /**
     * @param string $property
     *
     * @throws RequiredTransferPropertyException
     *
     * @return void
     */
    protected function assertPropertyIsSet($property)
    {
        if ($this->$property === null) {
            throw new RequiredTransferPropertyException(sprintf(
                'Missing required property "%s" for transfer %s.',
                $property,
                get_class($this)
            ));
        }
    }

    /**
     * @param string $property
     *
     * @throws RequiredTransferPropertyException
     *
     * @return void
     */
    protected function assertCollectionPropertyIsSet($property)
    {
        /** @var \ArrayObject $collection */
        $collection = $this->$property;
        if ($collection->count() === 0) {
            throw new RequiredTransferPropertyException(sprintf(
                'Empty required collection property "%s" for transfer %s.',
                $property,
                get_class($this)
            ));
        }
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getTypeForProperty($key)
    {
        return $this->transferMetadata[$key]['type'];
    }

    /**
     * Performance-Speedup. We do not want another instance of the filter for each property.
     *
     * @return UnderscoreToCamelCase
     */
    private function getFilterUnderscoreToCamelCase()
    {
        if (self::$filterUnderscoreToCamelCase === null) {
            self::$filterUnderscoreToCamelCase = new UnderscoreToCamelCase();
        }

        return self::$filterUnderscoreToCamelCase;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @param bool $ignoreMissingProperty
     *
     * @throws \InvalidArgumentException
     */
    private function callSetMethod($property, $value, $ignoreMissingProperty)
    {
        $setter = 'set' . ucfirst($property);

        try {
            $this->$setter($value);
        } catch (\Exception $e) {
            if ($ignoreMissingProperty === false) {
                throw new \InvalidArgumentException(
                    sprintf('Missing property "%s" in "%s" (setter %s)', $property, get_class($this), $setter)
                );
            }
        }
    }

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return TransferInterface
     */
    private function initializeNestedTransferObject($property, $value)
    {
        $type = $this->getTypeForProperty($property);
        $transferObject = $this->createInstance($type);
        if (is_array($value)) {
            $transferObject->fromArray($value);
            $value = $transferObject;

            return $value;
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param string $property
     * @param bool $ignoreMissingProperty
     *
     * @return \ArrayObject
     */
    private function processCollection($value, $property, $ignoreMissingProperty = false)
    {
        $elementType = $this->transferMetadata[$property]['type'];
        $value = $this->processArrayObject($elementType, $value, $ignoreMissingProperty);

        return $value;
    }

    /**
     * @param string $type
     *
     * @return TransferInterface
     */
    private function createInstance($type)
    {
        return new $type();
    }

    /**
     * @param string $property
     * @param array $properties
     * @param bool $ignoreMissingProperty
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    private function hasProperty($property, array $properties, $ignoreMissingProperty)
    {
        if (in_array($property, $properties) === false) {
            if ($ignoreMissingProperty) {
                return false;
            } else {
                throw new \InvalidArgumentException(
                    sprintf('Missing property "%s" in "%s"', $property, get_class($this))
                );
            }
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function filterPropertyUnderscoreToCamelCase($key)
    {
        $filter = $this->getFilterUnderscoreToCamelCase();
        $property = lcfirst($filter->filter($key));

        return $property;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    private function callGetMethod($property)
    {
        $getter = 'get' . ucfirst($property);
        $value = $this->$getter();

        return $value;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    private function transformUnderscoreArrayKey($property)
    {
        $property = $this->transferMetadata[$property]['name_underscore'];

        return $property;
    }

    /**
     * @param bool $recursive
     * @param mixed $value
     * @param array $values
     * @param string $arrayKey
     *
     * @return array
     */
    private function addValuesToCollection($recursive, $value, $values, $arrayKey)
    {
        foreach ($value as $elementKey => $arrayElement) {
            if (is_array($arrayElement) || is_scalar($arrayElement)) {
                $values[$arrayKey][$elementKey] = $arrayElement;
            } else {
                $values[$arrayKey][$elementKey] = $arrayElement->toArray($recursive);
            }
        }

        return $values;
    }

}
