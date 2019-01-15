<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;

/**
 * {@inheritdoc}
 */
class TCMSFieldLookupMultiselectTags extends TCMSFieldLookupMultiselect
{
    /**
     * if true, the field tries to load tag suggestions.
     *
     * @var bool - default true
     */
    protected $bShowSuggestions = true;

    /**
     * list object of all currently connected tags.
     *
     * @var null|TdbCmsTagsList
     */
    protected $oConnectedMLTRecords = null;

    /**
     * defines the amount of tags that should be shown as suggestions.
     *
     * @var int
     */
    protected $iMaxTagSuggestions = 10;

    /**
     * defines the amount of tags that will be shown as autocomplete.
     *
     * @var int
     */
    protected $iMaxAutocompleteTags = 20;

    /**
     * language suffix for name field of cms_tags if field based translation is active.
     *
     *
     * @var null|string - "__en"
     */
    protected $sLanguageIsoName = null;

    public function GetHTML()
    {
        $tags = '';
        if (is_null($this->oConnectedMLTRecords)) {
            $this->oConnectedMLTRecords = $this->FetchConnectedMLTRecords();
        }
        $this->oConnectedMLTRecords->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
        while ($oMLTRecord = $this->oConnectedMLTRecords->Next()) {
            $tags .= $oMLTRecord->GetDisplayValue().', ';
        }

        if (', ' === substr($tags, -2, 2)) {
            $tags = substr($tags, 0, -2);
        }

        $html = '<div style="">'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_lookup_multi_select_tag.separator_hint')).'</div>
      <div style="padding-right: 27px;"><textarea id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" class="form-control form-control-sm">'.TGlobal::OutHTML($tags)."</textarea></div>\n";
        if ($this->bShowSuggestions) {
            $html .= '<div id="'.TGlobal::OutHTML($this->name).'_suggested" class="tagInputSuggestedTags"><span class="label">'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_lookup_multi_select_tag.suggestions')).": </span><span class=\"tagInputSuggestedTagList\"></span></div>\n";
        }

        return $html;
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('GetTagList', 'GetTagSuggestions');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * returns the language suffix for the name field if field is translated field based
     * e.g "__en".
     *
     * @return string
     */
    protected function GetLanguageSuffix()
    {
        if (null === $this->sLanguageIsoName) {
            $sLanguageIsoName = '';
            if (TdbCmsTags::CMSFieldIsTranslated('name')) {
                $oCMSConfig = TdbCmsConfig::GetInstance();
                $sBaseLanguageID = $oCMSConfig->fieldTranslationBaseLanguageId;
                $sActiveLanguageId = TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID();
                if ($sBaseLanguageID !== $sActiveLanguageId) {
                    $sLanguageIsoName = '__'.$this->getLanguageService()->getLanguageIsoCode($sActiveLanguageId);
                }
            }

            $this->sLanguageIsoName = $sLanguageIsoName;
        }

        return $this->sLanguageIsoName;
    }

    /**
     * this method is called via ajax for autocompletion.
     *
     * @return array
     */
    public function GetTagList()
    {
        $aFoundTags = array();
        $oGlobal = TGlobal::instance();

        if ($oGlobal->UserDataExists('search')) {
            $sLanguageIsoName = $this->GetLanguageSuffix();
            $sTagBlackListQueryPart = '';
            if ($oGlobal->UserDataExists('currentTags')) {
                $sCurrentTags = $oGlobal->GetUserData('currentTags');
                $aCurrentTags = explode(',', $sCurrentTags);
                if (count($aCurrentTags) > 0) {
                    $sTagBlackList = '';
                    foreach ($aCurrentTags as $sCurrentTag) {
                        $sTagBlackList .= "'".MySqlLegacySupport::getInstance()->real_escape_string(trim(strtolower($sCurrentTag)))."',";
                    }
                    $sTagBlackList = substr($sTagBlackList, 0, -1);

                    $sTagBlackListQueryPart = ' AND `name'.$sLanguageIsoName.'` NOT IN ('.$sTagBlackList.')';
                }
            }

            $query = 'SELECT DISTINCT *
                  FROM `cms_tags`
                  WHERE `name'.MySqlLegacySupport::getInstance()->real_escape_string($sLanguageIsoName)."` LIKE '".MySqlLegacySupport::getInstance()->real_escape_string(strtolower($oGlobal->GetUserData('search')))."%' ".$sTagBlackListQueryPart;

            $query .= ' ORDER BY `name` ASC';
            $iLimit = $this->iMaxAutocompleteTags;
            if ($oGlobal->UserDataExists('limit')) {
                $iLimit = $oGlobal->GetUserData('limit');
            }
            $query .= ' LIMIT '.MySqlLegacySupport::getInstance()->real_escape_string($iLimit);
            $oRecordList = TdbCmsTagsList::GetList($query);
            $oRecordList->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());

            while ($oRecord = $oRecordList->Next()) {
                $iTagUsageCount = $oRecord->fieldCount;
                $name = $oRecord->GetName();
                if (!empty($name)) {
                    $aFoundTags[] = array('tag' => $name, 'freq' => $iTagUsageCount);
                }
            }
        }

        return $aFoundTags;
    }

