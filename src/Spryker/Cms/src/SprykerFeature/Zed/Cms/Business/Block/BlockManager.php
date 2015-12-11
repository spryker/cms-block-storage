<?php

/**
 * (c) Spryker Systems GmbH copyright protected.
 */

namespace SprykerFeature\Zed\Cms\Business\Block;

use Generated\Shared\Transfer\CmsBlockTransfer;
use Propel\Runtime\Connection\ConnectionInterface;
use SprykerFeature\Shared\Cms\CmsConstants;
use SprykerFeature\Zed\Cms\Business\Exception\MissingPageException;
use SprykerFeature\Zed\Cms\Dependency\Facade\CmsToTouchInterface;
use SprykerFeature\Zed\Cms\Persistence\CmsQueryContainerInterface;
use Orm\Zed\Cms\Persistence\SpyCmsBlock;

class BlockManager implements BlockManagerInterface
{

    /**
     * @var CmsQueryContainerInterface
     */
    protected $cmsQueryContainer;

    /**
     * @var CmsToTouchInterface
     */
    protected $touchFacade;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @param CmsQueryContainerInterface $cmsQueryContainer
     * @param CmsToTouchInterface $touchFacade
     * @param ConnectionInterface $connection
     */
    public function __construct(CmsQueryContainerInterface $cmsQueryContainer, CmsToTouchInterface $touchFacade, ConnectionInterface $connection)
    {
        $this->cmsQueryContainer = $cmsQueryContainer;
        $this->touchFacade = $touchFacade;
        $this->connection = $connection;
    }

    /**
     * @param CmsBlockTransfer $cmsBlockTransfer
     *
     * @return CmsBlockTransfer
     */
    public function saveBlock(CmsBlockTransfer $cmsBlockTransfer)
    {
        $this->checkPageExists($cmsBlockTransfer->getFkPage());

        if ($this->getCmsBlockByIdPage($cmsBlockTransfer->getFkPage()) === null) {
            $block = $this->createBlock($cmsBlockTransfer);
        } else {
            $block = $this->updateBlock($cmsBlockTransfer);
        }

        return $this->convertBlockEntityToTransfer($block);
    }

    /**
     * @param CmsBlockTransfer $cmsBlockTransfer
     *
     * @return CmsBlockTransfer
     */
    public function saveBlockAndTouch(CmsBlockTransfer $cmsBlockTransfer)
    {
        $blockEntity = $this->getCmsBlockByIdPage($cmsBlockTransfer->getFkPage());
        $oldBlockEntity = null;

        if ($blockEntity !== null) {
            $oldBlockEntity = clone $blockEntity;
        }

        $blockTransfer = $this->saveBlock($cmsBlockTransfer);

        if ($oldBlockEntity !== null) {
            $this->touchKeyChangeNecessary($blockTransfer, $oldBlockEntity);
        } else {
            $this->touchBlockActive($blockTransfer);
        }

        return $blockTransfer;
    }

    /**
     * @param int $idCategoryNode
     *
     * @return void
     */
    public function updateBlocksAssignedToDeletedCategoryNode($idCategoryNode)
    {
        $this->connection->beginTransaction();

        $assignedBlocks = $this->getCmsBlocksByIdCategoryNode($idCategoryNode);

        foreach ($assignedBlocks as $idBlock => $blockTransfer) {
            //unique keys is on name, type and value therefore the name has to be changed
            $blockTransfer->setName(
                $blockTransfer->getName() . '_' . CmsConstants::RESOURCE_TYPE_CATEGORY_NODE . '_deleted_' . $blockTransfer->getIdCmsBlock()
            );
            $blockTransfer->setType(CmsConstants::RESOURCE_TYPE_STATIC);
            $blockTransfer->setValue(0);
            $this->saveBlockAndTouch($blockTransfer);
        }

        $this->connection->commit();
    }

    /**
     * @param SpyCmsBlock $blockEntity
     *
     * @return CmsBlockTransfer
     */
    public function convertBlockEntityToTransfer(SpyCmsBlock $blockEntity)
    {
        $blockTransfer = new CmsBlockTransfer();
        $blockTransfer->fromArray($blockEntity->toArray(), true);

        return $blockTransfer;
    }

    /**
     * @param CmsBlockTransfer $cmsBlockTransfer
     *
     * @return void
     */
    public function touchBlockActive(CmsBlockTransfer $cmsBlockTransfer)
    {
        $this->touchFacade->touchActive(CmsConstants::RESOURCE_TYPE_BLOCK, $cmsBlockTransfer->getIdCmsBlock());
    }

