<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\SprykerFeature\Client\ZedRequest\Service\Client\Fixture;

use SprykerEngine\Shared\Transfer\AbstractTransfer;
use SprykerEngine\Shared\Transfer\Exception\RequiredTransferPropertyException;

class TestTransfer extends AbstractTransfer
{

    const FOO = 'foo';

    /**
     * @var string
     */
    protected $foo;

    /**
     * @var array
     */
    protected $transferMetadata = [
        self::FOO => [
            'type' => 'string',
            'name_underscore' => 'foo',
            'is_collection' => false,
            'is_transfer' => false,
        ],
    ];


    /**
     * @param string $foo
     *
     * @return $this
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
        $this->addModifiedProperty(self::FOO);

        return $this;
    }

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @throws RequiredTransferPropertyException
     *
     * @return self
     */
    public function requireFoo()
    {
        $this->assertPropertyIsSet(self::FOO);

        return $this;
    }

}
