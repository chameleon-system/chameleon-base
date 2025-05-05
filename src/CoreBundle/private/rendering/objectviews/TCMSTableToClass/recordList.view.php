<?php echo "<?php\n"; ?>
/**
* THIS FILE IS CREATED BY CHAMELEON. DO NOT CHANGE IT BY HAND! IT WILL BE OVERWRITTEN BY
* CHAMELEON ANYTIME A CHANGE IS MADE TO THE CONNECTED TABLE. IF YOU NEED TO MODIFY THE CLASS
* YOU MUST USE ITS EXTENSION IN CMSDataObjects
*/

/****************************************************************************
* Copyright <?php echo $year; ?> by ESONO AG, Freiburg, Germany
<?php
if (is_array($aTableNotes)) {
    foreach ($aTableNotes as $sLine) {
        echo "  * {$sLine}\n";
    }
}

$sTempTableName = '_tmp_'.$sTableDBName;
?>
****************************************************************************/

/**
 * @extends TCMSRecordList<<?php echo $sClassName; ?>>
 */
class <?php echo $sAutoClassName; ?>List extends TCMSRecordList
{
    /**
     * @var bool
     */
    public $bChangedDataChanged = false;

    /**
     * limit the result set to this number of records (-1 = no restriction).
     * @var int $iLimitResultSet
     */
    protected $iLimitResultSet = <?php if (is_array($aTableConf) && array_key_exists('auto_limit_results', $aTableConf)) {
        echo $aTableConf['auto_limit_results'];
    } else {
        echo '-1';
    } ?>;

    /**
     * create a new instance
     *
     * @param string $sQuery
     * @param string $sLanguageId
     */
    public function __construct($sQuery=null, $sLanguageId=null)
    {
        $this->sTableObject = '<?php echo $sClassName; ?>';
        parent::__construct($this->sTableObject, '<?php echo $sTableDBName; ?>',$sQuery,$sLanguageId);
    }

    /**
     * return an instance for the query passed
     *
     * @param string $sQuery - custom query instead of default query
     * @param string $iLanguageId - the language id for record overloading
     * @param boolean $bAllowCaching - set this to true if you want to cache the record list object
     * @param boolean $bForceWorkflow - (deprecated) set this to true to force adding the workflow query part even in cms backend mode
     * @param boolean $bUseGlobalFilterInsteadOfPreviewFilter - (deprecated) set this to true if you want to overload all workflow data instead of only the records that are marked for preview
     * @return <?php echo $sClassName; ?>List
     */
    static public function GetList($sQuery=null,$iLanguageId=null, $bAllowCaching = false, $bForceWorkflow = false, $bUseGlobalFilterInsteadOfPreviewFilter = false): <?php echo $sClassName; ?>List
    {
        if (null === $iLanguageId) {
            $iLanguageId = self::getMyLanguageService()->getActiveLanguageId();
        }
        if (null === $sQuery) {
            $sQuery = <?php echo $sClassName; ?>List::GetDefaultQuery($iLanguageId);
        }
        $oList = new <?php echo $sClassName; ?>List(); /** @var $oList <?php echo $sClassName; ?>List*/
        $oList->bAllowItemCache = $bAllowCaching;
        $oList->SetLanguage($iLanguageId);
        $sQuery = self::getFieldTranslationUtil()->getTranslatedQuery($sQuery);
        $oList->Load($sQuery);

        if ($bAllowCaching) $oList->bChangedDataChanged = true;

        return $oList;
    }

    /**
     * return default query for the table
     * @param int $iLanguageId - language used for query
     * @param string $sFilterString - any filter conditions to add to the query
     * @return string
     */
    static public function GetDefaultQuery($iLanguageId, $sFilterString='1=1'): string
    {
        $sDefaultQuery = "<?php echo $sCMSListQuery; ?>";
        $sDefaultQuery = str_replace('[{sFilterConditions}]',$sFilterString,$sDefaultQuery);
        return $sDefaultQuery;
    }

    /**
     * returns the current item from the list without changing the pointer
     * if we are at the end of the record, then the function will return false (like after GoToLast)
     * if we are at the start of the record (like after GoToStart), then it will return the first element
     *
     * @return false|<?php echo $sClassName."\n"; ?>
     */
    public function Current(): bool|<?php echo $sClassName."\n"; ?>
    {
        return parent::Current();
    }

    /**
     * returns the next element from the list, moving the pointer to the next
     * record
     *
     * @return false|<?php echo $sClassName."\n"; ?>
     */
    public function Next(): bool|<?php echo $sClassName."\n"; ?>
    {
        return parent::Next();
    }

    /**
     * returns the previous record from the list, moving the pointer back one
     *
     * @return false|<?php echo $sClassName."\n"; ?>
     */
    public function Previous(): bool|<?php echo $sClassName."\n"; ?>
    {
        return parent::Previous();
    }

<?php
    $oFields->GoToStart();
while ($oField = $oFields->Next()) {
    $sProp = $oField->RenderFieldListMethodsString();
    if (!empty($sProp)) {
        echo $sProp."\n";
    }
}
?>
    /**
     * factory returning an element for the list
     *
     * @param array $aData
     * @return <?php echo $sClassName; ?>
     */
    protected function _NewElement($aData): <?php echo $sClassName; ?>
    {
        $this->bChangedDataChanged = true;
        $oObj = <?php echo $sClassName; ?>::GetNewInstance($aData,$this->iLanguageId);
        return $oObj;
    }

}

