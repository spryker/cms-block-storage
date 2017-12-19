<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsBlockProductStorage\Communication\Plugin\Event\Listener;

use Generated\Shared\Transfer\CmsBlockProductTransfer;
use Orm\Zed\CmsBlockProductStorage\Persistence\SpyCmsBlockProductStorage;
use Spryker\Zed\CmsBlockProductStorage\Communication\CmsBlockProductStorageCommunicationFactory;
use Spryker\Zed\CmsBlockProductStorage\Persistence\CmsBlockProductStorageQueryContainerInterface;
use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class AbstractCmsBlockProductStorageListener
 *
 * @method CmsBlockProductStorageCommunicationFactory getFactory()
 * @method CmsBlockProductStorageQueryContainerInterface getQueryContainer()
 */
abstract class AbstractCmsBlockProductStorageListener extends AbstractPlugin implements EventBulkHandlerInterface
{

    /**
     * @param array $productAbstractIds
     *
     * @return void
     */
    protected function publish(array $productAbstractIds)
    {
        $cmsBlockProductsTransfer = $this->getCmsBlockProductsTransfer($productAbstractIds);
        $spyCmsBlockProductStorageEntities = $this->findCmsBlockProductStorageEntitiesByProductIds($productAbstractIds);
        $this->storeData($cmsBlockProductsTransfer, $spyCmsBlockProductStorageEntities);
    }

    /**
     * @param array $productAbstractIds
     *
     * @return void
     */
    protected function refreshOrUnpublish(array $productAbstractIds)
    {
        $cmsBlockProductsTransfer = $this->getCmsBlockProductsTransfer($productAbstractIds);
        $spyCmsBlockProductStorageEntities = $this->findCmsBlockProductStorageEntitiesByProductIds($productAbstractIds);

        foreach ($spyCmsBlockProductStorageEntities as $spyCmsBlockProductStorageEntity) {
            if (isset($cmsBlockProductsTransfer[$spyCmsBlockProductStorageEntity->getFkProductAbstract()])) {
                $this->storeData($cmsBlockProductsTransfer, $spyCmsBlockProductStorageEntities);

                continue;
            }

            $spyCmsBlockProductStorageEntity->delete();
        }
    }

    /**
     * @param CmsBlockProductTransfer[] $cmsBlockProductsTransfer
     * @param array $spyCmsBlockProductStorageEntities
     *
     * @return void
     */
    protected function storeData(array $cmsBlockProductsTransfer, array $spyCmsBlockProductStorageEntities)
    {
        foreach ($cmsBlockProductsTransfer as $cmsBlockProductTransfer) {
            if (isset($spyCmsBlockProductStorageEntities[$cmsBlockProductTransfer->getIdProductAbstract()])) {
                $this->storeDataSet($cmsBlockProductTransfer, $spyCmsBlockProductStorageEntities[$cmsBlockProductTransfer->getIdProductAbstract()]);
            } else {
                $this->storeDataSet($cmsBlockProductTransfer);
            }
        }
    }

    /**
     * @param CmsBlockProductTransfer $cmsBlockProductsTransfer
     * @param SpyCmsBlockProductStorage|null $spyCmsBlockProductStorage
     *
     * @return void
     */
    protected function storeDataSet(CmsBlockProductTransfer $cmsBlockProductsTransfer, SpyCmsBlockProductStorage $spyCmsBlockProductStorage = null)
    {
        if ($spyCmsBlockProductStorage === null) {
            $spyCmsBlockProductStorage = new SpyCmsBlockProductStorage();
        }

        $data = $this->getFactory()->getUtilSanitizeService()->arrayFilterRecursive($cmsBlockProductsTransfer->toArray());
        $spyCmsBlockProductStorage->setFkProductAbstract($cmsBlockProductsTransfer->getIdProductAbstract());
        $spyCmsBlockProductStorage->setData($data);
        $spyCmsBlockProductStorage->save();
    }

    /**
     * @param array $productAbstractIds
     *
     * @return array
     */
    protected function getCmsBlockProductsTransfer(array $productAbstractIds)
    {
        $mappedCmsBlockProducts = $this->getCmsBlockProducts($productAbstractIds);

        $cmsBlockProductsTransfer = [];
        foreach ($mappedCmsBlockProducts as $productAbstractId => $cmsBlockProduct) {
            $cmsBlockProductTransfer = new CmsBlockProductTransfer();
            $cmsBlockProductTransfer->setIdProductAbstract($productAbstractId);
            $cmsBlockProductTransfer->setBlockNames($cmsBlockProduct);
            $cmsBlockProductsTransfer[$productAbstractId] = $cmsBlockProductTransfer;
        }

        return $cmsBlockProductsTransfer;
    }

    /**
     * @param array $productAbstractIds
     *
     * @return array
     */
    protected function getCmsBlockProducts(array $productAbstractIds)
    {
        $cmsBlockProducts = $this->getQueryContainer()
            ->queryCmsBlockProducts($productAbstractIds)
            ->find();

        $mappedCmsBlockProducts = [];
        foreach ($cmsBlockProducts as $cmsBlockProduct) {
            $mappedCmsBlockProducts[$cmsBlockProduct->getFkProductAbstract()][] = $cmsBlockProduct->getName();
        }

        return $mappedCmsBlockProducts;
    }

    /**
     * @param array $productAbstractIds
     *
     * @return array
     */
    protected function findCmsBlockProductStorageEntitiesByProductIds(array $productAbstractIds)
    {
        $cmsBlockProductStorageEntities = $this->getQueryContainer()->queryCmsBlockProductStorageByIds($productAbstractIds)->find();
        $cmsBlockProductStorageEntitiesById = [];
        foreach ($cmsBlockProductStorageEntities as $cmsBlockProductStorageEntity) {
            $cmsBlockProductStorageEntitiesById[$cmsBlockProductStorageEntity->getFkProductAbstract()] = $cmsBlockProductStorageEntity;
        }

        return $cmsBlockProductStorageEntitiesById;
    }
}
