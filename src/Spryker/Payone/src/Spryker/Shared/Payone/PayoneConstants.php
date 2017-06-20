<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Payone;

interface PayoneConstants
{

    const PAYONE = 'PAYONE';
    const PAYONE_CREDENTIALS = 'PAYONE_CREDENTIALS';
    const PAYONE_CREDENTIALS_ENCODING = 'PAYONE_CREDENTIALS_ENCODING';
    const PAYONE_PAYMENT_GATEWAY_URL = 'PAYONE_PAYMENT_GATEWAY_URL';
    const PAYONE_CREDENTIALS_KEY = 'PAYONE_CREDENTIALS_KEY';
    const PAYONE_CREDENTIALS_MID = 'PAYONE_CREDENTIALS_MID';
    const PAYONE_CREDENTIALS_AID = 'PAYONE_CREDENTIALS_AID';
    const PAYONE_CREDENTIALS_PORTAL_ID = 'PAYONE_CREDENTIALS_PORTAL_ID';
    const PAYONE_REDIRECT_SUCCESS_URL = 'PAYONE_REDIRECT_SUCCESS_URL';
    const PAYONE_REDIRECT_ERROR_URL = 'PAYONE_REDIRECT_ERROR_URL';
    const PAYONE_REDIRECT_BACK_URL = 'PAYONE_REDIRECT_BACK_URL';
    const PAYONE_EMPTY_SEQUENCE_NUMBER = 'PAYONE_EMPTY_SEQUENCE_NUMBER';

    const PAYONE_TXACTION_APPOINTED = 'appointed';

    const PAYONE_MODE = 'MODE';
    const PAYONE_MODE_TEST = 'test';
    const PAYONE_MODE_LIVE = 'live';

    /** @deprecated Please use PayoneConstants::BASE_URL_YVES instead */
    const HOST_YVES = 'HOST_YVES';

    /**
     * Base url for Yves including scheme and port (e.g. http://www.de.demoshop.local:8080)
     *
     * @api
     */
    const BASE_URL_YVES = 'PAYONE:BASE_URL_YVES';

}
