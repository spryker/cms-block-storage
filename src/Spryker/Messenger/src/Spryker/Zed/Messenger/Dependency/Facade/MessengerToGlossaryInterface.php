<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Messenger\Dependency\Facade;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Glossary\Business\Exception\MissingTranslationException;

interface MessengerToGlossaryInterface
{

    /**
     * @param string $keyName
     * @param LocaleTransfer|null $locale
     *
     * @return bool
     */
    public function hasTranslation($keyName, LocaleTransfer $locale = null);

    /**
     * @param string $keyName
     * @param array $data
     *
     * @throws MissingTranslationException
     *
     * @return string
     */
    public function translate($keyName, array $data = []);
}
