<?php

namespace ChameleonSystem\SecurityBundle\Voter;

/**
 * Defines user roles that have special meaning in the system.
 */
class CmsUserRoleConstants
{
    /**
     * Every backend user is assigned this role - independent of the roles assigned via database.
     */
    final public const CMS_USER = 'ROLE_CMS_USER';

    /** Fake ID for the ROLE_CMS_USER in the user role array */
    final public const CMS_USER_FAKE_ID = '-';

    /**
     * Corresponds to the role assigned via cms_role.name = 'cms_admin'.
     */
    final public const CMS_ADMIN = 'ROLE_CMS_ADMIN';
}
