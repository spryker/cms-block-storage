<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\SprykerFeature\Zed\Tax;

use Codeception\TestCase\Test;
use SprykerFeature\Zed\Tax\Persistence\Propel\SpyTaxSet;
use SprykerFeature\Zed\Tax\Persistence\Propel\SpyTaxRate;
use SprykerEngine\Zed\Kernel\Business\Factory;
use SprykerEngine\Zed\Kernel\Locator;
use SprykerFeature\Zed\Tax\Business\TaxFacade;
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
    const DUMMY_TAX_RATE1_PERCENTAGE = 2.5;
    const DUMMY_TAX_RATE2_NAME = 'Regional';
    const DUMMY_TAX_RATE2_PERCENTAGE = 10;

    /**
     * @var TaxFacade
     */
    private $taxFacade;

    /**
     * @var AutoCompletion $locator
     */
    protected $locator;

    public function setUp()
    {
        parent::setUp();

        $this->locator = Locator::getInstance();
        $this->taxFacade = new TaxFacade(new Factory('Tax'), $this->locator);
    }

    public function testGetTaxRates()
    {
        $this->loadFixtures();
        $taxRateCollectionTransfer = $this->taxFacade->getTaxRates();
        $this->assertNotEmpty($taxRateCollectionTransfer->getTaxRates());
    }

    public function testGetTaxRate()
    {
        $persistedTaxSet = $this->loadFixtures();
        $result = $this->taxFacade->getTaxRate($persistedTaxSet->getSpyTaxRates()[0]->getIdTaxRate());
        $this->assertNotEmpty($result);
        $this->assertEquals(self::DUMMY_TAX_RATE1_NAME, $result->getName());
        $this->assertEquals(self::DUMMY_TAX_RATE1_PERCENTAGE, $result->getRate());
    }

    public function testTaxRateExists()
    {
        $persistedTaxSet = $this->loadFixtures();
        $result = $this->taxFacade->taxRateExists($persistedTaxSet->getSpyTaxRates()[0]->getIdTaxRate());
        $this->assertTrue($result);
    }

    public function testGetTaxSets()
    {
        $this->loadFixtures();
        $taxSetCollectionTransfer = $this->taxFacade->getTaxSets();
        $this->assertNotEmpty($taxSetCollectionTransfer->getTaxSets());
    }

    public function testGetTaxSet()
    {
        $persistedTaxSet = $this->loadFixtures();
        $result = $this->taxFacade->getTaxSet($persistedTaxSet->getIdTaxSet());
        $this->assertNotEmpty($result);
        $this->assertEquals(self::DUMMY_TAX_SET_NAME, $result->getName());
    }

    public function testTaxSetExists()
    {
        $persistedTaxSet = $this->loadFixtures();
        $result = $this->taxFacade->taxSetExists($persistedTaxSet->getIdTaxSet());
        $this->assertTrue($result);
    }

    public function testExceptionRaisedIfAttemptingToFetchNonExistentTaxRate()
    {
        $this->setExpectedException('SprykerFeature\Zed\Tax\Business\Model\Exception\ResourceNotFoundException');
        $this->taxFacade->getTaxSet(9999999999);
    }

    public function testExceptionRaisedIfAttemptingToFetchNonExistentTaxSet()
    {
        $this->setExpectedException('SprykerFeature\Zed\Tax\Business\Model\Exception\ResourceNotFoundException');
        $this->taxFacade->getTaxRate(9999999999);
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
