<?php


namespace Spryker\Zed\ShipmentDiscountConnector\Business\Model;


use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\DiscountableItemTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Shipment\ShipmentConstants;
use Spryker\Zed\ShipmentDiscountConnector\Dependency\Facade\ShipmentDiscountConnectorToDiscountInterface;

class PriceDiscountCollector implements PriceDiscountCollectorInterface
{

    /**
     * @var PriceDiscountDecisionRuleInterface
     */
    protected $priceDiscountDecisionRule;

    /**
     * @param PriceDiscountDecisionRuleInterface $priceDiscountDecisionRule
     */
    public function __construct(PriceDiscountDecisionRuleInterface $priceDiscountDecisionRule)
    {
        $this->priceDiscountDecisionRule = $priceDiscountDecisionRule;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ClauseTransfer $clauseTransfer
     *
     * @return \Generated\Shared\Transfer\DiscountableItemTransfer[]
     */
    public function collect(QuoteTransfer $quoteTransfer, ClauseTransfer $clauseTransfer)
    {
        $discountableItems = [];

        foreach ($quoteTransfer->getExpenses() as $expenseTransfer) {
            if ($expenseTransfer->getType() === ShipmentConstants::SHIPMENT_EXPENSE_TYPE) {
                $isSatisfied = $this->priceDiscountDecisionRule->isSatisfiedBy($quoteTransfer, $expenseTransfer, $clauseTransfer);

                if ($isSatisfied) {
                    $discountableItems[] = $this->createDiscountableItemTransfer($expenseTransfer, $quoteTransfer->getPriceMode());
                }
            }
        }

        return $discountableItems;
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param string $priceMode
     *
     * @return \Generated\Shared\Transfer\DiscountableItemTransfer
     */
    protected function createDiscountableItemTransfer(ExpenseTransfer $expenseTransfer, $priceMode)
    {
        $discountableItemTransfer = new DiscountableItemTransfer();
        $discountableItemTransfer->fromArray($expenseTransfer->toArray(), true);
        $discountableItemTransfer->setUnitGrossPrice($this->getPrice($expenseTransfer, $priceMode));
        $discountableItemTransfer->setOriginalItemCalculatedDiscounts($expenseTransfer->getCalculatedDiscounts());

        return $discountableItemTransfer;
    }

    /**
     * @deprecated This method calculated gross price when in tax mode, because discounts currently working with gross mode, will be removed in the future
     *
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param string $priceMode
     *
     * @return int
     */
    protected function getPrice(ExpenseTransfer $expenseTransfer, $priceMode)
    {
        if ($priceMode === 'NET_MODE') {
            return $expenseTransfer->getUnitNetPrice() + (int)round($expenseTransfer->getUnitNetPrice() * $expenseTransfer->getTaxRate() / 100);
        } else {
            return $expenseTransfer->getUnitGrossPrice();
        }
    }

}