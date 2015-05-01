<?php

namespace SprykerFeature\Zed\SearchPage\Business\Reader;

use SprykerFeature\Zed\SearchPage\Persistence\Propel\SpySearchDocumentAttribute;

interface DocumentAttributeReaderInterface
{

    /**
     * @param string $name
     * @param string $type
     *
     * @return bool
     */
    public function hasDocumentAttributeByNameAndType($name, $type);

    /**
     * @param $idDocumentAttribute
     *
     * @return SpySearchDocumentAttribute
     */
    public function getDocumentAttributeById($idDocumentAttribute);

    /**
     * @return bool
     */
    public function hasDocumentAttributes();
}
