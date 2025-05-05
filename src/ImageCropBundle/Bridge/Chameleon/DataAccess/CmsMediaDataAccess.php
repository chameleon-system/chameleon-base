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
use ChameleonSystem\ImageCrop\Interfaces\CmsMediaDataAccessInterface;

class CmsMediaDataAccess implements CmsMediaDataAccessInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCmsMedia($id, $languageId)
    {
        $tableObject = \TdbCmsMedia::GetNewInstance(null, $languageId);
        if (false === $tableObject->Load($id)) {
            return null;
        }

        return $this->createDataModelFromTableObject($tableObject);
    }

    /**
     * @return CmsMediaDataModel
     */
    private function createDataModelFromTableObject(\TdbCmsMedia $tableObject)
    {
        return new CmsMediaDataModel(
            $tableObject->id,
            PATH_MEDIA_LIBRARY.$tableObject->fieldPath,
            $tableObject->fieldPath,
            URL_MEDIA_LIBRARY.$tableObject->fieldPath
        );
    }
}
