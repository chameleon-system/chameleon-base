<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * assumes the path in the TCMSSmartURLData is a simple tree path.
 *
 * @deprecated since 6.1.5 - no longer needed.
/**/
class TCMSSmartURLHandler_Pagepath extends TCMSSmartURLHandler
{
    /**
     * {@inheritdoc}
     */
    public function GetPageDef()
    {
        return false;
    }

    /**
     * checks the workflowengine (if active) for tree node entries and if one matches an url part.
     *
     * @param array  $aParts          - the URL parts
     * @param string $sLanguageSuffix - optional language suffix (ISO format: en, fr)
     *
     * @return string - returns the id of a page if match found or false
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    protected function CheckPublishingWorkflowChangesPreview($aParts, $sLanguageSuffix = '')
    {
        return false;
    }

    protected function GetNaviNodes($iPortalId)
    {
        $aNodeIds = array();
        $query = "SELECT * FROM `cms_portal_navigation` WHERE `cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iPortalId)."'";
        $tres = MySqlLegacySupport::getInstance()->query($query);
        while ($aTmp = MySqlLegacySupport::getInstance()->fetch_assoc($tres)) {
            $aNodeIds[] = $aTmp['tree_node'];
        }

        return $aNodeIds;
    }
}
