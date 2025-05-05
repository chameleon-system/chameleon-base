<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorDocumentPkgUrlAlias extends TCMSTableEditorDocumentPkgUrlAliasAutoParent
{
    /**
     * saves only one field of a record (like the edit-on-click WYSIWYG).
     *
     * @param string $sFieldName the field name to save to
     * @param string $sFieldContent the content to save
     * @param bool $bTriggerPostSaveHook - if set to true, the PostSaveHook method will be called at the end of the call
     *
     * @return TCMSstdClass
     */
    public function SaveField($sFieldName, $sFieldContent, $bTriggerPostSaveHook = false)
    {
        // create pkgUrlAlias entry
        $bCreateURLAlias = false;
        if ('cms_document_tree_id' == $sFieldName && TCMSRecord::TableExists('cms_url_alias') && PKG_URL_ALIAS_ADD_PUBLIC_DOCUMENT_REDIRECTS_ON_MOVE) {
            $oDocument = TdbCmsDocument::GetNewInstance($this->sId);
            if ('0' == $oDocument->sqlData['private']) { // redirect works only with public links
                $bCreateURLAlias = true;
                $sOldDownloadURL = $oDocument->GetPlainDownloadLink(false, false, true);
            }
        }

        $oReturnVal = parent::SaveField($sFieldName, $sFieldContent, $bTriggerPostSaveHook);

        if ($bCreateURLAlias) {
            $oDocument = TdbCmsDocument::GetNewInstance($this->sId);
            $sNewDownloadURL = $oDocument->GetPlainDownloadLink(false, false, true);

            // check if redirect exists as target for the old url that is newer than 24h
            $sQuery = "SELECT *
            FROM `cms_url_alias`
            WHERE `target_url` = '".MySqlLegacySupport::getInstance()->real_escape_string($sOldDownloadURL)."'
            AND `expiration_date` > NOW()
            AND `active` = '1'
            AND TIMEDIFF( NOW( ) , `datecreated` ) <= '24:00:00'
            ";
            $oURLAliasList = TdbCmsUrlAliasList::GetList($sQuery);
            if ($oURLAliasList->Length() > 0) {
                // update existing redirect (in a perfect world this should only be one record)
                while ($oURLAlias = $oURLAliasList->Next()) {
                    /** @var TCMSTableEditorManager $oTableEditorURLAlias */
                    $oTableEditorURLAlias = TTools::GetTableEditorManager('cms_url_alias', $oURLAlias->id);
                    $oTableEditorURLAlias->AllowEditByAll(true);
                    $oTableEditorURLAlias->AllowDeleteByAll(true);
                    if ($oURLAlias->fieldTargetUrl == $sNewDownloadURL) { // looks like someone moved a download and then moved it back to the original directory, so delete the redirect
                        $oTableEditorURLAlias->Delete($oURLAlias->id);
                    } else {
                        $aPostData = [];
                        $aPostData['id'] = $oURLAlias->id;
                        $aPostData['datecreated'] = date('Y-m-d H:i:s');
                        $aPostData['expiration_date'] = date('Y-m-d H:i:s', time() + 259200); // redirect expires after 3 days
                        $aPostData['target_url'] = $sNewDownloadURL;
                        $oTableEditorURLAlias->Save($aPostData);
                    }
                }
            } else {
                // insert new redirect URL
                /** @var TCMSTableEditorManager $oTableEditorURLAlias */
                $oTableEditorURLAlias = TTools::GetTableEditorManager('cms_url_alias');
                $oTableEditorURLAlias->AllowEditByAll(true);

                $aPostData = [];
                $aPostData['name'] = 'reordered download '.$sOldDownloadURL;
                $aPostData['source_url'] = $sOldDownloadURL;
                $aPostData['target_url'] = $sNewDownloadURL;
                $aPostData['datecreated'] = date('Y-m-d H:i:s');
                $aPostData['expiration_date'] = date('Y-m-d H:i:s', time() + 259200); // redirect expires after 3 days
                $aPostData['active'] = '1';
                $aPostData['exact_match'] = '1';

                $oTableEditorURLAlias->Save($aPostData);
            }
        }

        return $oReturnVal;
    }
}
