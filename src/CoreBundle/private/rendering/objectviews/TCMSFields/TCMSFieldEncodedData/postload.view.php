//- decode data
if (isset($this->sqlData['<?= $sFieldDatabaseName; ?>'])) {
if (!isset($this->sqlData['<?= $sFieldDatabaseName; ?>__decoded']) || empty($this->sqlData['<?= $sFieldDatabaseName; ?>__decoded'])) {
$sDecodedData = $this->sqlData['<?= $sFieldDatabaseName; ?>'];
if(defined('CMSFIELD_DATA_ENCODING_KEY')) {
$sKey = CMSFIELD_DATA_ENCODING_KEY;
if(!empty($sKey)) {
$sKey = str_rot13($sKey);
//decode data
if (false === is_object($this->sqlData['<?= $sFieldDatabaseName; ?>']) && false === is_array($this->sqlData['<?= $sFieldDatabaseName; ?>'])) {
$sQry = "SELECT DECODE(".$this->getDatabaseConnection()->quote($this->sqlData['<?= $sFieldDatabaseName; ?>']).",".$this->getDatabaseConnection()->quote($sKey).") AS encoded_value ";

$aDecodedData = $this->getDatabaseConnection()->fetchAssoc($sQry);
$sDecodedData = $aDecodedData['encoded_value'];
} else {
$sDecodedData = $this->sqlData['<?= $sFieldDatabaseName; ?>'];
}
}
}
if($sDecodedData == '') {
$sDecodedData = $this->sqlData['<?= $sFieldDatabaseName; ?>'];
}else{
if ($sDecodedData === serialize(false)) $sDecodedData = false; // special case - false was serialized
else {
$sTmpCleanData = @unserialize($sDecodedData);
if ($sTmpCleanData !== false) {
$sDecodedData = $sTmpCleanData;
}
}
}
$this->sqlData['<?= $sFieldDatabaseName; ?>'] = $sDecodedData;
$this->sqlData['<?= $sFieldDatabaseName; ?>__decoded'] = time();
}
$this-><?= $sFieldName; ?> = $this->sqlData['<?= $sFieldDatabaseName; ?>'];
}

