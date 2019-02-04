<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\MediaManager\Mapper;

use AbstractViewMapper;
use ChameleonSystem\MediaManager\AccessRightsModel;
use IMapperCacheTriggerRestricted;
use IMapperRequirementsRestricted;
use IMapperVisitorRestricted;

class ImageCropAccessRightsMapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        $oVisitor->SetMappedValue('accessRightsCrop', $this->createAccessRightsModel('cms_image_crop'));
    }

    /**
     * @param string $tableName
     *
     * @return AccessRightsModel
     */
    private function createAccessRightsModel($tableName)
    {
        $accessRightsModel = new AccessRightsModel();
        $backendUser = \TdbCmsUser::GetActiveUser();
        if (null === $backendUser) {
            return $accessRightsModel;
        }

        $accessManager = $backendUser->oAccessManager;
        $accessRightsModel->new = $accessManager->HasNewPermission($tableName);
        $accessRightsModel->edit = $accessManager->HasEditPermission($tableName);
        $accessRightsModel->delete = $accessManager->HasDeletePermission($tableName);
        $accessRightsModel->show = $accessManager->HasShowAllPermission(
                $tableName
            ) || $accessManager->HasShowAllReadOnlyPermission($tableName);

        return $accessRightsModel;
    }
}
