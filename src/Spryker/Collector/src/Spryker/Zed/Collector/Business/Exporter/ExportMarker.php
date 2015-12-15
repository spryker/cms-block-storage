<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Collector\Business\Exporter;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Shared\Collector\Code\KeyBuilder\KeyBuilderInterface;
use Spryker\Zed\Collector\Business\Exporter\Reader\ReaderInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\WriterInterface;

class ExportMarker implements MarkerInterface
{

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var KeyBuilderInterface
     */
    private $keyBuilder;

    /**
     * @param WriterInterface $writer
     * @param ReaderInterface $reader
     * @param KeyBuilderInterface $keyBuilder
     */
    public function __construct(
        WriterInterface $writer,
        ReaderInterface $reader,
        KeyBuilderInterface $keyBuilder
    ) {
        $this->writer = $writer;
        $this->reader = $reader;
        $this->keyBuilder = $keyBuilder;
    }

    /**
     * @param string $exportType
     * @param LocaleTransfer $locale
     *
     * @return \DateTime
     */
    public function getLastExportMarkByTypeAndLocale($exportType, LocaleTransfer $locale)
    {
        $lastTimeStamp = $this->reader->read($this->keyBuilder->generateKey($exportType, $locale->getLocaleName()), $exportType);

        if ($lastTimeStamp) {
            $lastDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $lastTimeStamp);
        } else {
            $lastDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00');
        }

        return $lastDateTime;
    }

    /**
     * @param string $exportType
     * @param LocaleTransfer $locale
     * @param string $timestamp
     *
     * @return void
     */
    public function setLastExportMarkByTypeAndLocale($exportType, LocaleTransfer $locale, $timestamp)
    {
        $timestampKey = $this->keyBuilder->generateKey($exportType, $locale->getLocaleName());
        $this->writer->write([$timestampKey => $timestamp], $exportType);
    }

    /**
     * @param array $keys
     *
     * @return bool
     */
    public function deleteTimestamps(array $keys)
    {
        return $this->writer->delete($keys);
    }

}