    /**
     * called on each field after the record is saved (NOT on insert, only on save).
     *
     * @param int $iRecordId - the id of the record
     */
    public function PostSaveHook($iRecordId)
    {
        // save tags
        $oRecord = new TCMSRecord();
        /** @var $oRecord TCMSRecord */
        $oRecord->table = $this->sTableName;
        $oRecord->Load($iRecordId);
        $tags = $this->data;
        $aTags = explode(',', $tags);
        if (0 === $tags || '0' === $tags) {
            /** @var $oTableEditor TCMSTableEditorManager */
            $sDBTableName = 'Tdb'.TCMSTableToClass::GetClassName('', $this->sTableName);
            $oTableRecord = call_user_func(array($sDBTableName, 'GetNewInstance'));
            /** @var $oTableRecord TCMSRecord */
            $oTableRecord->Load($this->recordId);
            $aCmsTagIDs = $oTableRecord->GetMLTIdList('cms_tags');
            if (is_array($aCmsTagIDs) && count($aCmsTagIDs) > 0) {
                $databaseConnection = $this->getDatabaseConnection();
                $cmsTagIdString = implode(',', array_map(array($databaseConnection, 'quote'), $aCmsTagIDs));

                $query = "SELECT * FROM `cms_tags` WHERE `id` IN ($cmsTagIdString)";
                $oCmsTagList = TdbCmsTagsList::GetList($query);
                $aTags = array();
                while ($oCmsTag = $oCmsTagList->Next()) {
                    $aTags[] = $oCmsTag->fieldName;
                }
            }
        }

        $oTagTableConf = new TCMSTableConf();
        $oTagTableConf->LoadFromField('name', 'cms_tags');

        // loop through tags and insert missing tags to database
        $aTagIDs = array();
        foreach ($aTags as $tag) {
            $cleanTag = trim(strtolower($tag));
            if ('' != $cleanTag) {
                // perform case insensitive search for existing tag
                $sLanguageIsoName = $this->GetLanguageSuffix();
                $checkQuery = 'SELECT * FROM `cms_tags` WHERE `name'.MySqlLegacySupport::getInstance()->real_escape_string($sLanguageIsoName)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($cleanTag)."'";
                $checkResult = MySqlLegacySupport::getInstance()->query($checkQuery);
                if (MySqlLegacySupport::getInstance()->num_rows($checkResult) < 1) { // tag not yet in database, so perform an insert
                    $oTableManager = new TCMSTableEditorManager();
                    /** @var $oTableManager TCMSTableEditorManager */
                    $oTableManager->Init($oTagTableConf->id, null);
                    $aDefaultData = array('name' => $cleanTag, 'urlname' => $this->getUrlNormalizationUtil()->normalizeUrl($cleanTag));
                    $oTableManager->Save($aDefaultData);
                    $aTagIDs[] = $oTableManager->sId;
                } else {
                    $row = MySqlLegacySupport::getInstance()->fetch_assoc($checkResult);
                    $aTagIDs[] = $row['id'];
                }
            }
        }

        // perform mlt changes
        if (is_array($oRecord->sqlData) && array_key_exists('id', $oRecord->sqlData) && !empty($oRecord->sqlData['id'])) {
            $recordId = $oRecord->sqlData['id'];
            $mltTableName = $this->GetMLTTableName();

            $oTableConf = new TCMSTableConf();
            $oTableConf->LoadFromField('name', $this->sTableName);

            /** @var $aConnectedIds array */
            $aConnectedIds = array();

            $sAlreadyConnectedQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTableName)."` WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($recordId)."'";
            $tRes = MySqlLegacySupport::getInstance()->query($sAlreadyConnectedQuery);
            while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
                $aConnectedIds[] = $aRow['target_id'];
            }

