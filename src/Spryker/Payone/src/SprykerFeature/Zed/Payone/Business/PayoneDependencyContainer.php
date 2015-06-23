<?php

namespace SprykerFeature\Zed\Payone\Business;

use Generated\Shared\Payone\StandardParameterInterface;
use SprykerFeature\Zed\Payone\Business\Api\Adapter\AdapterInterface;
use SprykerFeature\Shared\Payone\PayoneApiConstants;
use SprykerEngine\Zed\Kernel\Business\Factory;
use Generated\Zed\Ide\FactoryAutoCompletion\PayoneBusiness;
use SprykerEngine\Zed\Kernel\Business\AbstractDependencyContainer;
use SprykerFeature\Zed\Payone\Business\Api\TransactionStatus\TransactionStatusRequest;
use SprykerFeature\Zed\Payone\Business\Payment\PaymentMethodMapperInterface;
use SprykerFeature\Zed\Payone\Business\Payment\PaymentManager;
use SprykerFeature\Zed\Payone\Business\TransactionStatus\TransactionStatusUpdateManager;
use SprykerFeature\Zed\Payone\PayoneConfig;
use SprykerFeature\Shared\Payone\Dependency\ModeDetectorInterface;
use SprykerFeature\Shared\Payone\Dependency\HashInterface;
use SprykerFeature\Zed\Payone\PayoneDependencyProvider;
use SprykerFeature\Zed\Payone\Persistence\PayoneQueryContainer;
use SprykerFeature\Zed\Payone\Business\SequenceNumber\SequenceNumberProviderInterface;
use SprykerFeature\Zed\Payone\Business\ApiLog\ApiLogFinder;

/**
 * @method Factory|PayoneBusiness getFactory()
 * @method PayoneConfig getConfig()
 */
class PayoneDependencyContainer extends AbstractDependencyContainer
{

    /**
     * @var StandardParameterInterface
     */
    private $standardParameter;



    /**
     * @return PayoneFacade
     */
    public function createPayoneFacade()
    {
        return $this->getLocator()->payone()->facade();
    }

    /**
     * @return PaymentManager
     */
    public function createPaymentManager()
    {
        $paymentManager = $this->getFactory()
            ->createPaymentPaymentManager(
                $this->createExecutionAdapter(),
                $this->createQueryContainer(),
                $this->createStandardParameter(),
                $this->createKeyHashProvider(),
                $this->createSequenceNumberProvider(),
                $this->createModeDetector()
            );

        foreach ($this->getAvailablePaymentMethods() as $paymentMethod) {
            $paymentManager->registerPaymentMethodMapper($paymentMethod);
        }

        return $paymentManager;
    }

    /**
     * @return TransactionStatusUpdateManager
     */
    public function createTransactionStatusManager()
    {
        return $this->getFactory()
            ->createTransactionStatusTransactionStatusUpdateManager(
                $this->createQueryContainer(),
                $this->createStandardParameter(),
                $this->createKeyHashProvider()
            );
    }

    /**
     * @return ApiLogFinder
     */
    public function createApiLogFinder()
    {
        return $this->getFactory()->createApiLogApiLogFinder(
            $this->createQueryContainer()
        );
    }

    /**
     * @return PayoneQueryContainer
     */
    protected function createQueryContainer()
    {
        return $this->getLocator()->payone()->queryContainer();
    }

    /**
     * @return AdapterInterface
     */
    protected function createExecutionAdapter()
    {
        return $this->getFactory()
            ->createApiAdapterHttpCurl(
                $this->createStandardParameter()->getPaymentGatewayUrl()
            );
    }

    /**
     * @return SequenceNumberProviderInterface
     */
    protected function createSequenceNumberProvider()
    {
        return $this->getFactory()
            ->createSequenceNumberSequenceNumberProvider(
                $this->createQueryContainer()
            );
    }

    /**
     * @return HashInterface
     */
    protected function createKeyHashProvider()
    {
        return $this->getFactory()->createKeyHashProvider();
    }

    /**
     * @return ModeDetectorInterface
     */
    protected function createModeDetector()
    {
        return $this->getFactory()->createModeModeDetector();
    }

    /**
     * @param array $requestParams
     * @return TransactionStatusRequest
     */
    public function createTransactionStatusUpdateRequest(array $requestParams)
    {
        return new TransactionStatusRequest($requestParams);
    }

    /**
     * @todo move implementation to PayoneConfig
     * @return array
     */
    protected function getAvailablePaymentMethods()
    {
        $storeConfig = $this->getProvidedDependency(PayoneDependencyProvider::STORE_CONFIG);
        return [
            PayoneApiConstants::PAYMENT_METHOD_PREPAYMENT => $this->getFactory()->createPaymentMethodMapperPrePayment($storeConfig),
            PayoneApiConstants::PAYMENT_METHOD_CREDITCARD_PSEUDO => $this->getFactory()->createPaymentMethodMapperCreditCardPseudo($storeConfig),
            PayoneApiConstants::PAYMENT_METHOD_PAYPAL => $this->getFactory()->createPaymentMethodMapperPayPal($storeConfig)
        ];
    }

    /**
     * @return StandardParameterInterface
     */
    protected function createStandardParameter()
    {
        if ($this->standardParameter === null) {
            $this->standardParameter = $this->getConfig()->getRequestStandardParameter();
        }

        return $this->standardParameter;
    }

}
