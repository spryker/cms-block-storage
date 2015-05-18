<?php

namespace SprykerEngine\Zed\Transfer\Business\Model\Generator;

use Zend\Filter\Word\UnderscoreToCamelCase;

class ClassDefinition implements ClassDefinitionInterface
{

    const TYPE_ARRAY = 'array';
    const TYPE_BOOLEAN = 'bool';
    const TYPE_INTEGER = 'int';
    const TYPE_STRING = 'string';

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $uses = [];

    /**
     * @var array
     */
    private $interfaces = [];

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var array
     */
    private $constructorDefinition = [];

    /**
     * @var array
     */
    private $methods = [];

    /**
     * @param array $transferDefinition
     */
    public function __construct(array $transferDefinition)
    {
        $this->setName($transferDefinition['name']);

        if (isset($transferDefinition['interface'])) {
            $this->addInterfaces($transferDefinition['interface']);
        }

        if (isset($transferDefinition['property'])) {
            $this->addProperties($transferDefinition['property']);
            $this->addMethods($transferDefinition['property']);
        }
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    private function setName($name)
    {
        if (strpos($name, 'Transfer') === false) {
            $name .= 'Transfer';
        }
        $this->name = ucfirst($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $interfaces
     *
     * @return $this
     */
    private function addInterfaces(array $interfaces)
    {
        foreach ($interfaces as $interface) {
            if (is_array($interface)) {
                $this->addInterface($interface['name']);
            } else {
                $this->addInterface($interface);
            }
        }

        return $this;
    }

    /**
     * @param string $interface
     */
    private function addInterface($interface)
    {
        if (!in_array($interface, $this->interfaces)) {
            $interfaceParts = explode('\\', $interface);
            $this->uses[] = $interface;
            $this->interfaces[] = array_pop($interfaceParts);
        }
    }

    /**
     * @return array
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @return array
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * @param array $properties
     */
    private function addProperties(array $properties)
    {
        if (isset($properties[0])) {
            foreach ($properties as $property) {
                $this->addProperty($property);
            }
        } else {
            $this->addProperty($properties);
        }
    }

    /**
     * @param array $property
     */
    private function addProperty(array $property)
    {
        $propertyInfo = [
            'name' => $property['name'],
            'type' => $this->getPropertyVar($property)
        ];

        $this->properties[$property['name']] = $propertyInfo;
        if ($this->isCollection($property)) {
            $this->uses[] = 'Generated\\Shared\\Transfer\\' . str_replace('[]', '', $property['type']);
        }
        $this->addPropertyConstructorIfCollection($property);
    }

    /**
     * @param array $property
     *
     * @return string
     */
    private function getPropertyVar(array $property)
    {
        if ($property['type'] === '[]' || $property['type'] === 'array') {
            return 'array';
        }

        if ($this->isCollection($property)) {
            return '\ArrayObject';
        }

        return $property['type'];
    }

    /**
     * @param array $property
     *
     * @return string
     */
    private function getSetVar(array $property)
    {
        if ($property['type'] === '[]' || $property['type'] === 'array') {
            return 'array';
        }

        if ($this->isCollection($property)) {
            return '\ArrayObject';
        }

        return $property['type'];
    }

    /**
     * @param array $property
     *
     * @return string
     */
    private function getAddVar(array $property)
    {
        if ($property['type'] === '[]' || $property['type'] === 'array') {
            return 'array';
        }

        if ($this->isCollection($property)) {
            return str_replace('[]', '', $property['type']);
        }

        return $property['type'];
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    private function addMethods(array $properties)
    {
        if (isset($properties[0])) {
            foreach ($properties as $property) {
                $this->addPropertyMethods($property);
            }
        } else {
            $this->addPropertyMethods($properties);
        }
    }

    /**
     * @param array $property
     */
    private function addPropertyMethods(array $property)
    {
        if ($this->isCollection($property)) {
            $this->buildCollectionMethods($property);
        } else {
            $this->buildGetterAndSetter($property);
        }
    }

    /**
     * @param array $property
     */
    private function addPropertyConstructorIfCollection(array $property)
    {
        if ($this->isCollection($property)) {
            $this->constructorDefinition[$property['name']] = '\ArrayObject';
        }
    }

    /**
     * @return array
     */
    public function getConstructorDefinition()
    {
        return $this->constructorDefinition;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param array $property
     */
    private function buildCollectionMethods(array $property)
    {
        $this->buildGetterAndSetter($property);
        $this->buildAddMethod($property);
    }

    /**
     * @param array $property
     */
    private function buildGetterAndSetter(array $property)
    {
        $this->buildSetMethod($property);
        $this->buildGetMethod($property);
    }

    /**
     * @param array $property
     *
     * @return string
     */
    private function getPropertyName(array $property)
    {
        $filter = new UnderscoreToCamelCase();

        return lcfirst($filter->filter($property['name']));
    }

    /**
     * @param array $property
     *
     * @return string
     */
    private function getReturnType(array $property)
    {
        if ($property['type'] === 'array' || $property['type'] === '[]') {
            return 'array';
        }

        return $property['type'];
    }

    /**
     * @param array $property
     *
     * @return bool
     */
    private function isCollection(array $property)
    {
        return preg_match('/(.*?)\[\]/', $property['type']);
    }

    /**
     * @param array $property
     *
     * @return bool|string
     */
    private function getTypeHint(array $property)
    {
        if ($property['type'] === 'array' || $property['type'] === '[]') {
            return 'array';
        }

        if (preg_match('/(string|int|bool|boolean)/', $property['type'])) {
            return false;
        }

        if ($this->isCollection($property)) {
            return '\ArrayObject';
        }

        return $property['type'];
    }

    /**
     * @param array $property
     *
     * @return bool|string
     */
    private function getAddTypeHint(array $property)
    {
        if (preg_match('/^(string|int|bool|boolean|array|\[\])/', $property['type'])) {
            return false;
        }

        if ($this->isCollection($property)) {
            return str_replace('[]', '', $property['type']);
        }

        return $property['type'];
    }

    /**
     * @param array $property
     */
    private function buildGetMethod(array $property)
    {
        $propertyName = $this->getPropertyName($property, 'get');
        $methodName = 'get' . ucfirst($propertyName);
        $method = [
            'name' => $methodName,
            'property' => $propertyName,
            'return' => $this->getReturnType($property)
        ];
        $this->methods[$methodName] = $method;
    }

    /**
     * @param $property
     */
    private function buildSetMethod($property)
    {
        $propertyName = $this->getPropertyName($property);
        $methodName = 'set' . ucfirst($propertyName);
        $method = [
            'name' => $methodName,
            'property' => $propertyName,
            'var' => $this->getSetVar($property),
        ];
        $method = $this->addTypeHint($property, $method);

        $this->methods[$methodName] = $method;
    }

    /**
     * @param $property
     */
    private function buildAddMethod($property)
    {
        $parent = $this->getPropertyName($property);
        if (array_key_exists('singular', $property)) {
            $property['name'] = $property['singular'];
        }
        $propertyName = $this->getPropertyName($property);
        $methodName = 'add' . ucfirst($propertyName);
        $method = [
            'name' => $methodName,
            'property' => $propertyName,
            'parent' => $parent,
            'var' => $this->getAddVar($property),
        ];

        $typeHint = $this->getAddTypeHint($property);
        if ($typeHint) {
            $method['typeHint'] = $typeHint;
        }

        $this->methods[$methodName] = $method;
    }

    /**
     * @param array $property
     * @param array $method
     *
     * @return array
     */
    private function addTypeHint(array $property, array $method)
    {
        $typeHint = $this->getTypeHint($property);
        if ($typeHint) {
            $method['typeHint'] = $typeHint;
        }

        return $method;
    }

    /**
     * @param array $property
     * @param array $method
     *
     * @return array
     */
    private function addDefault(array $property, array $method)
    {
        if (array_key_exists('default', $property)) {
            $method['default'] = $property['default'];
        }

        return $method;
    }
}
