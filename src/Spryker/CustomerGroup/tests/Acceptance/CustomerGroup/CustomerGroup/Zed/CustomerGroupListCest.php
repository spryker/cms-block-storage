<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Acceptance\CustomerGroup\Customer\Zed;

use Acceptance\CustomerGroup\CustomerGroup\Zed\PageObject\CustomerGroupListPage;
use Acceptance\CustomerGroup\CustomerGroup\Zed\Tester\CustomerGroupTester;

/**
 * @group Acceptance
 * @group CustomerGroup
 * @group CustomerGroup
 * @group Zed
 * @group CustomerGroupListCest
 */
class CustomerGroupListCest
{

    /**
     * @param \Acceptance\CustomerGroup\CustomerGroup\Zed\Tester\CustomerGroupTester $i
     *
     * @return void
     */
    public function showListOfCustomer(CustomerGroupTester $i)
    {
        $i->amOnPage(CustomerGroupListPage::URL);
        $i->seeElement(CustomerGroupListPage::SELECTOR_TABLE);
    }

}
