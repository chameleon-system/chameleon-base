<?php

namespace ChameleonSystem\CoreBundle\Security;

use ChameleonSystem\CoreBundle\DataModel\CmsMasterPagdef;

interface PageAccessCheckInterface
{
    /**
     * Checks for page access of the current user/client to the current page.
     * In case of failure this redirects to the login page.
     *
     * @return void
     */
    public function assertAccess();

    /**
     * @param \TdbCmsUser     $user
     * @param CmsMasterPagdef $pagedef
     *
     * @return bool - false if page access is forbidden
     */
    public function checkPageAccess(\TdbCmsUser $user, CmsMasterPagdef $pagedef): bool;
}
