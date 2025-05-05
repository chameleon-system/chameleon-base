<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\DataAccess;

use ChameleonSystem\ImageCrop\DataModel\CmsMediaDataModel;
use ChameleonSystem\ImageCrop\DataModel\ImageCropDataModel;
use ChameleonSystem\ImageCrop\DataModel\ImageCropPresetDataModel;
use ChameleonSystem\ImageCrop\Exception\ImageCropDataAccessException;
use ChameleonSystem\ImageCrop\Interfaces\CmsMediaDataAccessInterface;
use ChameleonSystem\ImageCrop\Interfaces\ImageCropDataAccessInterface;
use ChameleonSystem\ImageCrop\Interfaces\ImageCropPresetDataAccessInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class ImageCropDataAccess implements ImageCropDataAccessInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @var ImageCropPresetDataAccessInterface
     */
    private $imageCropPresetDataAccess;

    /**
     * @var \TTools
     */
    private $tools;

    /**
     * @var CmsMediaDataAccessInterface
     */
    private $cmsMediaDataAccess;

    public function __construct(
        Connection $databaseConnection,
        ImageCropPresetDataAccessInterface $imageCropPresetDataAccess,
        \TTools $tools,
        CmsMediaDataAccessInterface $cmsMediaDataAccess
    ) {
        $this->databaseConnection = $databaseConnection;
        $this->imageCropPresetDataAccess = $imageCropPresetDataAccess;
        $this->tools = $tools;
        $this->cmsMediaDataAccess = $cmsMediaDataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function insertImageCrop(ImageCropDataModel $imageCrop)
    {
        try {
            $this->databaseConnection->beginTransaction();

            $preset = $imageCrop->getImageCropPreset();

            $id = $this->tools->GetUUID();
            $data = [
                'id' => $id,
                'cms_media_id' => $imageCrop->getCmsMedia()->getId(),
                'cms_image_crop_preset_id' => null !== $preset ? $preset->getId() : '',
                'pos_x' => $imageCrop->getPosX(),
                'pos_y' => $imageCrop->getPosY(),
                'width' => $imageCrop->getWidth(),
                'height' => $imageCrop->getHeight(),
                'name' => $imageCrop->getName(),
            ];

            $this->databaseConnection->insert('cms_image_crop', $data);
            $this->databaseConnection->commit();

            return $id;
        } catch (DBALException $e) {
            $this->databaseConnection->rollBack();
            throw new ImageCropDataAccessException(
                sprintf('Image crop could not be inserted: %s', $e->getMessage()),
                0,
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateImageCrop(ImageCropDataModel $imageCrop)
    {
        try {
            $this->databaseConnection->beginTransaction();

            $preset = $imageCrop->getImageCropPreset();

            $data = [
                'cms_media_id' => $imageCrop->getCmsMedia()->getId(),
                'cms_image_crop_preset_id' => null !== $preset ? $preset->getId() : '',
                'pos_x' => $imageCrop->getPosX(),
                'pos_y' => $imageCrop->getPosY(),
                'width' => $imageCrop->getWidth(),
                'height' => $imageCrop->getHeight(),
                'name' => $imageCrop->getName(),
            ];

            $this->databaseConnection->update('cms_image_crop', $data, ['id' => $imageCrop->getId()]);
            $this->databaseConnection->commit();
        } catch (DBALException $e) {
            $this->databaseConnection->rollBack();
            throw new ImageCropDataAccessException(
                sprintf('Image crop with id %s could not be updated: %s', $imageCrop->getId(), $e->getMessage()), 0, $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getImageCrop(CmsMediaDataModel $cmsMedia, ImageCropPresetDataModel $preset)
    {
        $tableObject = \TdbCmsImageCrop::GetNewInstance();
        if (false === $tableObject->LoadFromFields(
            ['cms_media_id' => $cmsMedia->getId(), 'cms_image_crop_preset_id' => $preset->getId()]
        )
        ) {
            return null;
        }

        return $this->createCropDataModelFromTableObject($tableObject, $cmsMedia);
    }

    /**
     * @return ImageCropDataModel
     */
    private function createCropDataModelFromTableObject(\TdbCmsImageCrop $tableObject, CmsMediaDataModel $cmsMedia)
    {
        $preset = $this->imageCropPresetDataAccess->getPresetById($tableObject->fieldCmsImageCropPresetId);

        $crop = new ImageCropDataModel(
            $tableObject->id,
            $cmsMedia,
            (int) $tableObject->fieldPosX,
            (int) $tableObject->fieldPosY,
            (int) $tableObject->fieldWidth,
            (int) $tableObject->fieldHeight
        );

        if (null !== $preset) {
            $crop->setImageCropPreset($preset);
        }

        $crop->setName($tableObject->fieldName);

        return $crop;
    }

    /**
     * {@inheritdoc}
     */
    public function getImageCropById($cropId, $languageId)
    {
        $tableObject = \TdbCmsImageCrop::GetNewInstance();
        if (false === $tableObject->Load($cropId)
        ) {
            return null;
        }

        $cmsMedia = $this->cmsMediaDataAccess->getCmsMedia($tableObject->fieldCmsMediaId, $languageId);

        return $this->createCropDataModelFromTableObject($tableObject, $cmsMedia);
    }

    /**
     * {@inheritdoc}
     */
    public function getExistingCrops(CmsMediaDataModel $cmsMedia)
    {
        $crops = [];

        try {
            $query = 'SELECT * FROM `cms_image_crop` WHERE `cms_media_id` = :mediaItemId';
            $rows = $this->databaseConnection->fetchAllAssociative($query, ['mediaItemId' => $cmsMedia->getId()]);
        } catch (DBALException $e) {
            throw new ImageCropDataAccessException(sprintf('Could not get crops: %s', $e->getMessage()), 0, $e);
        }

        foreach ($rows as $row) {
            $tableObject = \TdbCmsImageCrop::GetNewInstance($row);
            $crops[] = $this->createCropDataModelFromTableObject($tableObject, $cmsMedia);
        }

        return $crops;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCrop(string $cropId): void
    {
        try {
            $this->databaseConnection->delete('cms_image_crop', ['id' => $cropId]);
        } catch (DBALException $e) {
            throw new ImageCropDataAccessException(
                sprintf('Image crop with id %s could not be deleted: %s', $cropId, $e->getMessage()), 0, $e
            );
        }
    }
}
