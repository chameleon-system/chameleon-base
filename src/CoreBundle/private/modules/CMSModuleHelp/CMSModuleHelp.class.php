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
 * shows help texts from all table fields.
/**/
class CMSModuleHelp extends TCMSModelBase
{
    protected $listParams = null;
    protected $oTableManager = null;

    /**
     * TCMSTableConf object of table based on "tableID".
     *
     * @var TCMSTableConf
     */
    protected $oTableConf = null;
    protected $oTableList = null;

    public function Init()
    {
    }

    public function &Execute()
    {
        $this->data = parent::Execute();

        $oCMSUser = TCMSUser::GetActiveUser();
        $aTableBlackList = array();

        $sTableHTML = '<table class="table table-striped table-bordered table-sm">
        <thead>
            <tr>
                <th>'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_help_text.header_table')).'</th>
                <th>'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_help_text.header_field')).'</th>
                <th>'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_help_text.header_field_description')).'</th>
            </tr>
        </thead>
        <tbody>';
        $sQuery = 'SELECT * FROM `cms_tbl_conf` ORDER BY `cms_tbl_conf`.`name` ASC ';
        $oTableList = TdbCmsTblConfList::GetList($sQuery);
        /** @var $oTable TdbCmsTblConf */
        while ($oTable = $oTableList->Next()) {
            if (!in_array($oTable->fieldName, $aTableBlackList)) {
                $tableInUserGroup = $oCMSUser->oAccessManager->user->IsInGroups($oTable->fieldCmsUsergroupId);
                $bRightAllowEdit = $oCMSUser->oAccessManager->HasEditPermission($oTable->fieldName);
                $bRightShowAllReadOnly = $oCMSUser->oAccessManager->HasShowAllReadOnlyPermission($oTable->fieldName);

                if ($tableInUserGroup && ($bRightAllowEdit || $bRightShowAllReadOnly)) {
                    $sTableHTML .= '<tr class="headline"><td colspan="3">';
                    $sTableHTML .= '<h2 style="font-size: 16px;">';

                    if (!empty($oTable->fieldCmsContentBoxId)) {
                        $sURL = PATH_CMS_CONTROLLER.'?pagedef=tablemanager&id='.$oTable->id;
                        $sTableHTML .= '<a href="'.$sURL.'" style="float:left; color: #841313;">';
                    }

                    $sTableHTML .= TGlobal::OutHTML($oTable->GetName()).' <span>['.TGlobal::OutHTML($oTable->fieldName).']</span>';

                    if (!empty($oTable->fieldCmsContentBoxId)) {
                        $sTableHTML .= '</a><img src="'.URL_CMS.'/images/icons/application_view_detail.png" style="float: left; margin-left: 10px; margin-top: 5px;" /><div class="cleardiv"></div>';
                    }

                    $sTableHTML .= '</h2>';

                    $sTableHTML .= '</td></tr>';
                    if (!empty($oTable->fieldNotes)) {
                        $sTableHTML .= '<tr><td colspan="3">'.TGlobal::OutHTML($oTable->fieldNotes)."</td></tr>\n";
                    }

                    // get base Data first
                    $sTableHTML .= '<tr><td>&nbsp;</td><td colspan="2"><h3>'.TGlobal::Translate('chameleon_system_core.cms_module_help_text.header_tables').'</h3></td></tr>';
                    $sQuery = "SELECT * FROM `cms_field_conf` WHERE `cms_tbl_conf_id` = '".$oTable->id."' AND `cms_tbl_field_tab` = ''";
                    $oFieldList = TdbCmsFieldConfList::GetList($sQuery);
                    while ($oField = $oFieldList->Next()) {
                        $sTableHTML .= '<tr>';
                        $sTableHTML .= '<td>&nbsp;</td><td><h2>Feld: '.TGlobal::OutHTML($oField->GetName())."</h2></td>\n";
                        if (!empty($oField->field049Helptext)) {
                            $sTableHTML .= '<td>'.nl2br(TGlobal::OutHTML($oField->field049Helptext))."</td>\n";
                        } else {
                            $sTableHTML .= "<td>&nbsp;</td>\n";
                        }
                        $sTableHTML .= '</tr>';
                    }

                    $oTabList = $oTable->GetFieldCmsTblFieldTabList();
                    if ($oTabList->Length() > 0) {
                        while ($oTab = $oTabList->Next()) {
                            $sQuery = "SELECT * FROM `cms_field_conf` WHERE `cms_field_conf`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTable->id)."' AND `cms_field_conf`.`cms_tbl_field_tab` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTab->id)."'";
                            $oTabFieldList = TdbCmsFieldConfList::GetList($sQuery);
                            if ($oTabFieldList->Length() > 0) {
                                $sTableHTML .= '<tr><td>&nbsp;</td><td colspan="2"><h3>'.TGlobal::OutHTML($oTab->fieldName).'</h3></td></tr>';
                                while ($oTabField = $oTabFieldList->Next()) {
                                    $sTableHTML .= '<tr>';
                                    $sTableHTML .= '<td>&nbsp;</td>
                                    <td><h2>Feld: '.TGlobal::OutHTML($oTabField->GetName())."</h2></td>\n";
                                    $sTableHTML .= '<td>';

                                    if (!empty($oTabField->field049Helptext)) {
                                        $sTableHTML .= nl2br(TGlobal::OutHTML($oTabField->field049Helptext));
                                    } else {
                                        $sTableHTML .= '&nbsp;';
                                    }
                                    $sTableHTML .= "</td>\n";
                                    $sTableHTML .= '</tr>';
                                }
                            }
                        }
                    }
                }
            }
        }
        $sTableHTML .= '
        </tbody>
        </table>';

        $this->data['sHtml'] = $sTableHTML;

        return $this->data;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        return $aIncludes;
    }
}
