<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_treeNodeConnectedPageName($recordID, $row)
{
    $sName = ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list.gcf_connected_page_no_page');
    $oCmsTplPage = TdbCmsTplPage::GetNewInstance();
    /* @var $oCmsTplPage TdbCmsTplPage */
    $oCmsTplPage->Load($recordID);
    if (!is_null($oCmsTplPage)) {
        $sName = TGlobal::OutHTML($oCmsTplPage->GetName());
    }

    return $sName;
}