            $aNewConnections = array_diff($aTagIDs, $aConnectedIds);
            $aDeletedConnections = array_diff($aConnectedIds, $aTagIDs);

            $targetField = $this->name;

            $oTableEditor = new TCMSTableEditorManager();
            $oTableEditor->Init($oTableConf->id, $recordId);

            foreach ($aDeletedConnections as $targetID) {
                $oTableEditor->oTableEditor->removeTagMLTConnection($targetField, $targetID);
            }

            foreach ($aNewConnections as $targetID) {
                $oTableEditor->oTableEditor->addTagMLTConnection($targetField, $targetID);
            }
        }
    }

    public function GetSQL()
    {
        return false; // prevent saving of sql
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        parent::GetCMSHtmlHeadIncludes();
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/timers/jQuery.timers.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/tagInput/jQuery.tagInput.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/tags.css" rel="stylesheet" type="text/css" />';

        $oTableConf = &$this->oTableRow->GetTableConf();
        $sAutoCompleteURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('pagedef' => 'tableeditor', '_rmhist' => 'false', 'module_fnc[contentmodule]' => 'ExecuteAjaxCall', 'callFieldMethod' => '1', 'id' => $this->recordId, 'tableid' => $oTableConf->id, '_fieldName' => $this->name, '_fnc' => 'GetTagList'));
        $sSuggestedTagsURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('pagedef' => 'tableeditor', '_rmhist' => 'false', 'module_fnc[contentmodule]' => 'ExecuteAjaxCall', 'callFieldMethod' => '1', 'id' => $this->recordId, 'tableid' => $oTableConf->id, '_fieldName' => $this->name, '_fnc' => 'GetTagSuggestions'));

        $sTaggingJS = '<script type="text/javascript">
        $(function(){
          $("#'.TGlobal::OutJS($this->name).'").tagInput({
            jsonUrl:"'.$sAutoCompleteURL.'",
            sortBy:"frequency",';

        if ($this->bShowSuggestions) {
            $sTaggingJS .= 'loadSuggestedTagsFromJSON:true,
              jsonUrlSuggestedTags:"'.$sSuggestedTagsURL.'",
              suggestedTagsPlaceHolder:$("#'.TGlobal::OutJS($this->name).'_suggested"),'."\n";
        }

        $sTaggingJS .= 'autoFilter:false,
            autoStart:false
          })
        })
      </script>';

        $aIncludes[] = $sTaggingJS;

        return $aIncludes;
    }

    /**
     * you may overwrite this method for custom tag suggestions
     * returns multidimensional array sorted by tag frequence array(array('tag'='fooBaa','count'=>3)).
     *
     * @return array
     */
    public function GetTagSuggestions()
    {
        $aTagSuggestions = array();

        $oGlobal = TGlobal::instance();
        $sConnectedTagIds = '';
        if ($oGlobal->UserDataExists('currentTags')) {
            $sLanguageIsoName = $this->GetLanguageSuffix();

            $sCurrentTags = $oGlobal->GetUserData('currentTags');
            $aCurrentTags = explode(',', $sCurrentTags);
            if (count($aCurrentTags) > 0) {
                $sTagList = '';
                foreach ($aCurrentTags as $sCurrentTag) {
                    $sTagList .= "'".MySqlLegacySupport::getInstance()->real_escape_string(trim(strtolower($sCurrentTag)))."',";
                }
                $sTagList = substr($sTagList, 0, -1);

                $sTagListQuery = 'SELECT * FROM `cms_tags` WHERE `name'.$sLanguageIsoName.'` IN ('.$sTagList.')';

                $oTagsList = TdbCmsTagsList::GetList($sTagListQuery);
                $sConnectedTagIds = $oTagsList->GetIdList('id', true);
            }
        }

        if (empty($sConnectedTagIds)) {
            if (is_null($this->oConnectedMLTRecords)) {
                $this->oConnectedMLTRecords = $this->FetchConnectedMLTRecords();
            }
            $sConnectedTagIds = $this->oConnectedMLTRecords->GetIdList('id', true);
        }

        $sMLTTableName = $this->GetMLTTableName();

        $sRecordId = $this->recordId;
        $oTableConf = new TCMSTableConf();
        $oTableConf->LoadFromField('name', $this->sTableName);

        $aTagSuggestionsFinal = array();
        if (!empty($sConnectedTagIds)) {
            $sQuery = '
        SELECT DISTINCT `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.* FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
        LEFT JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($this->GetMLTTableName()).'` ON `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'`.`source_id` =  `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.`id`
        WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'`.`target_id` IN ('.$sConnectedTagIds.') AND `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName)."`.`id` != '".$sRecordId."'
        ";

            $result = MySqlLegacySupport::getInstance()->query($sQuery);
            while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($result)) {
                $sMLTQuery = '
          SELECT DISTINCT `cms_tags`.* FROM `cms_tags`
          LEFT JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($this->GetMLTTableName()).'` ON `'.MySqlLegacySupport::getInstance()->real_escape_string($this->GetMLTTableName()).'`.`target_id` =  `cms_tags`.`id`
          WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'`.`target_id` NOT IN ('.$sConnectedTagIds.') AND `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName)."`.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($row['id'])."'
          ";
                $oSimilarTagsList = TdbCmsTagsList::GetList($sMLTQuery, TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
                while ($oSimilarTag = $oSimilarTagsList->Next()) {
                    $sTag = $oSimilarTag->GetName();
                    if (!array_key_exists($sTag, $aTagSuggestions)) { // add tag to suggestions
                        $aTagSuggestions[$sTag] = $oSimilarTag->fieldCount;
                    } else { // count up instead
                        $aTagSuggestions[$sTag] = $oSimilarTag->fieldCount;
                    }
                }
            }

            asort($aTagSuggestions, SORT_NUMERIC); // we want high frequent tags at first
            $aTagSuggestions = array_reverse($aTagSuggestions);

            reset($aTagSuggestions);
            $aTagSuggestionsFinal = array();
            $i = 0;
            foreach ($aTagSuggestions as $sTag => $iCount) {
                ++$i;
                if ($i == $this->iMaxTagSuggestions) {
                    break;
                }
                $aTagSuggestionsFinal[] = array('tag' => $sTag, 'count' => $iCount); // note: count is not yet used
            }
        }

        return $aTagSuggestionsFinal;
    }

    /**
     * returns true if field data is not empty
     * overwrite this method for mlt and property fields.
     *
     * @return bool
     */
    public function HasContent()
    {
        $bHasContent = false;
        $sValue = $this->GetCleanedTagString();
        if ('' !== $sValue) {
            $bHasContent = true;
        }

        return $bHasContent;
    }

    protected function GetCleanedTagString()
    {
        $sReturnTags = '';
        $aTagData = explode(',', $this->data);
        if (count($aTagData) > 0) {
            $aValidTags = array();
            foreach ($aTagData as $sTagName) {
                if ('' !== trim($sTagName)) {
                    $aValidTags[] = trim($sTagName);
                }
            }
            $sReturnTags = implode(',', $aValidTags);
        }

        return $sReturnTags;
    }

    /**
     * @return UrlNormalizationUtil
     */
    private function getUrlNormalizationUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }
}
