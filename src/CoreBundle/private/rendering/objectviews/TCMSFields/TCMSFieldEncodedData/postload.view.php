//- decode data
if (isset($this->sqlData['<?php echo $sFieldDatabaseName; ?>'])) {
if (!isset($this->sqlData['<?php echo $sFieldDatabaseName; ?>__decoded']) || empty($this->sqlData['<?php echo $sFieldDatabaseName; ?>__decoded'])) {
$sDecodedData = $this->sqlData['<?php echo $sFieldDatabaseName; ?>'];
if(defined('CMSFIELD_DATA_ENCODING_KEY')) {
$sKey = CMSFIELD_DATA_ENCODING_KEY;
if(!empty($sKey)) {
$sKey = str_rot13($sKey);
//decode data
if (false === is_object($this->sqlData['<?php echo $sFieldDatabaseName; ?>']) && false === is_array($this->sqlData['<?php echo $sFieldDatabaseName; ?>'])) {
$sQry = "SELECT DECODE(".$this->getDatabaseConnection()->quote($this->sqlData['<?php echo $sFieldDatabaseName; ?>']).",".$this->getDatabaseConnection()->quote($sKey).") AS encoded_value ";

$aDecodedData = $this->getDatabaseConnection()->fetchAssociative($sQry);
$sDecodedData = $aDecodedData['encoded_value'];
} else {
$sDecodedData = $this->sqlData['<?php echo $sFieldDatabaseName; ?>'];
}
}
}
if($sDecodedData == '') {
$sDecodedData = $this->sqlData['<?php echo $sFieldDatabaseName; ?>'];
}else{
if ($sDecodedData === serialize(false)) $sDecodedData = false; // special case - false was serialized
elseif (is_string($sDecodedData)) {
$sTmpCleanData = @unserialize($sDecodedData);
if ($sTmpCleanData !== false) {
$sDecodedData = $sTmpCleanData;
}
}
}
$this->sqlData['<?php echo $sFieldDatabaseName; ?>'] = $sDecodedData;
$this->sqlData['<?php echo $sFieldDatabaseName; ?>__decoded'] = time();
}
$this-><?php echo $sFieldName; ?> = $this->sqlData['<?php echo $sFieldDatabaseName; ?>'];
}

