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

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\MediaManager\AccessRightsModel;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;

class ImageCropAccessRightsMapper extends \AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        $oVisitor->SetMappedValue('accessRightsCrop', $this->createAccessRightsModel('cms_image_crop'));
    }

    /**
     * @param string $tableName
     *
     * @return AccessRightsModel
     */
    private function createAccessRightsModel($tableName)
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $accessRightsModel = new AccessRightsModel();

        $accessRightsModel->new = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $tableName);
        $accessRightsModel->edit = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $tableName);
        $accessRightsModel->delete = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $tableName);
        $accessRightsModel->show = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $tableName);

        return $accessRightsModel;
    }
}
