<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Tax\Business\Model;

use Generated\Shared\Transfer\TaxRateTransfer;
use Generated\Shared\Transfer\TaxSetTransfer;
use Generated\Shared\Transfer\TaxRateCollectionTransfer;
use Generated\Shared\Transfer\TaxSetCollectionTransfer;
use SprykerFeature\Zed\Tax\Persistence\TaxQueryContainerInterface;
use SprykerFeature\Zed\Tax\TaxConfig;
use Propel\Runtime\Exception\PropelException;
use SprykerFeature\Zed\Tax\Business\Model\Exception\ResourceNotFoundException;


class TaxReader implements TaxReaderInterface
{
    /**
     * @var TaxQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param TaxQueryContainerInterface $queryContainer
     */
    public function __construct(
        TaxQueryContainerInterface $queryContainer
    ) {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @return TaxRateCollectionTransfer
     * @throws PropelException
     */
    public function getTaxRates()
    {
        $propelCollection = $this->queryContainer->queryAllTaxRates()->find();

        $transferCollection = new TaxRateCollectionTransfer();
        foreach ($propelCollection as $taxRateEntity) {
            $taxRateTransfer = (new TaxRateTransfer)->fromArray($taxRateEntity->toArray());
            $transferCollection->addTaxRate($taxRateTransfer);
        }

        return $transferCollection;
    }

    /**
     * @param int $id
     *
     * @return TaxRateTransfer
     * @throws PropelException
     * @throws ResourceNotFoundException
     */
    public function getTaxRate($id)
    {
        $taxRateEntity = $this->queryContainer->queryTaxRate($id)->findOne();

        if (null == $taxRateEntity) {
            throw new ResourceNotFoundException();
        }

        return (new TaxRateTransfer())->fromArray($taxRateEntity->toArray());
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws PropelException
     */
    public function taxRateExists($id)
    {
        $taxRateQuery = $this->queryContainer->queryTaxRate($id);

        return $taxRateQuery->count() > 0;
    }

    /**
     * @return TaxSetCollectionTransfer
     * @throws PropelException
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
     * @return TaxSetTransfer
     * @throws PropelException
     * @throws ResourceNotFoundException
     */
    public function getTaxSet($id)
    {
        $taxSetEntity = $this->queryContainer->queryTaxSet($id)->findOne();

        if (null == $taxSetEntity) {
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
     * @return bool
     * @throws PropelException
     */
    public function taxSetExists($id)
    {
        $taxSetQuery = $this->queryContainer->queryTaxSet($id);

        return $taxSetQuery->count() > 0;
    }
}
