<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\Spryker\Shared\Transfer\Fixtures;

use Spryker\Shared\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Shared\Transfer\TransferInterface;
use Spryker\Shared\Transfer\AbstractTransfer as ParentAbstractTransfer;

class AbstractTransfer extends ParentAbstractTransfer
{

    const STRING = 'string';

    const INT = 'int';

    const BOOL = 'bool';

    const ARRAY_PROPERTY = 'array';

    const TRANSFER = 'transfer';

    const TRANSFER_COLLECTION = 'transferCollection';

    /**
     * @var string
     */
    protected $string;

    /**
     * @var int
     */
    protected $int;

    /**
     * @var bool
     */
    protected $bool;

    /**
     * @var array
     */
    protected $array = [];

    /**
     * @var TransferInterface
     */
    protected $transfer;

    /**
     * @var \ArrayObject|TransferInterface[]
     */
    protected $transferCollection;

    /**
     * @var array
     */
    protected $transferMetadata = [
        self::STRING => [
            'type' => 'string',
            'name_underscore' => 'string',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::INT => [
            'type' => 'int',
            'name_underscore' => 'int',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::BOOL => [
            'type' => 'bool',
            'name_underscore' => 'bool',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::ARRAY_PROPERTY => [
            'type' => 'array',
            'name_underscore' => 'array',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::TRANSFER => [
            'type' => 'Unit\Spryker\Shared\Transfer\Fixtures\AbstractTransfer',
            'name_underscore' => 'transfer',
            'is_collection' => false,
            'is_transfer' => true,
        ],
        self::TRANSFER_COLLECTION => [
            'type' => 'Unit\Spryker\Shared\Transfer\Fixtures\AbstractTransfer',
            'name_underscore' => 'transfer_collection',
            'is_collection' => true,
            'is_transfer' => true,
        ],
    ];

    /**
     * @param string $string
     *
     * @return $this
     */
    public function setString($string)
    {
        $this->string = $string;
        $this->addModifiedProperty(self::STRING);

        return $this;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * @throws RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireString()
    {
        $this->assertPropertyIsSet(self::STRING);

        return $this;
    }

    /**
     * @param int $int
     *
     * @return $this
     */
    public function setInt($int)
    {
        $this->int = $int;
        $this->addModifiedProperty(self::INT);

        return $this;
    }

    /**
     * @return int
     */
    public function getInt()
    {
        return $this->int;
    }

    /**
     * @throws RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireInt()
    {
        $this->assertPropertyIsSet(self::INT);

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setBool($bool)
    {
        $this->bool = $bool;
        $this->addModifiedProperty(self::BOOL);

        return $this;
    }

    /**
     * @return bool
     */
    public function getBool()
    {
        return $this->bool;
    }

    /**
     * @throws RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireBool()
    {
        $this->assertPropertyIsSet(self::BOOL);

        return $this;
    }

    /**
     * @param array $array
     *
     * @return $this
     */
    public function setArray(array $array = [])
    {
        $this->array = $array;
        $this->addModifiedProperty(self::ARRAY_PROPERTY);

        return $this;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * @param array $arr
     *
     * @return $this
     */
    public function addArr($arr)
    {
        $this->array[] = $arr;
        $this->addModifiedProperty(self::ARRAY_PROPERTY);

        return $this;
    }

    /**
     * @throws RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireArr()
    {
        $this->assertCollectionPropertyIsSet(self::ARRAY_PROPERTY);

        return $this;
    }

    /**
     * @param TransferInterface|null $transfer
     *
     * @return $this
     */
    public function setTransfer(TransferInterface $transfer = null)
    {
        $this->transfer = $transfer;
        $this->addModifiedProperty(self::TRANSFER);

        return $this;
    }

    /**
     * @return TransferInterface
     */
    public function getTransfer()
    {
        return $this->transfer;
    }

    /**
     * @throws RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireTransfer()
    {
        $this->assertPropertyIsSet(self::TRANSFER);

        return $this;
    }

    /**
     * @param \ArrayObject|TransferInterface[] $transferCollection
     *
     * @return $this
     */
    public function setTransferCollection(\ArrayObject $transferCollection)
    {
        $this->transferCollection = $transferCollection;
        $this->addModifiedProperty(self::TRANSFER_COLLECTION);

        return $this;
    }

    /**
     * @return TransferInterface[]
     */
    public function getTransferCollection()
    {
        return $this->transferCollection;
    }

    /**
     * @param TransferInterface $transferCollection
     *
     * @return $this
     */
    public function addTransferCollection(TransferInterface $transferCollection)
    {
        $this->transferCollection[] = $transferCollection;
        $this->addModifiedProperty(self::TRANSFER_COLLECTION);

        return $this;
    }

    /**
     * @throws RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireTransferCollection()
    {
        $this->assertCollectionPropertyIsSet(self::TRANSFER_COLLECTION);

        return $this;
    }

}
