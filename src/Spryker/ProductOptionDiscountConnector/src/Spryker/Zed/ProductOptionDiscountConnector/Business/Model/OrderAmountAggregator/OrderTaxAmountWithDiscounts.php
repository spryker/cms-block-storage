<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\ProductOptionDiscountConnector\Business\Model\OrderAmountAggregator;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\TaxTotalTransfer;
use Spryker\Zed\ProductOptionDiscountConnector\Dependency\Facade\ProductOptionToTaxBridgeInterface;

class OrderTaxAmountWithDiscounts implements OrderAmountAggregatorInterface
{
    /**
     * @var \Spryker\Zed\ProductOptionDiscountConnector\Dependency\Facade\ProductOptionToTaxBridgeInterface
     */
    protected $taxFacade;

    /**
     * @param \Spryker\Zed\ProductOptionDiscountConnector\Dependency\Facade\ProductOptionToTaxBridgeInterface $taxFacade
     */
    public function __construct(ProductOptionToTaxBridgeInterface $taxFacade)
    {
        $this->taxFacade = $taxFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function aggregate(OrderTransfer $orderTransfer)
    {
        $orderEffectiveTaxRate = $this->getOrderEffectiveTaxRate($orderTransfer);
        $totalTaxAmount = $this->getTotalTaxAmount($orderTransfer, $orderEffectiveTaxRate);

        $this->setTaxTotals($orderTransfer, $orderEffectiveTaxRate, $totalTaxAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return int
     */
    protected function getOrderEffectiveTaxRate(OrderTransfer $orderTransfer)
    {
        $itemEffectiveRates = $this->getEfectiveTaxRatesFromTaxableItems($orderTransfer->getItems());
        $expenseEffectiveRates = $this->getEfectiveTaxRatesFromTaxableItems($orderTransfer->getExpenses());
        $productOptionEffectiveRates = $this->getProductOptionEffectiveRates($orderTransfer->getItems());

        $taxRates = array_merge($itemEffectiveRates, $expenseEffectiveRates, $productOptionEffectiveRates);

        $totalTaxRate = array_sum($taxRates);
        if (empty($totalTaxRate)) {
            return 0;
        }

        $effectiveTaxRate = $totalTaxRate / count($taxRates);

        return $effectiveTaxRate;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[]|\Generated\Shared\Transfer\ExpenseTransfer[]|\Generated\Shared\Transfer\ProductOptionTransfer[] $taxableItems
     *
     * @return array|int[]
     */
    protected function getEfectiveTaxRatesFromTaxableItems(\ArrayObject $taxableItems)
    {
        $taxRates = [];
        foreach ($taxableItems as $item) {
            if (!empty($item->getTaxRate())) {
                $taxRates[] = $item->getTaxRate();
            }
        }
        return $taxRates;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return array|int[]
     */
    protected function getProductOptionEffectiveRates(\ArrayObject $items)
    {
        $productOptionRates = [];
        foreach ($items as $itemTransfer) {
            $rates = $this->getEfectiveTaxRatesFromTaxableItems($itemTransfer->getProductOptions());
            $productOptionRates = array_merge($productOptionRates, $rates);
        }

        return $productOptionRates;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $orderEffectiveTaxRate
     *
     * @return int
     */
    protected function getTotalTaxAmount(OrderTransfer $orderTransfer, $orderEffectiveTaxRate)
    {
        $orderTransfer->requireTotals();
        $orderTransfer->getTotals()->requireGrandTotal();

        return $this->taxFacade->getTaxAmountFromGrossPrice(
            $orderTransfer->getTotals()->getGrandTotal(),
            $orderEffectiveTaxRate
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $orderEffectiveTaxRate
     * @param int $totalTaxAmount
     *
     * @return void
     */
    protected function setTaxTotals(OrderTransfer $orderTransfer, $orderEffectiveTaxRate, $totalTaxAmount)
    {
        $taxTotalTransfer = new TaxTotalTransfer();
        $taxTotalTransfer->setAmount($totalTaxAmount);
        $taxTotalTransfer->setTaxRate($orderEffectiveTaxRate);

        $orderTransfer->getTotals()->setTaxTotal($taxTotalTransfer);
    }
}
