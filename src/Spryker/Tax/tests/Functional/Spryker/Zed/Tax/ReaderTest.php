<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\Spryker\Zed\Tax;

use Codeception\TestCase\Test;
use Orm\Zed\Tax\Persistence\SpyTaxSet;
use Orm\Zed\Tax\Persistence\SpyTaxRate;
use Spryker\Zed\Tax\Business\TaxFacade;
use Generated\Zed\Ide\AutoCompletion;

/**
 * @group Business
 * @group Zed
 * @group Tax
 * @group ReaderTest
 */
class ReaderTest extends Test
{

    const DUMMY_TAX_SET_NAME = 'SalesTax';
    const DUMMY_TAX_RATE1_NAME = 'Local';
    const DUMMY_TAX_RATE1_PERCENTAGE = 25;
    const DUMMY_TAX_RATE2_NAME = 'Regional';
    const DUMMY_TAX_RATE2_PERCENTAGE = 10;
    const NON_EXISTENT_ID = 999999999;

    /**
     * @var \Spryker\Zed\Tax\Business\TaxFacade
     */
    private $taxFacade;

    /**
     * @var \Generated\Zed\Ide\AutoCompletion
     */
    protected $locator;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->taxFacade = new TaxFacade();
    }

    /**
     * @return void
     */
    public function testGetTaxRates()
    {
        $this->loadFixtures();
        $taxRateCollectionTransfer = $this->taxFacade->getTaxRates();
        $this->assertTrue(count($taxRateCollectionTransfer->getTaxRates()) > 0);
    }

    /**
     * @return void
     */
    public function testGetTaxRate()
    {
        $persistedTaxSet = $this->loadFixtures();
        $result = $this->taxFacade->getTaxRate($persistedTaxSet->getSpyTaxRates()[0]->getIdTaxRate());
        $this->assertEquals(self::DUMMY_TAX_RATE1_NAME, $result->getName());
        $this->assertEquals(self::DUMMY_TAX_RATE1_PERCENTAGE, $result->getRate());
    }

    /**
     * @return void
     */
    public function testTaxRateExists()
    {
        $persistedTaxSet = $this->loadFixtures();
        $result = $this->taxFacade->taxRateExists($persistedTaxSet->getSpyTaxRates()[0]->getIdTaxRate());
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testGetTaxSets()
    {
        $this->loadFixtures();
        $taxSetCollectionTransfer = $this->taxFacade->getTaxSets();
        $this->assertNotEmpty($taxSetCollectionTransfer->getTaxSets());
    }

    /**
     * @return void
     */
    public function testGetTaxSet()
    {
        $persistedTaxSet = $this->loadFixtures();
        $result = $this->taxFacade->getTaxSet($persistedTaxSet->getIdTaxSet());
        $this->assertEquals(self::DUMMY_TAX_SET_NAME, $result->getName());
    }

    /**
     * @return void
     */
    public function testTaxSetExists()
    {
        $persistedTaxSet = $this->loadFixtures();
        $result = $this->taxFacade->taxSetExists($persistedTaxSet->getIdTaxSet());
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testExceptionRaisedIfAttemptingToFetchNonExistentTaxRate()
    {
        $this->setExpectedException('Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException');
        $this->taxFacade->getTaxSet(self::NON_EXISTENT_ID);
    }

    /**
     * @return void
     */
    public function testExceptionRaisedIfAttemptingToFetchNonExistentTaxSet()
    {
        $this->setExpectedException('Spryker\Zed\Tax\Business\Model\Exception\ResourceNotFoundException');
        $this->taxFacade->getTaxRate(self::NON_EXISTENT_ID);
    }

    private function loadFixtures()
    {
        $taxRateEntity = new SpyTaxRate();
        $taxRateEntity->setName(self::DUMMY_TAX_RATE1_NAME);
        $taxRateEntity->setRate(self::DUMMY_TAX_RATE1_PERCENTAGE);
        $taxRateEntity->save();

        $taxSetEntity = new SpyTaxSet();
        $taxSetEntity->setName(self::DUMMY_TAX_SET_NAME);
        $taxSetEntity->addSpyTaxRate($taxRateEntity);
        $taxSetEntity->save();

        return $taxSetEntity;
    }

}
