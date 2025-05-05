<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorCronJob extends TCMSTableEditor
{
    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'runCronJob';
        $oMenuItem->sDisplayName = ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_cron_jobs.action_run_job');
        $oMenuItem->sIcon = 'far fa-play-circle';

        $sCallURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(['pagedef' => 'runcrons', 'cronjobid' => $this->sId]);

        $oMenuItem->sOnClick = "CreateModalIFrameDialogCloseButton('".$sCallURL."',0,0,'".TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_cron_jobs.action_run_job_confirm'))."',true,true);";
        $this->oMenuItems->AddItem($oMenuItem);
    }
}
