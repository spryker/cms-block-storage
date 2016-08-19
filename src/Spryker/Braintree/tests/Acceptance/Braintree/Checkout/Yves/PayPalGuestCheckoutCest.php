<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Acceptance\Braintree\Checkout\Yves;

use Acceptance\Braintree\Checkout\Yves\Tester\CheckoutTester;

/**
 * @group Acceptance
 * @group Braintree
 * @group Checkout
 * @group Yves
 * @group PayPalGuestCheckoutCest
 */
class PayPalGuestCheckoutCest
{

    /**
     * @param \Acceptance\Braintree\Checkout\Yves\Tester\CheckoutTester $i
     *
     * @return void
     */
    public function testPayPalCheckoutAsGuest(CheckoutTester $i)
    {
        $i->wantToTest('That i can go through paypal checkout as guest');
        $i->addToCart('/en/samsung-gear-s2-79');
        $i->checkoutWithPayPalAsGuest();
    }

}
