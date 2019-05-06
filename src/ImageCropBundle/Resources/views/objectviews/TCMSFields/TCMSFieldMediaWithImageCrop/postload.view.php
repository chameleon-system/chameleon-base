<?php
/**
 * @var $sFieldFullName              string
 * @var $sFieldVisibility            string
 * @var $sFieldDatabaseName          string
 * @var $additionalFieldName         string
 * @var $additionalFieldPropertyName string
 * @var $oDefinition                 TCMSFieldDefinition
 */
if (isset($oDefinition->sqlData['is_translatable']) && '1' == $oDefinition->sqlData['is_translatable']) {
    //in the case of an image field, we assume a translation that is the default value (= placeholder image) should have a fallback to the base language in frontend ?>$key = '<?= $sFieldDatabaseName; ?>__' . $sActiveLanguagePrefix;
    if (isset($this->sqlData[$key]) && $this->sqlData[$key] === '<?= $oDefinition->sqlData['field_default_value']; ?>') {
    $this->sqlData[$key] = '';
    }<?php
} ?>
if (isset($this->sqlData['<?= $sFieldDatabaseName; ?>'])) {
<?php
if (true === ACTIVE_TRANSLATION && isset($oDefinition->sqlData['is_translatable']) && '1' == $oDefinition->sqlData['is_translatable']) {
        /*Note: we do not need to know the base language - we just avoid copying content if the __LN field does not exist */ ?>
    $this->transformFieldTranslation('<?= $sFieldDatabaseName; ?>',$sActiveLanguagePrefix);
    <?php
    }
?>
$this-><?= $sFieldName; ?> = $this->sqlData['<?= $sFieldDatabaseName; ?>'];
}
if (isset($this->sqlData['<?= $additionalFieldName; ?>'])) {
<?php
if (isset($oDefinition->sqlData['is_translatable']) && '1' == $oDefinition->sqlData['is_translatable']) {
    ?>
    if (isset($this->sqlData['<?= $additionalFieldName; ?>__'.$sActiveLanguagePrefix]) && $this->sqlData['<?= $sFieldDatabaseName; ?>__' . $sActiveLanguagePrefix] !== '' && $this->sqlData['<?= $sFieldDatabaseName; ?>__' . $sActiveLanguagePrefix] !== '<?= $oDefinition->sqlData['field_default_value']; ?>') { //only if image itself has translated value
    $this->sqlData['<?= $additionalFieldName; ?>'] = $this->sqlData['<?= $additionalFieldName; ?>__'.$sActiveLanguagePrefix];
    }
    <?php
} ?>
$this-><?= $additionalFieldPropertyName; ?> = $this->sqlData['<?= $additionalFieldName; ?>'];
}