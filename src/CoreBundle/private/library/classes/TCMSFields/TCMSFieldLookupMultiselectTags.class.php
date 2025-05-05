<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

/**
 * {@inheritdoc}
 */
class TCMSFieldLookupMultiselectTags extends TCMSFieldLookupMultiselect
{
    /**
     * if true, the field tries to load tag suggestions.
     *
     * @var bool
     */
    protected $bShowSuggestions = true;

    /**
     * list object of all currently connected tags.
     *
     * @var TdbCmsTagsList|null
     */
    protected $oConnectedMLTRecords;

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
     * @var string|null - "__en"
     */
    protected $sLanguageIsoName;

    public function GetHTML()
    {
        if (null === $this->oConnectedMLTRecords) {
            $this->oConnectedMLTRecords = $this->FetchConnectedMLTRecords();
        }

        $dataAjaxSuggestionsUrl = '';
        if (true === $this->bShowSuggestions) {
            $dataAjaxSuggestionsUrl = 'data-ajax-suggestions-url="'.$this->getTagSuggestionsUrl().'"';
        }

        $selectBox = '<select id="%s" name="%s[]" data-select2-ajax="%s" class="form-control form-control-sm" data-tags="true" multiple="multiple" %s>';
        $html = sprintf($selectBox, TGlobal::OutHTML($this->name), TGlobal::OutHTML($this->name), $this->getTagAutocompleteUrl(), $dataAjaxSuggestionsUrl);

        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $this->oConnectedMLTRecords->SetLanguage($backendSession->getCurrentEditLanguageId());
        while ($oMLTRecord = $this->oConnectedMLTRecords->Next()) {
            $html .= '<option selected="selected">'.TGlobal::OutHTML($oMLTRecord->GetDisplayValue()).'</option>';
        }

        $html .= '</select>';

        if ($this->bShowSuggestions) {
            $html .= '<p id="'.TGlobal::OutHTML($this->name).'_suggestions" class="mt-2 tagSuggestions">
<span class="label">'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select_tag.suggestions')).': </span>
<span class="tagSuggestionList"></span>
</p>';
        }

        return $html;
    }

    private function getAjaxUrlParameters(): array
    {
        $tableConf = $this->oTableRow->GetTableConf();

        return [
            'pagedef' => 'tableeditor',
            '_rmhist' => 'false',
            'module_fnc[contentmodule]' => 'ExecuteAjaxCall',
            'callFieldMethod' => '1',
            'id' => $this->recordId,
            'tableid' => $tableConf->id,
            '_fieldName' => $this->name,
        ];
    }

    private function getTagAutocompleteUrl(): string
    {
        $urlParameter = $this->getAjaxUrlParameters();
        $urlParameter['_fnc'] = 'getAutocompleteTagList';

        return $this->getUrlUtil()->getArrayAsUrl($urlParameter, PATH_CMS_CONTROLLER.'?', '&');
    }

    private function getTagSuggestionsUrl(): string
    {
        $urlParameter = $this->getAjaxUrlParameters();
        $urlParameter['_fnc'] = 'GetTagSuggestions';

        return $this->getUrlUtil()->getArrayAsUrl($urlParameter, PATH_CMS_CONTROLLER.'?', '&');
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['GetTagList', 'GetTagSuggestions', 'getAutocompleteTagList'];
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
        if (null !== $this->sLanguageIsoName) {
            return $this->sLanguageIsoName;
        }

        if (false === TdbCmsTags::CMSFieldIsTranslated('name')) {
            return '';
        }

        $cmsConfig = TdbCmsConfig::GetInstance();
        $baseLanguageId = $cmsConfig->fieldTranslationBaseLanguageId;
        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $activeLanguageId = $backendSession->getCurrentEditLanguageId();
        if ($baseLanguageId !== $activeLanguageId) {
            return '__'.$this->getLanguageService()->getLanguageIsoCode($activeLanguageId);
        }

        return '';
    }

    public function getAutocompleteTagList(): array
    {
        $returnVal = [];

        $tagList = $this->GetTagList();
        foreach ($tagList as $tagItem) {
            $tag = $tagItem['tag'];
            $returnVal[] = ['id' => $tag, 'text' => $tag];
        }

        return $returnVal;
    }

    /**
     * this method is called via ajax for autocompletion.
     *
     * @return array
     */
    public function GetTagList()
    {
        $aFoundTags = [];
        $oGlobal = TGlobal::instance();

        $inputFilter = $this->getInputFilterUtil();

        $searchTerm = $inputFilter->getFilteredGetInput('q', '');

        if ('' === $searchTerm) {
            return [];
        }

        $searchTerm = strtolower(trim($searchTerm));

        $sLanguageIsoName = $this->GetLanguageSuffix();
        $sTagBlackListQueryPart = '';

        $currentTags = $inputFilter->getFilteredGetInput('currentTags', '');

        if ('' !== $currentTags) {
            $currentTagsList = explode(',', $currentTags);
            if (count($currentTagsList) > 0) {
                $tagBlackList = '';
                foreach ($currentTagsList as $currentTag) {
                    $tagBlackList .= "'".MySqlLegacySupport::getInstance()->real_escape_string(trim(strtolower($currentTag)))."',";
                }
                $tagBlackList = substr($tagBlackList, 0, -1);

                $sTagBlackListQueryPart = ' AND `name'.$sLanguageIsoName.'` NOT IN ('.$tagBlackList.')';
            }
        }

        $query = 'SELECT DISTINCT *
              FROM `cms_tags`
              WHERE `name'.MySqlLegacySupport::getInstance()->real_escape_string($sLanguageIsoName)."` LIKE '".MySqlLegacySupport::getInstance()->real_escape_string($searchTerm)."%' ".$sTagBlackListQueryPart;

        $query .= ' ORDER BY `name` ASC';
        $iLimit = $this->iMaxAutocompleteTags;
        if ($oGlobal->UserDataExists('limit')) {
            $iLimit = $oGlobal->GetUserData('limit');
        }
        $query .= ' LIMIT '.MySqlLegacySupport::getInstance()->real_escape_string($iLimit);
        $oRecordList = TdbCmsTagsList::GetList($query);

        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $oRecordList->SetLanguage($backendSession->getCurrentEditLanguageId());

        while ($oRecord = $oRecordList->Next()) {
            $iTagUsageCount = $oRecord->fieldCount;
            $name = $oRecord->GetName();
            if (!empty($name)) {
                $aFoundTags[] = ['tag' => $name, 'freq' => $iTagUsageCount];
            }
        }

        return $aFoundTags;
    }

    /**
     * {@inheritdoc}
     */
    public function PostSaveHook($iRecordId)
    {
        $currentRecord = $this->oTableRow;

        // save tags
        $tags = $this->data;

        if (false === is_array($tags)) {
            $tags = [];
        }

        $oTagTableConf = new TCMSTableConf();
        $oTagTableConf->LoadFromField('name', 'cms_tags');

        // loop through tags and insert missing tags to database
        $aTagIDs = [];
        foreach ($tags as $tag) {
            $cleanTag = trim(strtolower($tag));
            if ('' !== $cleanTag) {
                // perform case insensitive search for existing tag
                $sLanguageIsoName = $this->GetLanguageSuffix();
                $checkQuery = 'SELECT * FROM `cms_tags` WHERE `name'.MySqlLegacySupport::getInstance()->real_escape_string($sLanguageIsoName)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($cleanTag)."'";
                $checkResult = MySqlLegacySupport::getInstance()->query($checkQuery);
                if (MySqlLegacySupport::getInstance()->num_rows($checkResult) < 1) {
                    // tag not yet in database, so perform an insert
                    $oTableManager = new TCMSTableEditorManager();
                    $oTableManager->Init($oTagTableConf->id, null);
                    $aDefaultData = ['name' => $cleanTag, 'urlname' => $this->getUrlNormalizationUtil()->normalizeUrl($cleanTag)];
                    $oTableManager->Save($aDefaultData);
                    $aTagIDs[] = $oTableManager->sId;
                } else {
                    $row = MySqlLegacySupport::getInstance()->fetch_assoc($checkResult);
                    $aTagIDs[] = $row['id'];
                }
            }
        }

        // perform mlt changes
        if (is_array($currentRecord->sqlData) && array_key_exists('id', $currentRecord->sqlData) && !empty($currentRecord->sqlData['id'])) {
            $recordId = $currentRecord->sqlData['id'];
            $mltTableName = $this->GetMLTTableName();

            $oTableConf = new TCMSTableConf();
            $oTableConf->LoadFromField('name', $this->sTableName);

            /** @var $aConnectedIds array */
            $aConnectedIds = [];

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
        return $this->ConvertDataToFieldBasedData($this->ConvertPostDataToSQL());
    }

    /**
     * you may overwrite this method for custom tag suggestions
     * returns multidimensional array sorted by tag frequence array(array('tag'='fooBaa','count'=>3)).
     *
     * @return array
     */
    public function GetTagSuggestions()
    {
        $aTagSuggestions = [];

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
        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $aTagSuggestionsFinal = [];
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
                $oSimilarTagsList = TdbCmsTagsList::GetList($sMLTQuery, $backendSession->getCurrentEditLanguageId());
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
            $aTagSuggestionsFinal = [];
            $i = 0;
            foreach ($aTagSuggestions as $sTag => $iCount) {
                ++$i;
                if ($i == $this->iMaxTagSuggestions) {
                    break;
                }
                $aTagSuggestionsFinal[] = ['id' => $sTag, 'name' => $sTag];
            }
        }

        return $aTagSuggestionsFinal;
    }

    /**
     * {@inheritdoc}
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

    /**
     * @return string
     */
    protected function GetCleanedTagString()
    {
        $sReturnTags = '';
        $aTagData = explode(',', $this->data);
        if (count($aTagData) > 0) {
            $aValidTags = [];
            foreach ($aTagData as $sTagName) {
                if ('' !== trim($sTagName)) {
                    $aValidTags[] = trim($sTagName);
                }
            }
            $sReturnTags = implode(',', $aValidTags);
        }

        return $sReturnTags;
    }

    private function getUrlNormalizationUtil(): UrlNormalizationUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}
