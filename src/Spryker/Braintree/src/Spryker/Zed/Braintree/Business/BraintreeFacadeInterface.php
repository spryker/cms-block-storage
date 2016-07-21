<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Braintree\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface BraintreeFacadeInterface
{

    /**
     * Specification:
     * - Saves order payment method data according to quote and checkout response transfer data.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer);

    /**
     * Specification:
     * - Sends pre-authorize payment request to Braintree gateway to retrieve transaction data.
     * - Checks that form data matches transaction response data
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function preCheckPayment(QuoteTransfer $quoteTransfer);

    /**
     * Specification:
     * - Processes payment confirmation request to Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function authorizePayment(OrderTransfer $orderTransfer, $idPayment);

    /**
     * Specification:
     * - Processes cancel payment request to Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function revertPayment(OrderTransfer $orderTransfer, $idPayment);

    /**
     * Specification:
     * - Processes capture payment request to Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function capturePayment(OrderTransfer $orderTransfer, $idPayment);

    /**
     * Specification:
     * - Processes refund payment request to Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function refundPayment(OrderTransfer $orderTransfer, $idPayment);

    /**
     * Specification:
     * - Checks if pre-authorization API request got success response from Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isAuthorizationApproved(OrderTransfer $orderTransfer);

    /**
     * Specification:
     * - Checks if cancel API request got success response from Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isReversalApproved(OrderTransfer $orderTransfer);

    /**
     * Specification:
     * - Checks if capture API request got success response from Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCaptureApproved(OrderTransfer $orderTransfer);

    /**
     * Specification:
     * - Checks if refund API request got success response from Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundApproved(OrderTransfer $orderTransfer);

}
