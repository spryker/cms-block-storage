<?php

namespace SprykerFeature\Zed\Cms\Dependency\Facade;

use SprykerEngine\Zed\Locale\Business\Exception\MissingLocaleException;
use Generated\Shared\Transfer\TranslationTransfer;
use SprykerFeature\Zed\Glossary\Business\Exception\KeyExistsException;
use SprykerFeature\Zed\Glossary\Business\Exception\MissingKeyException;
use SprykerFeature\Zed\Glossary\Business\Exception\MissingTranslationException;
use SprykerFeature\Zed\Glossary\Business\Exception\TranslationExistsException;

interface CmsToGlossaryInterface
{
    /**
     * @param int $idKey
     * @param array $data
     *
     * @return string
     * @throws MissingTranslationException
     */
    public function translateByKeyId($idKey, array $data = []);

    /**
     * @param string $keyName
     * @param string $value
     * @param bool $isActive
     *
     * @return TranslationTransfer
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     */
    public function createTranslationForCurrentLocale($keyName, $value, $isActive = true);

    /**
     * @param string $keyName
     *
     * @return int
     * @throws KeyExistsException
     */
    public function createKey($keyName);

    /**
     * @param string $keyName
     *
     * @return bool
     */
    public function hasKey($keyName);

    /**
     * @param int $idKey
     */
    public function touchCurrentTranslationForKeyId($idKey);
}
