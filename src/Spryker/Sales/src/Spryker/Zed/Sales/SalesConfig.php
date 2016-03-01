<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Sales;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Sales\SalesConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class SalesConfig extends AbstractBundleConfig
{

    const PARAM_IS_SALES_ORDER = 'id-sales-order';
    const TEST_CUSTOMER_FIRST_NAME = 'test order';

    /**
     * TODO Not needed, remove
     * this is used in project level src/Pyz/Zed/Sales/SalesConfig.php:28
     *
     * @var array|string[]
     */
    protected static $stateMachineMapper = [
        'invoice' => 'Invoice01',
        'no_payment' => 'Nopayment01',
    ];

    /**
     * Separator for the sequence number
     * @return string
     */
    public function getUniqueIdentifierSeparator()
    {
        return '-';
    }

    /**
     * TODO FW Move the whole algortithm to the bundle config
     *
     * OR-condition
     *
     * @return array
     */
    public function getMarkAsTestConditions()
    {
        return [
            'last_name' => 'Tester',
        ];
    }

    /**
     * Defines the prefix for the sequence number which is the public id of an order.
     *
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    public function getOrderReferenceDefaults()
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $sequenceNumberSettingsTransfer->setName(SalesConstants::NAME_ORDER_REFERENCE);

        $sequenceNumberPrefixParts = [];
        $sequenceNumberPrefixParts[] = Store::getInstance()->getStoreName();
        $sequenceNumberPrefixParts[] = $this->get(SalesConstants::ENVIRONMENT_PREFIX);
        $prefix = implode($this->getUniqueIdentifierSeparator(), $sequenceNumberPrefixParts) . $this->getUniqueIdentifierSeparator();
        $sequenceNumberSettingsTransfer->setPrefix($prefix);

        return $sequenceNumberSettingsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function isTestOrder(QuoteTransfer $quoteTransfer)
    {
        $shippingAddressTransfer = $quoteTransfer->getShippingAddress();

        if ($shippingAddressTransfer === null || $shippingAddressTransfer->getFirstName() !== self::TEST_CUSTOMER_FIRST_NAME) {
            return false;
        }

        return true;
    }

    /**
     * This method determines state machine process from the given quote transfer and order item.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    public function determineProcessForOrderItem(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer)
    {
        throw new \BadMethodCallException('You need to provide at least one state machine process for given method!');
    }

    /**
     * This method provides list of actions for zed order details external blocks
     *
     * @return array
     */
    public function getSalesDetailExternalBlocksUrls()
    {
        return [];
    }

}