    /**
     * @param CmsBlockTransfer $cmsBlockTransfer
     *
     * @return void
     */
    public function touchBlockActiveWithKeyChange(CmsBlockTransfer $cmsBlockTransfer)
    {
        $this->touchFacade->touchActive(CmsConstants::RESOURCE_TYPE_BLOCK, $cmsBlockTransfer->getIdCmsBlock(), true);
    }

    /**
     * @param CmsBlockTransfer $cmsBlockTransfer
     *
     * @return void
     */
    public function touchBlockDelete(CmsBlockTransfer $cmsBlockTransfer)
    {
        $this->touchFacade->touchDeleted(CmsConstants::RESOURCE_TYPE_BLOCK, $cmsBlockTransfer->getIdCmsBlock());
    }

    /**
     * @param CmsBlockTransfer $cmsBlockTransfer
     *
     * @return SpyCmsBlock
     */
    protected function createBlock(CmsBlockTransfer $cmsBlockTransfer)
    {
        $blockEntity = new SpyCmsBlock();

        $blockEntity->fromArray($cmsBlockTransfer->toArray());
        $blockEntity->save();

        return $blockEntity;
    }

    /**
     * @param CmsBlockTransfer $cmsBlockTransfer
     *
     * @return SpyCmsBlock
     */
    protected function updateBlock(CmsBlockTransfer $cmsBlockTransfer)
    {
        $blockEntity = $this->getCmsBlockByIdPage($cmsBlockTransfer->getFkPage());
        $blockEntity->fromArray($cmsBlockTransfer->toArray());

        if (!$blockEntity->isModified()) {
            return $blockEntity;
        }

        $blockEntity->save();

        return $blockEntity;
    }

    /**
     * @param int $idPage
     *
     * @throws MissingPageException
     *
     * @return void
     */
    protected function checkPageExists($idPage)
    {
        if (!$this->cmsQueryContainer->queryPageById($idPage)
                ->count() > 0
        ) {
            throw new MissingPageException(sprintf('Tried to refer to a missing page with id %s', $idPage));
        }
    }

    /**
     * @param int $idCmsPage
     *
     * @return SpyCmsBlock
     */
    protected function getCmsBlockByIdPage($idCmsPage)
    {
        $blockEntity = $this->cmsQueryContainer->queryBlockByIdPage($idCmsPage)
            ->findOne();

        return $blockEntity;
    }

    /**
     * @param int $idCategoryNode
     *
     * @return CmsBlockTransfer[]
     */
    public function getCmsBlocksByIdCategoryNode($idCategoryNode)
    {
        $blockEntities = $this->cmsQueryContainer->queryBlockByIdCategoryNode($idCategoryNode)
            ->find();

        $blockTransfers = [];
        foreach ($blockEntities as $block) {
            $blockTransfers[$block->getIdCmsBlock()] = $this->convertBlockEntityToTransfer($block);
        }

        return $blockTransfers;
    }

    /**
     * @param CmsBlockTransfer $cmsBlockTransfer
     * @param SpyCmsBlock $blockEntity
     *
     * @return void
     */
    protected function touchKeyChangeNecessary(CmsBlockTransfer $cmsBlockTransfer, SpyCmsBlock $blockEntity)
    {
        $blockName = $this->getCmsBlockKey($blockEntity->getName(), $blockEntity->getType(), $blockEntity->getValue());
        $newBlockName = $this->getCmsBlockKey($cmsBlockTransfer->getName(), $cmsBlockTransfer->getType(), $cmsBlockTransfer->getValue());

        if ($blockName !== $newBlockName) {
            $cmsBlockTransfer->setIdCmsBlock($blockEntity->getIdCmsBlock());
            $this->touchBlockActiveWithKeyChange($cmsBlockTransfer);
        } else {
            $this->touchBlockActive($cmsBlockTransfer);
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $value
     *
     * @return string
     */
    protected function getCmsBlockKey($name, $type, $value)
    {
        $blockKey = $name . '-' . $type . '-' . $value;

        return $blockKey;
    }

    /**
     * @param int $idCategoryNode
     *
     * @return bool
     */
    public function hasBlockCategoryNodeMapping($idCategoryNode)
    {
        $mappingCount = $this->cmsQueryContainer->queryBlockByIdCategoryNode($idCategoryNode)
            ->count();

        return $mappingCount > 0;
    }

}
