<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Tax\Business\Model;

use Generated\Shared\Transfer\TaxRateTransfer;
use Generated\Shared\Transfer\TaxSetTransfer;
use Generated\Shared\Transfer\TaxRateCollectionTransfer;
use Generated\Shared\Transfer\TaxSetCollectionTransfer;
use Spryker\Zed\Tax\Persistence\TaxQueryContainerInterface;
use Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException;

class TaxReader implements TaxReaderInterface
{

    /**
     * @var TaxQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \Spryker\Zed\Tax\Persistence\TaxQueryContainerInterface $queryContainer
     */
    public function __construct(
        TaxQueryContainerInterface $queryContainer
    ) {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\TaxRateCollectionTransfer
     */
    public function getTaxRates()
    {
        $propelCollection = $this->queryContainer->queryAllTaxRates()->find();

        $transferCollection = new TaxRateCollectionTransfer();
        foreach ($propelCollection as $taxRateEntity) {
            $taxRateTransfer = (new TaxRateTransfer())->fromArray($taxRateEntity->toArray());
            $transferCollection->addTaxRate($taxRateTransfer);
        }

        return $transferCollection;
    }

    /**
     * @param int $id
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException
     *
     * @return \Generated\Shared\Transfer\TaxRateTransfer
     */
    public function getTaxRate($id)
    {
        $taxRateEntity = $this->queryContainer->queryTaxRate($id)->findOne();

        if ($taxRateEntity === null) {
            throw new ResourceNotFoundException();
        }

        return (new TaxRateTransfer())->fromArray($taxRateEntity->toArray());
    }

    /**
     * @param int $id
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return bool
     */
    public function taxRateExists($id)
    {
        $taxRateQuery = $this->queryContainer->queryTaxRate($id);

        return $taxRateQuery->count() > 0;
    }

    /**
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\TaxSetCollectionTransfer
     */
    public function getTaxSets()
    {
        $propelCollection = $this->queryContainer->queryAllTaxsets()->find();

        $transferCollection = new TaxSetCollectionTransfer();
        foreach ($propelCollection as $taxSetEntity) {
            $taxSetTransfer = (new TaxSetTransfer())->fromArray($taxSetEntity->toArray());
            $transferCollection->addTaxSet($taxSetTransfer);
        }

        return $transferCollection;
    }

    /**
     * @param int $id
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException
     *
     * @return \Generated\Shared\Transfer\TaxSetTransfer
     */
    public function getTaxSet($id)
    {
        $taxSetEntity = $this->queryContainer->queryTaxSet($id)->findOne();

        if ($taxSetEntity === null) {
            throw new ResourceNotFoundException();
        }

        $taxSetTransfer = (new TaxSetTransfer())->fromArray($taxSetEntity->toArray());
        foreach ($taxSetEntity->getSpyTaxRates() as $taxRateEntity) {
            $taxRateTransfer = (new TaxRateTransfer())->fromArray($taxRateEntity->toArray());
            $taxSetTransfer->addTaxRate($taxRateTransfer);
        }

        return $taxSetTransfer;
    }

    /**
     * @param int $id
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return bool
     */
    public function taxSetExists($id)
    {
        $taxSetQuery = $this->queryContainer->queryTaxSet($id);

        return $taxSetQuery->count() > 0;
    }

}
