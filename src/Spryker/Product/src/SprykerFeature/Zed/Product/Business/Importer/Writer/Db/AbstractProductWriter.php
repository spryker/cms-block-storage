<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Product\Business\Importer\Writer\Db;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\AbstractProductTransfer;
use Propel\Runtime\Propel;
use Spryker\Zed\Product\Business\Importer\Writer\AbstractProductWriterInterface;
use Orm\Zed\Product\Persistence\Map\SpyAbstractProductTableMap;
use Orm\Zed\Product\Persistence\Map\SpyLocalizedAbstractProductAttributesTableMap;

class AbstractProductWriter implements AbstractProductWriterInterface
{

    /**
     * @var \PDOStatement
     */
    protected $productStatement;

    /**
     * @var \PDOStatement
     */
    protected $attributesStatement;

    /**
     * @var string
     */
    protected $localeTransfer;

    /**
     * @param LocaleTransfer $localeTransfer
     */
    public function __construct(LocaleTransfer $localeTransfer)
    {
        $this->localeTransfer = $localeTransfer;
        $this->createProductStatement();
        $this->createAttributeStatement();
    }

    /**
     * @param AbstractProductTransfer $product
     *
     * @return bool
     */
    public function writeAbstractProduct(AbstractProductTransfer $product)
    {
        $this->productStatement->execute(
            [
                ':sku' => $product->getSku(),
                ':attributes' => json_encode($product->getAttributes()),
            ]
        );

        foreach ($product->getLocalizedAttributes() as $localizedAttributes) {
            $this->attributesStatement->execute(
                [
                    ':attributes' => json_encode($localizedAttributes->getAttributes()),
                    ':name' => $localizedAttributes->getName(),
                    ':abstractProductSku' => $product->getSku(),
                    ':fkLocale' => $this->localeTransfer->getIdLocale(),
                ]
            );
        }

        return true;
    }

    /**
     * @return void
     */
    protected function createProductStatement()
    {
        $connection = Propel::getConnection();
        $this->productStatement = $connection->prepare(
            sprintf(
                'INSERT INTO %1$s (%2$s, %3$s) VALUES (:sku, :attributes)
                ON DUPLICATE KEY UPDATE
                 %2$s=VALUES(%2$s),
                 %3$s=VALUES(%3$s);',
                SpyAbstractProductTableMap::TABLE_NAME,
                SpyAbstractProductTableMap::COL_SKU,
                SpyAbstractProductTableMap::COL_ATTRIBUTES
            )
        );
    }

    /**
     * @return void
     */
    protected function createAttributeStatement()
    {
        $connection = Propel::getConnection();
        $this->attributesStatement = $connection->prepare(
            sprintf(
                'INSERT INTO %1$s (%2$s, %3$s, %4$s, %5$s) VALUES(
                    (SELECT %6$s FROM %7$s WHERE %8$s = :abstractProductSku),
                    :fkLocale,
                    :name,
                    :attributes
                ) ON DUPLICATE KEY UPDATE
                    %2$s=VALUES(%2$s),
                    %3$s=VALUES(%3$s),
                    %4$s=VALUES(%4$s),
                    %5$s=VALUES(%5$s);',
                SpyLocalizedAbstractProductAttributesTableMap::TABLE_NAME,
                SpyLocalizedAbstractProductAttributesTableMap::COL_FK_ABSTRACT_PRODUCT,
                SpyLocalizedAbstractProductAttributesTableMap::COL_FK_LOCALE,
                SpyLocalizedAbstractProductAttributesTableMap::COL_NAME,
                SpyLocalizedAbstractProductAttributesTableMap::COL_ATTRIBUTES,
                SpyAbstractProductTableMap::COL_ID_ABSTRACT_PRODUCT,
                SpyAbstractProductTableMap::TABLE_NAME,
                SpyAbstractProductTableMap::COL_SKU
            )
        );
    }

}
