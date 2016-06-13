<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Collector\Business;

use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Spryker\Zed\Collector\Business\Collector\DatabaseCollectorInterface;
use Spryker\Zed\Collector\Business\Exporter\Reader\ReaderInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\TouchUpdaterInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\WriterInterface;
use Spryker\Zed\Collector\Business\Model\BatchResultInterface;
use Spryker\Zed\Messenger\Business\Model\MessengerInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CollectorFacadeInterface
{

    /**
     * @api
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Spryker\Zed\Collector\Business\Model\BatchResultInterface[]
     */
    public function exportStorage(OutputInterface $output);

    /**
     * @api
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Spryker\Zed\Collector\Business\Model\BatchResultInterface[]
     */
    public function exportFile(OutputInterface $output);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Spryker\Zed\Collector\Business\Model\BatchResultInterface[]
     */
    public function exportStorageByLocale(LocaleTransfer $locale, OutputInterface $output);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Spryker\Zed\Collector\Business\Model\BatchResultInterface[]
     */
    public function exportSearchByLocale(LocaleTransfer $locale, OutputInterface $output);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     *
     * @return \Spryker\Zed\Collector\Business\Model\BatchResultInterface[]
     */
    public function updateSearchByLocale(LocaleTransfer $locale, OutputInterface $output);

    /**
     * @api
     *
     * @param \Spryker\Zed\Messenger\Business\Model\MessengerInterface $messenger
     *
     * @return void
     */
    public function install(MessengerInterface $messenger);

    /**
     * @api
     *
     * @return string
     */
    public function getSearchIndexName();

    /**
     * @api
     *
     * @return string
     */
    public function getSearchDocumentType();

    /**
     * @api
     *
     * @param string $type
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     *
     * @return string
     */
    public function getCsvFileName($type, LocaleTransfer $locale);

    /**
     * @api
     *
     * @param array $keys
     *
     * @return bool
     */
    public function deleteSearchTimestamps(array $keys = []);

    /**
     * @api
     *
     * @param array $keys
     *
     * @return bool
     */
    public function deleteStorageTimestamps(array $keys = []);

    /**
     * Specification:
     * - Runs collectors defined in the project and synchronizes data with stores (add, update, delete)
     *
     * @api
     *
     * @param \Spryker\Zed\Collector\Business\Collector\DatabaseCollectorInterface $collector
     * @param \Orm\Zed\Touch\Persistence\SpyTouchQuery $baseQuery
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Spryker\Zed\Collector\Business\Model\BatchResultInterface $result
     * @param \Spryker\Zed\Collector\Business\Exporter\Reader\ReaderInterface $dataReader
     * @param \Spryker\Zed\Collector\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Spryker\Zed\Collector\Business\Exporter\Writer\TouchUpdaterInterface $touchUpdater
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function runCollector(
        DatabaseCollectorInterface $collector,
        SpyTouchQuery $baseQuery,
        LocaleTransfer $locale,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @api
     *
     * @return array
     */
    public function getAllCollectorTypes();

    /**
     * @api
     *
     * @return array
     */
    public function getEnabledCollectorTypes();

}
