<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;

interface DataAccessCmsTblConfInterface
{
    public const PERMISSION_MAPPING = [
        CmsPermissionAttributeConstants::TABLE_EDITOR_NEW => 'cms_role_mlt',
        CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT => 'cms_role1_mlt',
        CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE => 'cms_role2_mlt',
        CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT_ALL => 'cms_role3_mlt',
        CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS_ALL => 'cms_role6_mlt',
        CmsPermissionAttributeConstants::TABLE_EDITOR_NEW_LANGUAGE => 'cms_role4_mlt',
        //CmsPermissionAttributeConstants::WORKFLOW_PUBLISH => 'cms_role5_mlt',
        //CmsPermissionAttributeConstants::VERSIONING => 'cms_role7_mlt',
    ];
    /**
     * returns array with table ids as key, and name as value
     * @return array<string,string>
     */
    public function getTableNames(): array;

    /**
     * returns the roles assigned to the action
     * @param string $action
     * @param string $tableName
     * @return array<string>
     */
    public function getPermittedRoles(string $action, string $tableName): array;
}