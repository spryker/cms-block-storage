<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Calculation;

use Spryker\Zed\Calculation\Communication\Plugin\ExpensesGrossSumAmountCalculatorPlugin;
use Spryker\Zed\Calculation\Communication\Plugin\ExpenseTotalsCalculatorPlugin;
use Spryker\Zed\Calculation\Communication\Plugin\GrandTotalTotalsCalculatorPlugin;
use Spryker\Zed\Calculation\Communication\Plugin\ItemGrossAmountsCalculatorPlugin;
use Spryker\Zed\Calculation\Communication\Plugin\ProductOptionGrossSumCalculatorPlugin;
use Spryker\Zed\Calculation\Communication\Plugin\RemoveAllExpensesCalculatorPlugin;
use Spryker\Zed\Calculation\Communication\Plugin\RemoveTotalsCalculatorPlugin;
use Spryker\Zed\Calculation\Communication\Plugin\SubtotalTotalsCalculatorPlugin;
use Spryker\Zed\DiscountCalculationConnector\Communication\Plugin\GrandTotalWithDiscountsCalculatorPlugin;
use Spryker\Zed\DiscountCalculationConnector\Communication\Plugin\RemoveAllCalculatedDiscountsCalculatorPlugin;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class CalculationDependencyProvider extends AbstractBundleDependencyProvider
{

    const CALCULATOR_STACK = 'calculator stack';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[self::CALCULATOR_STACK] = function (Container $container) {
            return $this->getCalculatorStack($container);
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Calculation\Dependency\Plugin\CalculatorPluginInterface[]
     */
    protected function getCalculatorStack(Container $container)
    {
        return [
            //Remove calculated values, start with clean state.
            new RemoveTotalsCalculatorPlugin(),
            new RemoveAllCalculatedDiscountsCalculatorPlugin(),

            //Item calculators
            new ProductOptionGrossSumCalculatorPlugin(),
            new ItemGrossAmountsCalculatorPlugin(),

            //SubTotal
            new SubtotalTotalsCalculatorPlugin(),

            //Expenses (e.g. shipping)
            new ExpensesGrossSumAmountCalculatorPlugin(),
            new ExpenseTotalsCalculatorPlugin(),

            //GrandTotal
            new GrandTotalTotalsCalculatorPlugin(),
            new GrandTotalWithDiscountsCalculatorPlugin(),
        ];
    }

}
