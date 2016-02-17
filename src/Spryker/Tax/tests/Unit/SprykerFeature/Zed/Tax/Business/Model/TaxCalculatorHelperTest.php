<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\SprykerFeature\Zed\Tax\Business\Model\Calculator;

use Spryker\Zed\Tax\Business\Model\PriceCalculationHelper;

class TaxCalculatorHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return void
     */
    public function testTaxValueFromTax()
    {
        $taxCalculatorHelper = $this->createPriceCalculationHelper();

        $netValueFromPrice = $taxCalculatorHelper->getNetValueFromPrice(100, 19);

        $this->assertEquals(84, $netValueFromPrice);
    }

    /**
     * @return void
     */
    public function testTaxValueFroPrice()
    {
        $taxCalculatorHelper = $this->createPriceCalculationHelper();

        $netValueFromPrice = $taxCalculatorHelper->getTaxValueFromPrice(100, 19);

        $this->assertEquals(16, $netValueFromPrice);
    }

    /**
     * @return void
     */
    public function testTaxRateFromPrice()
    {
        $taxCalculatorHelper = $this->createPriceCalculationHelper();

        $netValueFromPrice = $taxCalculatorHelper->getTaxValueFromPrice(100, 84);

        $this->assertEquals(46, $netValueFromPrice);
    }

    /**
     * @return \SprykerFeature\Zed\Tax\Business\Model\PriceCalculationHelper
     */
    protected function createPriceCalculationHelper()
    {
        return new PriceCalculationHelper();
    }
}
