<?php

namespace ChameleonSystem\SecurityBundle\CmsUser;

/**
 * Defines user roles that have special meaning in the system.
 */
class UserRoles
{
    /**
     * Every backend user is assigned this role - independent of the roles assigned via database.
     */
    final public const CMS_USER = 'ROLE_CMS_USER';

    /**
     * corresponds the role assigned via cms_role.name = 'cms_admin'
     */
    final public const CMS_ADMIN = 'ROLE_CMS_ADMIN';
}