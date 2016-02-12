<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Tax\Business\Model;

use Spryker\Zed\Tax\Business\Model\Exception\MissingTaxRateException;
use Orm\Zed\Tax\Persistence\SpyTaxRate;
use Orm\Zed\Tax\Persistence\SpyTaxSet;
use Spryker\Zed\Tax\Persistence\TaxQueryContainerInterface;
use Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException;
use Generated\Shared\Transfer\TaxRateTransfer;
use Generated\Shared\Transfer\TaxSetTransfer;
use Propel\Runtime\Collection\Collection;

class TaxWriter implements TaxWriterInterface
{

    /**
     * @var \Spryker\Zed\Tax\Persistence\TaxQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Zed\Tax\TaxConfig
     */
    protected $taxSettings;

    /**
     * @var \Spryker\Zed\Tax\Dependency\Plugin\TaxChangePluginInterface[]
     */
    protected $taxChangePlugins;

    /**
     * @param \Spryker\Zed\Tax\Persistence\TaxQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Tax\Dependency\Plugin\TaxChangePluginInterface[] $taxChangePlugins
     */
    public function __construct(
        TaxQueryContainerInterface $queryContainer,
        array $taxChangePlugins
    ) {
        $this->queryContainer = $queryContainer;
        $this->taxChangePlugins = $taxChangePlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\TaxRateTransfer $taxRateTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\TaxRateTransfer
     */
    public function createTaxRate(TaxRateTransfer $taxRateTransfer)
    {
        $taxRateEntity = $this->createTaxRateEntity($taxRateTransfer);

        $taxRateTransfer->setIdTaxRate($taxRateEntity->getIdTaxRate());

        return $taxRateTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\TaxRateTransfer $taxRateTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException
     *
     * @return int
     */
    public function updateTaxRate(TaxRateTransfer $taxRateTransfer)
    {
        $taxRateEntity = $this->queryContainer->queryTaxRate($taxRateTransfer->getIdTaxRate())->findOne();

        if ($taxRateEntity === null) {
            throw new ResourceNotFoundException();
        }

        $taxRateEntity->fromArray($taxRateTransfer->toArray());

        foreach ($this->taxChangePlugins as $plugin) {
            $plugin->handleTaxRateChange($taxRateEntity->getIdTaxRate());
        }

        return $taxRateEntity->save();
    }

    /**
     * @param \Generated\Shared\Transfer\TaxSetTransfer $taxSetTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException
     * @throws \Spryker\Zed\Tax\Business\Model\Exception\MissingTaxRateException
     *
     * @return \Generated\Shared\Transfer\TaxSetTransfer
     */
    public function createTaxSet(TaxSetTransfer $taxSetTransfer)
    {
        $taxSetEntity = new SpyTaxSet();
        $taxSetEntity->setName($taxSetTransfer->getName());

        if ($taxSetTransfer->getTaxRates()->count() === 0) {
            throw new MissingTaxRateException($taxSetTransfer->getName() . ' tax set is missing tax rates');
        }

        foreach ($taxSetTransfer->getTaxRates() as $taxRateTransfer) {
            $taxRateEntity = $this->findOrCreateTaxRateEntity($taxRateTransfer);
            $taxSetEntity->addSpyTaxRate($taxRateEntity);
        }

        $taxSetEntity->save();

        $taxSetTransfer->setIdTaxSet($taxSetEntity->getIdTaxSet());

        return $taxSetTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\TaxSetTransfer $taxSetTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException
     * @throws \Spryker\Zed\Tax\Business\Model\Exception\MissingTaxRateException
     *
     * @return int
     */
    public function updateTaxSet(TaxSetTransfer $taxSetTransfer)
    {
        $taxSetEntity = $this->queryContainer->queryTaxSet($taxSetTransfer->getIdTaxSet())->findOne();

        if ($taxSetEntity === null) {
            throw new ResourceNotFoundException();
        }

        if ($taxSetTransfer->getTaxRates()->count() === 0) {
            throw new MissingTaxRateException();
        }

        $taxSetEntity->setName($taxSetTransfer->getName())->setSpyTaxRates(new Collection());

        foreach ($taxSetTransfer->getTaxRates() as $taxRateTransfer) {
            $taxRateEntity = $this->findOrCreateTaxRateEntity($taxRateTransfer);
            $taxSetEntity->addSpyTaxRate($taxRateEntity);
        }

        foreach ($this->taxChangePlugins as $plugin) {
            $plugin->handleTaxSetChange($taxSetEntity->getIdTaxSet());
        }

        return $taxSetEntity->save();
    }

    /**
     * @param int $taxSetId
     * @param \Generated\Shared\Transfer\TaxRateTransfer $taxRateTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException
     *
     * @return int|null
     */
    public function addTaxRateToTaxSet($taxSetId, TaxRateTransfer $taxRateTransfer)
    {
        $taxSetEntity = $this->queryContainer->queryTaxSet($taxSetId)->findOne();

        if (!$taxSetEntity) {
            throw new ResourceNotFoundException();
        }

        $taxRate = $this->queryContainer->queryTaxRate($taxRateTransfer->getIdTaxRate())->findOne();
        if ($taxSetEntity->getSpyTaxRates()->contains($taxRate)) {
            return null;
        }

        $taxRateEntity = $this->findOrCreateTaxRateEntity($taxRateTransfer);
        $taxSetEntity->addSpyTaxRate($taxRateEntity);

        foreach ($this->taxChangePlugins as $plugin) {
            $plugin->handleTaxSetChange($taxSetEntity->getIdTaxSet());
        }

        return $taxSetEntity->save();
    }

    /**
     * @param int $taxSetId
     * @param int $taxRateId
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException
     * @throws \Spryker\Zed\Tax\Business\Model\Exception\MissingTaxRateException
     *
     * @return int|null
     */
    public function removeTaxRateFromTaxSet($taxSetId, $taxRateId)
    {
        $taxSetEntity = $this->queryContainer->queryTaxSet($taxSetId)->findOne();

        if (!$taxSetEntity) {
            throw new ResourceNotFoundException();
        }

        $taxRate = $this->queryContainer->queryTaxRate($taxRateId)->findOne();

        if (!$taxSetEntity->getSpyTaxRates()->contains($taxRate)) {
            return null;
        }

        if ($taxSetEntity->getSpyTaxRates()->count() === 1) {
            throw new MissingTaxRateException();
        }

        $taxSetEntity->removeSpyTaxRate($taxRate);

        foreach ($this->taxChangePlugins as $plugin) {
            $plugin->handleTaxSetChange($taxSetEntity->getIdTaxSet());
        }

        return $taxSetEntity->save();
    }

    /**
     * @param int $id
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return void
     */
    public function deleteTaxRate($id)
    {
        $taxRateEntity = $this->queryContainer->queryTaxRate($id)->findOne();

        if ($taxRateEntity) {
            $taxRateEntity->delete();
        }
    }

    /**
     * @param int $id
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return void
     */
    public function deleteTaxSet($id)
    {
        $taxSetEntity = $this->queryContainer->queryTaxSet($id)->findOne();

        if ($taxSetEntity) {
            $taxSetEntity->delete();
        }
    }

    /**
     * @param \Generated\Shared\Transfer\TaxRateTransfer $taxRateTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Orm\Zed\Tax\Persistence\SpyTaxRate
     */
    private function createTaxRateEntity(TaxRateTransfer $taxRateTransfer)
    {
        $taxRateEntity = new SpyTaxRate();
        $taxRateEntity->fromArray($taxRateTransfer->toArray());
        $taxRateEntity->save();

        return $taxRateEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\TaxRateTransfer $taxRateTransfer
     *
     * @return \Orm\Zed\Tax\Persistence\SpyTaxRate
     */
    private function findOrCreateTaxRateEntity(TaxRateTransfer $taxRateTransfer)
    {
        if (!empty($taxRateTransfer->getIdTaxRate())) {
            $taxRateEntity = $this->queryContainer->queryTaxRate($taxRateTransfer->getIdTaxRate())->findOne();
            if (!$taxRateEntity) {
                throw new ResourceNotFoundException();
            }
        } else {
            $taxRateEntity = $this->createTaxRateEntity($taxRateTransfer);
        }

        return $taxRateEntity;
    }

}
