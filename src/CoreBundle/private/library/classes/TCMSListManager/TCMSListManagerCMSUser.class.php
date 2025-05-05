<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * manages the webpage list (links to the template engine interface).
 */
class TCMSListManagerCMSUser extends TCMSListManagerFullGroupTable
{
    /**
     * show only records that belong to the user (if the table contains the user id).
     */
    public function GetUserRestriction()
    {
        $query = parent::GetUserRestriction();
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if ($securityHelper->isGranted(CmsUserRoleConstants::CMS_ADMIN)) {
            return $query;
        }

        // if the user is not an admin, then he may not view admin users and the public www user

        $tmpquery = "SELECT DISTINCT `cms_user_cms_role_mlt`.source_id
                   FROM `cms_user_cms_role_mlt`
             INNER JOIN  `cms_role` ON `cms_user_cms_role_mlt`.`target_id` = `cms_role`.`id`
                  WHERE `cms_role`.`name` = 'cms_admin' OR `cms_role`.`name` = 'www'";
        $adminRows = $this->getDatabaseConnection()->fetchAllAssociative($tmpquery);
        $aAdminIds = [];
        foreach ($adminRows as $admin) {
            $aAdminIds[] = $admin['source_id'];
        }

        if (count($aAdminIds) > 0) {
            // get admin users and exclude them..
            $databaseConnection = $this->getDatabaseConnection();
            $adminIdString = implode(',', array_map([$databaseConnection, 'quote'], $aAdminIds));
            $restrictionQuery = $this->CreateRestriction('id', "NOT IN ($adminIdString)");
        }
        if (!empty($restrictionQuery)) {
            $query .= " {$restrictionQuery}";
        }

        return $query;
    }

    /**
     * shows the delete button only if user is not required from system.
     *
     * @param string $id
     * @param array $row
     *
     * @return string
     */
    public function CallBackFunctionBlockDeleteButton($id, $row)
    {
        if (!$row['is_system']) {
            return parent::CallBackFunctionBlockDeleteButton($id, $row);
        }
        $translator = $this->getTranslator();

        return sprintf('<span title="%s" class="fas fa-trash-alt text-danger" style="color: #d9534f; opacity: .5;"></span>',
            TGlobal::OutJS($translator->trans('chameleon_system_core.list.system_entry_delete_not_allowed'))
        );
    }

    public function callbackCmsUserWithImage(string $name, array $row): string
    {
        $name = $name.', '.$row['firstname'];

        $imageTag = '<i class="fas fa-user mr-2"></i>';

        $imageId = $row['images'];
        if (false === is_numeric($imageId) || (int) $imageId >= 1000) {
            $image = new TCMSImage();
            if (null !== $image) {
                $image->Load($imageId);
                $oThumbnail = $image->GetThumbnail(16, 16);
                if (!is_null($oThumbnail)) {
                    $oBigThumbnail = $image->GetThumbnail(400, 400);
                    $imageTag = '<img src="'.TGlobal::OutHTML($oThumbnail->GetFullURL()).'" width="'.TGlobal::OutHTML($oThumbnail->aData['width']).'" height="'.TGlobal::OutHTML($oThumbnail->aData['height'])."\" hspace=\"0\" vspace=\"0\" border=\"0\" onclick=\"CreateMediaZoomDialogFromImageURL('".$oBigThumbnail->GetFullURL()."','".TGlobal::OutHTML($oBigThumbnail->aData['width'])."','".TGlobal::OutHTML($oBigThumbnail->aData['height'])."')\" style=\"cursor: hand; cursor: pointer; margin-right:10px\" align=\"left\" />";
                }
            }
        }

        return "<div>{$imageTag}".TGlobal::OutHTML($name).'<div class="cleardiv">&nbsp;</div></div>';
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ServiceLocator::get('database_connection');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ServiceLocator::get('translator');
    }
}
