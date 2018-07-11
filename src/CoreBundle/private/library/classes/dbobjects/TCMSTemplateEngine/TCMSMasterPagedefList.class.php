<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSMasterPagedefList extends TAdbCmsMasterPagedefList
{
    protected function GetBaseQuery()
    {
        if (is_null($this->sQuery)) {
            $oGlobal = TGlobal::instance();

            $portalRestriction = $oGlobal->oUser->oAccessManager->user->portals->PortalList();

            $this->sQuery = 'SELECT DISTINCT `cms_master_pagedef`.*
                           FROM `cms_master_pagedef`
                      LEFT JOIN `cms_master_pagedef_cms_portal_mlt` ON `cms_master_pagedef`.`id` = `cms_master_pagedef_cms_portal_mlt`.`source_id`
                          WHERE ';
            $this->sQuery .= " (`cms_master_pagedef_cms_portal_mlt`.`target_id` IN ({$portalRestriction}) OR `cms_master_pagedef`.`restrict_to_portals`='0')";
            $this->sQuery .= ' ORDER BY `cms_master_pagedef`.`name`';
        }

        return $this->sQuery;
    }
}
