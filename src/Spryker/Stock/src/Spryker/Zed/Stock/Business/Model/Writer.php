<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Stock\Business\Model;

use Generated\Shared\Transfer\StockProductTransfer;
use Generated\Shared\Transfer\TypeTransfer;
use Spryker\Zed\Stock\Business\Exception\StockProductAlreadyExistsException;
use Spryker\Zed\Stock\Business\Exception\StockProductNotFoundException;
use Spryker\Zed\Stock\Business\Exception\StockTypeNotFoundException;
use Spryker\Zed\Stock\Dependency\Facade\StockToTouchInterface;
use Orm\Zed\Stock\Persistence\SpyStock;
use Orm\Zed\Stock\Persistence\SpyStockProduct;
use Spryker\Zed\Stock\Persistence\StockQueryContainer;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;

class Writer implements WriterInterface
{

    const TOUCH_STOCK_TYPE = 'stock-type';
    const TOUCH_STOCK_PRODUCT = 'stock-product';
    const ERROR_STOCK_TYPE_UNKNOWN = 'stock type unknown';

    /**
     * @var StockQueryContainer
     */
    protected $queryContainer;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var StockToTouchInterface
     */
    protected $touchFacade;

    /**
     * @param StockQueryContainer $queryContainer
     * @param ReaderInterface $readerInterface
     * @param StockToTouchInterface $touchFacade
     */
    public function __construct(
        StockQueryContainer $queryContainer,
        ReaderInterface $readerInterface,
        StockToTouchInterface $touchFacade
    ) {
        $this->queryContainer = $queryContainer;
        $this->reader = $readerInterface;
        $this->touchFacade = $touchFacade;
    }

    /**
     * @param TypeTransfer $stockTypeTransfer
     *
     * @throws PropelException
     *
     * @return int
     */
    public function createStockType(TypeTransfer $stockTypeTransfer)
    {
        Propel::getConnection()->beginTransaction();
        $stockEntity = new SpyStock();
        $stockEntity
            ->setName($stockTypeTransfer->getName())
            ->save();
        $this->insertActiveTouchRecordStockType($stockEntity);
        Propel::getConnection()->commit();

        return $stockEntity->getPrimaryKey();
    }

    /**
     * @param TypeTransfer $stockTypeTransfer
     *
     * @throws PropelException
     * @throws StockTypeNotFoundException
     *
     * @return int
     */
    public function updateStockType(TypeTransfer $stockTypeTransfer)
    {
        Propel::getConnection()->beginTransaction();
        $stockTypeEntity = $this->queryContainer
            ->queryStockByName($stockTypeTransfer->getName())
            ->findOne();
        if ($stockTypeEntity === null) {
            throw new StockTypeNotFoundException();
        }
        $stockTypeEntity->setName($stockTypeTransfer->getName());
        $stockTypeEntity->save();

        Propel::getConnection()->commit();

        return $stockTypeEntity->getIdStock();
    }

    /**
     * @param StockProductTransfer $transferStockProduct
     *
     * @throws StockProductAlreadyExistsException
     *
     * @return int
     */
    public function createStockProduct(StockProductTransfer $transferStockProduct)
    {
        Propel::getConnection()->beginTransaction();

        $idStockType = $this->reader->getStockTypeIdByName($transferStockProduct->getStockType());
        $idProduct = $this->reader->getConcreteProductIdBySku($transferStockProduct->getSku());
        $this->reader->checkStockDoesNotExist($idStockType, $idProduct);
        $idStockProduct = $this->saveStockProduct($transferStockProduct, $idStockType, $idProduct);

        Propel::getConnection()->commit();

        return $idStockProduct;
    }

    /**
     * @param StockProductTransfer $transferStockProduct
     *
     * @throws PropelException
     * @throws StockProductNotFoundException
     *
     * @return int
     */
    public function updateStockProduct(StockProductTransfer $transferStockProduct)
    {
        Propel::getConnection()->beginTransaction();

        $idProduct = $this->reader->getConcreteProductIdBySku($transferStockProduct->getSku());
        $idStock = $this->reader->getStockTypeIdByName($transferStockProduct->getStockType());
        $stockProductEntity = $this->reader->getStockProductById($transferStockProduct->getIdStockProduct());

        $stockProductEntity
            ->setFkStock($idStock)
            ->setFkProduct($idProduct)
            ->setQuantity($transferStockProduct->getQuantity())
            ->setIsNeverOutOfStock($transferStockProduct->getIsNeverOutOfStock())
            ->save();
        $this->insertActiveTouchRecordStockProduct($stockProductEntity);

        Propel::getConnection()->commit();

        return $stockProductEntity->getPrimaryKey();
    }

    /**
     * @param string $sku
     * @param int $decrementBy
     * @param string $stockType
     *
     * @return void
     */
    public function decrementStock($sku, $stockType, $decrementBy = 1)
    {
        Propel::getConnection()->beginTransaction();
        $idProduct = $this->reader->getConcreteProductIdBySku($sku);
        $idStock = $this->reader->getStockTypeIdByName($stockType);
        $stockProductEntity = $this->queryContainer->queryStockProductByStockAndProduct(
            $idStock,
            $idProduct
        )->findOneOrCreate();

        $stockProductEntity->decrement($decrementBy);
        $this->insertActiveTouchRecordStockProduct($stockProductEntity);
        Propel::getConnection()->commit();
    }

    /**
     * @param string $sku
     * @param int $incrementBy
     * @param string $stockType
     *
     * @return void
     */
    public function incrementStock($sku, $stockType, $incrementBy = 1)
    {
        Propel::getConnection()->beginTransaction();
        $idProduct = $this->reader->getConcreteProductIdBySku($sku);
        $idStock = $this->reader->getStockTypeIdByName($stockType);
        $stockProductEntity = $this->queryContainer->queryStockProductByStockAndProduct(
            $idStock,
            $idProduct
        )->findOneOrCreate();

        $stockProductEntity->increment($incrementBy);
        $this->insertActiveTouchRecordStockProduct($stockProductEntity);
        Propel::getConnection()->commit();
    }

    /**
     * @param SpyStock $stockTypeEntity
     *
     * @return void
     */
    protected function insertActiveTouchRecordStockType(SpyStock $stockTypeEntity)
    {
        $this->touchFacade->touchActive(
            self::TOUCH_STOCK_TYPE,
            $stockTypeEntity->getIdStock()
        );
    }

    /**
     * @param SpyStockProduct $stockProductEntity
     *
     * @return void
     */
    protected function insertActiveTouchRecordStockProduct(SpyStockProduct $stockProductEntity)
    {
        $this->touchFacade->touchActive(
            self::TOUCH_STOCK_PRODUCT,
            $stockProductEntity->getIdStockProduct()
        );
    }

    /**
     * @param StockProductTransfer $transferStockProduct
     * @param int $idStockType
     * @param int $idProduct
     *
     * @throws PropelException
     *
     * @return int
     */
    protected function saveStockProduct(StockProductTransfer $transferStockProduct, $idStockType, $idProduct)
    {
        $stockProduct = new SpyStockProduct();
        $stockProduct->setFkProduct($idProduct)
            ->setFkStock($idStockType)
            ->setIsNeverOutOfStock($transferStockProduct->getIsNeverOutOfStock())
            ->setQuantity($transferStockProduct->getQuantity())
            ->save();
        $this->insertActiveTouchRecordStockProduct($stockProduct);

        return $stockProduct->getPrimaryKey();
    }

}
