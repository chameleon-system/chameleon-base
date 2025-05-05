<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorMasterPagedef extends TCMSTableEditor
{
    /**
     * {@inheritdoc}
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        $sLayoutFile = PATH_CUSTOMER_PAGELAYOUTS.'/'.$this->oTable->sqlData['layout'].'.layout.php';
        if (false === is_file($sLayoutFile) || false === is_readable($sLayoutFile)) {
            return;
        }
        $sLayoutFileContent = file_get_contents($sLayoutFile);
        $matchCount = preg_match_all("#GetModule\('(.+?)'\)#", $sLayoutFileContent, $aMatches);
        if (false === $matchCount || 0 === $matchCount) {
            return;
        }
        $databaseConnection = $this->getDatabaseConnection();
        foreach ($aMatches[1] as $sModuleSpotName) {
            // check if module spot is already in database
            $sQuery = 'SELECT COUNT(*) FROM cms_master_pagedef_spot WHERE cms_master_pagedef_id = :id AND name = :moduleSpotName';
            $result = $databaseConnection->fetchOne($sQuery, [
                'id' => $this->sId,
                'moduleSpotName' => $sModuleSpotName,
            ]);
            if (((int) $result) > 0) {
                continue;
            }
            $postData['cms_master_pagedef_id'] = $this->sId;
            $postData['name'] = $sModuleSpotName;
            $postData['model'] = 'MTEmpty';
            $postData['view'] = 'standard';
            $postData['static'] = '0';

            $iTableID = TTools::GetCMSTableId('cms_master_pagedef_spot');
            $oTableEditor = new TCMSTableEditorManager();
            $oTableEditor->Init($iTableID);
            $oTableEditor->Save($postData);
        }
    }
}
