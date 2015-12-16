<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Shared\ZedRequest\Client;

use Spryker\Zed\ZedRequest\Business\Client\Response as ClientResponse;
use Generated\Client\Ide\AutoCompletion;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\RequestException as GuzzleRequestException;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Spryker\Shared\Kernel\Factory\FactoryInterface;
use Spryker\Client\Auth\AuthClientInterface;
use Spryker\Shared\Library\Config;
use Spryker\Shared\Library\System;
use Spryker\Shared\Library\Zed\Exception\InvalidZedResponseException;
use Spryker\Shared\Lumberjack\Model\SharedEventJournal;
use Spryker\Shared\Lumberjack\Model\Event;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Transfer\TransferInterface;
use Spryker\Shared\ZedRequest\Client\Exception\RequestException;
use Spryker\Shared\ZedRequest\Client\ResponseInterface as ZedResponse;
use Spryker\Zed\ZedRequest\Business\Client\Request;

abstract class AbstractHttpClient implements HttpClientInterface
{

    const META_TRANSFER_ERROR =
        'Adding MetaTransfer failed. Either name missing/invalid or no object of TransferInterface provided.';

    const EVENT_FIELD_TRANSFER_DATA = 'transfer_data';

    const EVENT_FIELD_TRANSFER_CLASS = 'transfer_class';

    const EVENT_FIELD_PATH_INFO = 'path_info';

    const EVENT_FIELD_SUB_TYPE = 'sub_type';

    const EVENT_NAME_TRANSFER_REQUEST = 'transfer_request';

    const EVENT_NAME_TRANSFER_RESPONSE = 'transfer_response';

    /**
     * @var bool
     */
    protected static $alreadyRequested = false;

    /**
     * @var int
     */
    protected static $requestCounter = 0;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var int
     *
     * @todo Add ths timeout to config so this could be edited easily and from configuration level #894
     */
    protected static $timeoutInSeconds = 60;

    /**
     * @var AutoCompletion
     */
    protected $locator;

    /**
     * @var AuthClientInterface
     */
    protected $authClient;

