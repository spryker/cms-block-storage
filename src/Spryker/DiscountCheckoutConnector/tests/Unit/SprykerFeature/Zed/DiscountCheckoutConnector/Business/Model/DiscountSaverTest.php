<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\SprykerFeature\Zed\DiscountCheckoutConnector\Business\Model;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEngine\Zed\Kernel\AbstractUnitTest;
use SprykerFeature\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Orm\Zed\Discount\Persistence\SpyDiscountVoucher;
use Orm\Zed\Discount\Persistence\SpyDiscountVoucherPool;
use SprykerFeature\Zed\DiscountCheckoutConnector\Business\Model\DiscountSaver;

/**
 * @group SprykerFeature
 * @group Zed
 * @group DiscountCheckoutConnector
 * @group Business
 * @group DiscountSaver
 */
class DiscountSaverTest extends AbstractUnitTest
{

    const DISCOUNT_DISPLAY_NAME = 'discount';
    const DISCOUNT_AMOUNT = 100;
    const DISCOUNT_ACTION = 'action';

    const ID_SALES_ORDER = 1;
    const USED_CODE_1 = 'used code 1';
    const USED_CODE_2 = 'used code 2';


    public function testSaveDiscountMustSaveSalesItemsDiscount()
    {
        $discountSaver = $this->getDiscountSaverMock(['persistSalesDiscount']);
        $discountSaver->expects($this->once())
            ->method('persistSalesDiscount')
        ;

        $orderTransfer = new OrderTransfer();

        $discountTransfer = new DiscountTransfer();
        $discountTransfer->setDisplayName(self::DISCOUNT_DISPLAY_NAME);
        $discountTransfer->setAmount(self::DISCOUNT_AMOUNT);
        $discountTransfer->setAction(self::DISCOUNT_ACTION);

        $orderItemTransfer = new ItemTransfer();
        $orderItemTransfer->addDiscount($discountTransfer);

        $orderTransfer->addItem($orderItemTransfer);

        $checkoutResponseTransfer = new CheckoutResponseTransfer();

        $discountSaver->saveDiscounts($orderTransfer, $checkoutResponseTransfer);
    }

    public function testSaveDiscountMustNotSaveSalesDiscountCodeIfUsedCodesCanNotBeFound()
    {
        $discountSaver = $this->getDiscountSaverMock(['persistSalesDiscount', 'saveUsedCodes']);
        $discountSaver->expects($this->once())
            ->method('persistSalesDiscount')
        ;
        $discountSaver->expects($this->never())
            ->method('saveUsedCodes')
        ;

        $orderTransfer = new OrderTransfer();

        $discountTransfer = new DiscountTransfer();

        $orderItemTransfer = new ItemTransfer();
        $orderItemTransfer->addDiscount($discountTransfer);

        $orderTransfer->addItem($orderItemTransfer);

        $orderTransfer->setIdSalesOrder(self::ID_SALES_ORDER);
        $checkoutResponseTransfer = new CheckoutResponseTransfer();

        $discountSaver->saveDiscounts($orderTransfer, $checkoutResponseTransfer);
    }

    public function testSaveDiscountMustSaveSalesDiscountCodesIfUsedCodesPresent()
    {
        $discountSaver = $this->getDiscountSaverMock(['persistSalesDiscount', 'persistSalesDiscountCode', 'getDiscountVoucherEntityByCode']);
        $discountSaver->expects($this->once())
            ->method('persistSalesDiscount')
        ;
        $discountSaver->expects($this->exactly(2))
            ->method('persistSalesDiscountCode')
        ;
        $discountSaver->expects($this->exactly(2))
            ->method('getDiscountVoucherEntityByCode')
            ->will($this->returnCallback([$this, 'getDiscountVoucherEntityByCode']))
        ;

        $discountTransfer = new DiscountTransfer();
        $discountTransfer->setUsedCodes([self::USED_CODE_1, self::USED_CODE_2]);

        $orderTransfer = new OrderTransfer();

        $orderItemTransfer = new ItemTransfer();
        $orderItemTransfer->addDiscount($discountTransfer);
        $orderTransfer->addItem($orderItemTransfer);

        $orderTransfer->setIdSalesOrder(self::ID_SALES_ORDER);
        $checkoutResponseTransfer = new CheckoutResponseTransfer();

        $discountSaver->saveDiscounts($orderTransfer, $checkoutResponseTransfer);
    }

    public function testSaveDiscountMustNotSaveSalesDiscountCodesIfUsedCodeCanNotBeFound()
    {
        $discountSaver = $this->getDiscountSaverMock(['persistSalesDiscount', 'persistSalesDiscountCode', 'getDiscountVoucherEntityByCode']);
        $discountSaver->expects($this->once())
            ->method('persistSalesDiscount')
        ;
        $discountSaver->expects($this->never())
            ->method('persistSalesDiscountCode')
        ;
        $discountSaver->expects($this->once())
            ->method('getDiscountVoucherEntityByCode')
            ->will($this->returnValue(false))
        ;

        $discountTransfer = new DiscountTransfer();
        $discountTransfer->setUsedCodes([self::USED_CODE_1]);

        $orderTransfer = new OrderTransfer();

        $orderItemTransfer = new ItemTransfer();
        $orderItemTransfer->addDiscount($discountTransfer);
        $orderTransfer->addItem($orderItemTransfer);

        $orderTransfer->setIdSalesOrder(self::ID_SALES_ORDER);
        $checkoutResponseTransfer = new CheckoutResponseTransfer();

        $discountSaver->saveDiscounts($orderTransfer, $checkoutResponseTransfer);
    }

    /**
     * @return SpyDiscountVoucher
     */
    public function getDiscountVoucherEntityByCode()
    {
        $discountVoucherEntity = new SpyDiscountVoucher();
        $discountVoucherPoolEntity = new SpyDiscountVoucherPool();
        $discountVoucherEntity->setVoucherPool($discountVoucherPoolEntity);

        return $discountVoucherEntity;
    }

    /**
     * @param array $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|DiscountQueryContainerInterface
     */
    private function getDiscountQueryContainerMock(array $methods = [])
    {
        $discountQueryContainerMock = $this->getMock('SprykerFeature\Zed\Discount\Persistence\DiscountQueryContainerInterface', $methods);

        return $discountQueryContainerMock;
    }

    /**
     * @param array $discountSaverMethods
     * @param array $queryContainerMethods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|DiscountSaver
     */
    private function getDiscountSaverMock(array $discountSaverMethods = [], array $queryContainerMethods = [])
    {
        $discountSaverMock = $this->getMock(
            'SprykerFeature\Zed\DiscountCheckoutConnector\Business\Model\DiscountSaver',
            $discountSaverMethods,
            [$this->getDiscountQueryContainerMock($queryContainerMethods), $this->getFacade(null, 'Discount')]
        );

        return $discountSaverMock;
    }

}
