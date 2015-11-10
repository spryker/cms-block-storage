<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\CustomerCheckoutConnector\Business;

use Generated\Shared\CustomerCheckoutConnector\CheckoutRequestInterface;
use Generated\Shared\CustomerCheckoutConnector\OrderInterface;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use SprykerFeature\Zed\CustomerCheckoutConnector\Dependency\Facade\CustomerCheckoutConnectorToCustomerInterface;

class CustomerOrderHydrator implements CustomerOrderHydratorInterface
{

    /**
     * @var CustomerCheckoutConnectorToCustomerInterface
     */
    private $customerFacade;

    /**
     * @param CustomerCheckoutConnectorToCustomerInterface $customerFacade
     */
    public function __construct(CustomerCheckoutConnectorToCustomerInterface $customerFacade)
    {
        $this->customerFacade = $customerFacade;
    }

    /**
     * @param OrderInterface $orderTransfer
     * @param CheckoutRequestInterface $request
     */
    public function hydrateOrderTransfer(OrderInterface $orderTransfer, CheckoutRequestInterface $request)
    {
        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setEmail($request->getEmail());
        $customerTransfer->setPassword($request->getCustomerPassword());

        $this->setGuestProperties($orderTransfer, $request);

        $idUser = $request->getIdUser();
        if ($idUser !== null) {
            $orderTransfer->setFkCustomer($idUser);
            $customerTransfer->setIdCustomer($idUser);
            $customerTransfer = $this->customerFacade->getCustomer($customerTransfer);
        } elseif (!$customerTransfer->getPassword()) {
            $customerTransfer->setIsGuest($request->getIsGuest());
        }

        $billingAddress = $request->getBillingAddress();
        if ($billingAddress !== null) {
            $orderTransfer->setBillingAddress($billingAddress);

            $customerAddressTransfer = new AddressTransfer();
            $customerAddressTransfer->fromArray($billingAddress->toArray(), true);

            $customerTransfer->addBillingAddress($customerAddressTransfer);
        }

        $shippingAddress = $request->getShippingAddress();
        if ($shippingAddress !== null) {
            $orderTransfer->setShippingAddress($shippingAddress);

            $customerAddressTransfer = new AddressTransfer();
            $customerAddressTransfer->fromArray($shippingAddress->toArray(), true);

            $customerTransfer->addShippingAddress($customerAddressTransfer);
        }

        $orderTransfer->setCustomer($customerTransfer);
    }

    /**
     * @param OrderInterface $orderTransfer
     * @param CheckoutRequestInterface $request
     *
     * @return void
     */
    protected function setGuestProperties(
        OrderInterface $orderTransfer,
        CheckoutRequestInterface $request
    ) {
        $orderTransfer->setEmail($request->getEmail());
        $orderTransfer->setFirstName($request->getBillingAddress()->getFirstName());
        $orderTransfer->setLastName($request->getBillingAddress()->getLastName());
        $orderTransfer->setSalutation($request->getBillingAddress()->getSalutation());
    }

}