    /**
     * @param AuthClientInterface $authClient
     * @param string $baseUrl
     */
    public function __construct(
        AuthClientInterface $authClient,
        $baseUrl
    ) {
        $this->authClient = $authClient;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param int $timeoutInSeconds
     *
     * @return void
     */
    public static function setDefaultTimeout($timeoutInSeconds)
    {
        self::$timeoutInSeconds = $timeoutInSeconds;
    }

    /**
     * @return array
     */
    abstract public function getHeaders();

    /**
     * @param string $pathInfo
     * @param TransferInterface $transferObject
     * @param array $metaTransfers
     * @param null $timeoutInSeconds
     * @param bool $isBackgroundRequest
     *
     * @throws \LogicException
     *
     * @return \Spryker\Shared\Library\Communication\Response
     */
    public function request(
        $pathInfo,
        TransferInterface $transferObject = null,
        array $metaTransfers = [],
        $timeoutInSeconds = null,
        $isBackgroundRequest = false
    ) {
        if (!$this->isRequestAllowed($isBackgroundRequest)) {
            throw new \LogicException('You cannot make more than one request from Yves to Zed.');
        }
        self::$requestCounter++;

        $requestTransfer = $this->createRequestTransfer($transferObject, $metaTransfers);
        $request = $this->createGuzzleRequest($pathInfo, $requestTransfer, $timeoutInSeconds);
        $this->logRequest($pathInfo, $requestTransfer, (string) $request->getBody());

        $this->forwardDebugSession($request);
        try {
            $response = $this->sendRequest($request);
        } catch (GuzzleRequestException $e) {
            $requestException = new RequestException($e->getMessage(), $e->getCode(), $e);
            $requestException->setExtra((string) $e->getRequest()->getResponse());

            throw $requestException;
        }
        $responseTransfer = $this->getTransferFromResponse($response);
        $this->logResponse($pathInfo, $responseTransfer, $response->getBody(true));

        return $responseTransfer;
    }

    /**
     * @param string $pathInfo
     *
     * @return bool
     */
    protected function isLoggingAllowed($pathInfo)
    {
        return strpos($pathInfo, 'heartbeat');
    }

    /**
     * @param bool $isBackgroundRequest
     *
     * @return bool
     */
    protected function isRequestAllowed($isBackgroundRequest)
    {
        if (!$isBackgroundRequest) {
            if (self::$alreadyRequested === true) {
                return false;
            }
            self::$alreadyRequested = true;
        }

        return true;
    }

    /**
     * @param string $pathInfo
     * @param RequestInterface $requestTransfer
     * @param null $timeoutInSeconds
     *
     * @return EntityEnclosingRequest
     */
    protected function createGuzzleRequest($pathInfo, RequestInterface $requestTransfer, $timeoutInSeconds = null)
    {
        $client = new Client(
            $this->baseUrl,
            [
                Client::REQUEST_OPTIONS => [
                    'timeout' => ($timeoutInSeconds ?: self::$timeoutInSeconds),
                    'connect_timeout' => 1.5,
                ],
            ]
        );

        $char = (strpos($pathInfo, '?') === false) ? '?' : ' &';
        /*
         * @todo CD-417
         */
        $eventJournal = new SharedEventJournal();
        $event = new Event();
        $eventJournal->applyCollectors($event);
        $requestId = $event->getFields()['request_id'];
        $pathInfo .= $char . 'yvesRequestId=' . $requestId;

        $client->setUserAgent('Yves 2.0');
        /** @var EntityEnclosingRequest $request */
        $request = $client->post($pathInfo);
        $request->addHeader('X-Yves-Host', 1);
        foreach ($this->getHeaders() as $header => $value) {
            $request->addHeader($header, $value);
        }

        $data = $requestTransfer->toArray(true);
//        unset($data['transfer']);
        $rawRequestBody = json_encode($data);
        $request->setBody($rawRequestBody, 'application/json');
//        $request->setHeader('Host', System::getHostname());

        return $request;
    }

    /**
     * @param TransferInterface $transferObject
     * @param array $metaTransfers
     *
     * @throws \LogicException
     *
     * @return AbstractRequest
     */
    protected function createRequestTransfer(TransferInterface $transferObject, array $metaTransfers)
    {
        $request = $this->getClientRequest();
        $request->setSessionId(session_id());
        $request->setTime(time());
        $request->setHost(System::getHostname() ?: 'n/a');

        foreach ($metaTransfers as $name => $metaTransfer) {
            if (!is_string($name) || is_numeric($name) || !$metaTransfer instanceof TransferInterface) {
                throw new \LogicException(self::META_TRANSFER_ERROR);
            }
            $request->addMetaTransfer($name, $metaTransfer);
        }

        if (!empty($transferObject)) {
            $request->setTransfer($transferObject);
        }

        return $request;
    }

    /**
     * @param EntityEnclosingRequest $request
     *
     * @throws InvalidZedResponseException
     *
     * @return Response
     */
    protected function sendRequest(EntityEnclosingRequest $request)
    {
        $response = $request->send();
        if (!$response || !$response->isSuccessful() || !$response->getBody()->getSize()) {
            throw new InvalidZedResponseException('empty', $response);
        }

        return $response;
    }

    /**
     * @param Response $response
     *
     * @throws InvalidZedResponseException
     *
     * @return ZedResponse
     */
    protected function getTransferFromResponse(Response $response)
    {
        $data = json_decode(trim($response->getBody(true)), true);
        if (empty($data) || !is_array($data)) {
            throw new InvalidZedResponseException('no valid JSON', $response);
        }
        $responseTransfer = new ClientResponse();
        $responseTransfer->fromArray($data);

        return $responseTransfer;
    }

    /**
     * @param string $pathInfo
     * @param RequestInterface $requestTransfer
     * @param string $rawBody
     *
     * @return void
     */
    protected function logRequest($pathInfo, RequestInterface $requestTransfer, $rawBody)
    {
        $this->doLog($pathInfo, self::EVENT_NAME_TRANSFER_REQUEST, $requestTransfer, $rawBody);
    }

    /**
     * @param string $pathInfo
     * @param ZedResponse $responseTransfer
     * @param string $rawBody
     *
     * @return void
     */
    protected function logResponse($pathInfo, ZedResponse $responseTransfer, $rawBody)
    {
        $this->doLog($pathInfo, self::EVENT_NAME_TRANSFER_RESPONSE, $responseTransfer, $rawBody);
    }

    /**
     * @param string $pathInfo
     * @param string $subType
     * @param ObjectInterface $transfer
     * @param string $rawBody
     *
     * @return void
     */
    protected function doLog($pathInfo, $subType, ObjectInterface $transfer, $rawBody)
    {
        $lumberjack = new SharedEventJournal();
        $event = new Event();
        $responseTransfer = $transfer->getTransfer();
        if ($responseTransfer instanceof TransferInterface) {
            $event->addField(self::EVENT_FIELD_TRANSFER_DATA, $responseTransfer->toArray());
            $event->addField(self::EVENT_FIELD_TRANSFER_CLASS, get_class($responseTransfer));
        } else {
            $event->addField(self::EVENT_FIELD_TRANSFER_DATA, null);
            $event->addField(self::EVENT_FIELD_TRANSFER_CLASS, null);
        }

        $event->addField(Event::FIELD_NAME, 'transfer');
        $event->addField(self::EVENT_FIELD_PATH_INFO, $pathInfo);
        $event->addField(self::EVENT_FIELD_SUB_TYPE, $subType);
        $lumberjack->saveEvent($event);
    }

    /**
     * Used for debug output
     *
     * @return int
     */
    public static function getRequestCounter()
    {
        return self::$requestCounter;
    }

    /**
     * @param EntityEnclosingRequest $request
     *
     * @return void
     */
    protected function forwardDebugSession(EntityEnclosingRequest $request)
    {
        if (Config::get(ApplicationConstants::TRANSFER_DEBUG_SESSION_FORWARD_ENABLED)) {
            if (isset($_COOKIE[Config::get(ApplicationConstants::TRANSFER_DEBUG_SESSION_NAME)])) {
                $cookie = new Cookie();
                $cookie->setName(trim(Config::get(ApplicationConstants::TRANSFER_DEBUG_SESSION_NAME)));
                $cookie->setValue($_COOKIE[Config::get(ApplicationConstants::TRANSFER_DEBUG_SESSION_NAME)]);
                $cookie->setDomain(Config::get(ApplicationConstants::HOST_ZED_API));
                $cookieArray = new ArrayCookieJar(true);
                $cookieArray->add($cookie);

                $request->addSubscriber(new CookiePlugin($cookieArray));
            }
        }
    }

    /**
     * @return Request
     */
    private function getClientRequest()
    {
        return new Request();
    }

}
