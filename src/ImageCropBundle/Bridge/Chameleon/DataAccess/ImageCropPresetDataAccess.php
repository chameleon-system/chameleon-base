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

use ChameleonSystem\ImageCrop\DataModel\ImageCropPresetDataModel;
use ChameleonSystem\ImageCrop\Interfaces\ImageCropPresetDataAccessInterface;

class ImageCropPresetDataAccess implements ImageCropPresetDataAccessInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPresetById($id, $languageId = null)
    {
        $tableObject = \TdbCmsImageCropPreset::GetNewInstance(null, $languageId);
        if (false === $tableObject->Load($id)) {
            return null;
        }

        return $this->createDataModelFromTableObject($tableObject);
    }

    /**
     * @return ImageCropPresetDataModel
     */
    private function createDataModelFromTableObject(\TdbCmsImageCropPreset $tableObject)
    {
        return new ImageCropPresetDataModel(
            $tableObject->id,
            $tableObject->fieldSystemName,
            $tableObject->fieldName,
            (int) $tableObject->fieldWidth,
            (int) $tableObject->fieldHeight
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPresetBySystemName($systemName, $languageId = null)
    {
        $tableObject = \TdbCmsImageCropPreset::GetNewInstance(null, $languageId);
        if (false === $tableObject->LoadFromField('system_name', $systemName)) {
            return null;
        }

        return $this->createDataModelFromTableObject($tableObject);
    }
}
