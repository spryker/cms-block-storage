<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Tax\Communication\Table;

use Orm\Zed\Tax\Persistence\Map\SpyTaxRateTableMap;
use Orm\Zed\Tax\Persistence\Map\SpyTaxSetTableMap;
use Orm\Zed\Tax\Persistence\SpyTaxSet;
use Orm\Zed\Tax\Persistence\SpyTaxSetQuery;
use Spryker\Shared\Library\DateFormatterInterface;
use Spryker\Shared\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class SetTable extends AbstractTable
{
    const TABLE_COL_ACTIONS = 'Actions';
    const URL_PARAM_ID_TAX_SET = 'id-tax-set';

    /**
     * @var \Orm\Zed\Tax\Persistence\SpyTaxSetQuery
     */
    protected $taxSetQuery;

    /**
     * @var \Spryker\Shared\Library\DateFormatterInterface
     */
    protected $dateFormatter;

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxSetQuery $taxSetQuery
     * @param \Spryker\Shared\Library\DateFormatterInterface $dateFormatter
     */
    public function __construct(SpyTaxSetQuery $taxSetQuery, DateFormatterInterface $dateFormatter)
    {
        $this->taxSetQuery = $taxSetQuery;
        $this->dateFormatter = $dateFormatter;
    }


    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return mixed
     */
    protected function configure(TableConfiguration $config)
    {
        $url = Url::generate('listTable')->build();

        $config->setUrl($url);
        $config->setHeader([
            SpyTaxSetTableMap::COL_ID_TAX_SET => 'ID',
            SpyTaxSetTableMap::COL_NAME => 'Name',
            SpyTaxSetTableMap::COL_CREATED_AT => 'Created At',
            self::TABLE_COL_ACTIONS => 'Actions'
        ]);

        $config->setSearchable([
            SpyTaxSetTableMap::COL_NAME,
        ]);

        $config->setSortable([
            SpyTaxSetTableMap::COL_ID_TAX_SET,
            SpyTaxSetTableMap::COL_NAME,
            SpyTaxSetTableMap::COL_CREATED_AT,
        ]);

        $config->setDefaultSortColumnIndex(0);
        $config->setDefaultSortDirection(TableConfiguration::SORT_DESC);
        $config->addRawColumn(self::TABLE_COL_ACTIONS);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return mixed
     */
    protected function prepareData(TableConfiguration $config)
    {
        $result = [];


        $queryResult = $this->runQuery($this->taxSetQuery, $config, true);

        /** @var \Orm\Zed\Tax\Persistence\SpyTaxSet $taxSetEntity */
        foreach ($queryResult as $taxSetEntity) {
            $result[] = [
                SpyTaxSetTableMap::COL_ID_TAX_SET => $taxSetEntity->getIdTaxSet(),
                SpyTaxSetTableMap::COL_NAME => $taxSetEntity->getName(),
                SpyTaxSetTableMap::COL_CREATED_AT => $this->dateFormatter->dateTime($taxSetEntity->getCreatedAt()),
                self::TABLE_COL_ACTIONS => $this->getActionButtons($taxSetEntity),
            ];
        }
        return $result;
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxSet $taxSetEntity
     *
     * @return string
     */
    protected function getActionButtons(SpyTaxSet $taxSetEntity)
    {
        $buttons = [];
        $buttons[] = $this->createViewButton($taxSetEntity);
        $buttons[] = $this->createEditButton($taxSetEntity);
        $buttons[] = $this->createDeleteButton($taxSetEntity);

        return implode(' ', $buttons);
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxSet $taxRateEntity
     *
     * @return string
     */
    protected function createEditButton(SpyTaxSet $taxRateEntity)
    {
        $editTaxSetUrl = Url::generate(
            '/tax/set/edit',
            [
                self::URL_PARAM_ID_TAX_SET => $taxRateEntity->getIdTaxSet()
            ]
        );
        return $this->generateEditButton($editTaxSetUrl, 'Edit');
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxSet $taxSetEntity
     *
     * @return string
     *
     */
    protected function createViewButton(SpyTaxSet $taxSetEntity)
    {
        $viewTaxSetUrl = Url::generate(
            '/tax/set/view',
            [
                self::URL_PARAM_ID_TAX_SET => $taxSetEntity->getIdTaxSet()
            ]
        );
        return $this->generateViewButton($viewTaxSetUrl, 'view');
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxSet $taxSetEntity
     *
     * @return string
     */
    protected function createDeleteButton(SpyTaxSet $taxSetEntity)
    {
        $deleteTaxSetUrl = Url::generate(
            '/tax/set/delete',
            [
                self::URL_PARAM_ID_TAX_SET => $taxSetEntity->getIdTaxSet()
            ]
        );

        return $this->generateRemoveButton($deleteTaxSetUrl, 'delete');
    }
}
