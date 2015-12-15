<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\Spryker\Zed\ProductOptionCartConnector\Business;

use Spryker\Zed\Kernel\AbstractFunctionalTest;
use Spryker\Zed\ProductOptionCartConnector\Business\ProductOptionCartConnectorFacade;
use Generated\Shared\Transfer\ChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductOptionTransfer;
use Functional\Spryker\Zed\ProductOption\Persistence\DbFixturesLoader;

/**
 * @group Business
 * @group Zed
 * @group ProductOptionCartConnector
 * @group ProductOptionCartConnectorFacadeTest
 */
class ProductOptionCartConnectorFacadeTest extends AbstractFunctionalTest
{

    const LOCALE_CODE = 'xx_XX';

    /**
     * @var ProductOptionCartConnectorFacade
     */
    private $facade;

    /**
     * @var array
     */
    protected $ids = [];

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->facade = $this->getFacade();
        $this->ids = DbFixturesLoader::loadFixtures();
    }

    /**
     * @return void
     */
    public function testExpandProductOption()
    {
        $productOptionTransfer = (new ProductOptionTransfer())
            ->setIdOptionValueUsage($this->ids['idUsageLarge'])
            ->setLocaleCode(self::LOCALE_CODE);

        $itemTransfer = (new ItemTransfer())
            ->addProductOption($productOptionTransfer);

        $changeTransfer = (new ChangeTransfer())
            ->addItem($itemTransfer);

        $this->facade->expandProductOptions($changeTransfer);

        $productOptionTransfer = $changeTransfer->getItems()[0]->getProductOptions()[0];

        $this->assertEquals($this->ids['idUsageLarge'], $productOptionTransfer->getIdOptionValueUsage());
        $this->assertEquals(self::LOCALE_CODE, $productOptionTransfer->getLocaleCode());
        $this->assertEquals('Size', $productOptionTransfer->getLabelOptionType());
        $this->assertEquals('Large', $productOptionTransfer->getLabelOptionValue());
        $this->assertEquals(199, $productOptionTransfer->getGrossPrice());

        $taxSetTransfer = $productOptionTransfer->getTaxSet();

        $this->assertEquals('Baz', $taxSetTransfer->getName());

        $taxRateTransfer = $taxSetTransfer->getTaxRates()[0];
        $this->assertEquals('Foo', $taxRateTransfer->getName());
        $this->assertEquals('10', $taxRateTransfer->getRate());
    }

}
