<?php

namespace ChameleonSystem\CmsDashboardBundle\Library\Constants;

use ChameleonSystem\SecurityBundle\Voter\CmsVoterPrefixConstants;

class CmsGroup
{
    public const string CMS_ADMIN = CmsVoterPrefixConstants::GROUP.'CMS_ADMIN';
    public const string CMS_MANAGEMENT = CmsVoterPrefixConstants::GROUP.'CMS_MANAGEMENT';
}
