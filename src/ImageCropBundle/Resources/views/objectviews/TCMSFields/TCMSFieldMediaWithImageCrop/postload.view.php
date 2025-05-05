<?php
/**
 * @var $sFieldFullName              string
 * @var $sFieldVisibility            string
 * @var $sFieldDatabaseName          string
 * @var $additionalFieldName         string
 * @var $additionalFieldPropertyName string
 * @var $oDefinition                 TCMSFieldDefinition
 */
?>
if (isset($this->sqlData['<?php echo $sFieldDatabaseName; ?>'])) {
<?php
$fieldTranslationIsActive = true === ACTIVE_TRANSLATION && isset($oDefinition->sqlData['is_translatable']) && '1' === $oDefinition->sqlData['is_translatable'];
if ($fieldTranslationIsActive) {
    // in the case of an image field, we assume a translation that is the default value (= placeholder image) should have a fallback to the base language in frontend
    ?>    $translatedFieldName = '<?php echo $sFieldDatabaseName; ?>__'.$sActiveLanguagePrefix;
    if (isset($this->sqlData[$translatedFieldName]) && $this->sqlData[$translatedFieldName] === '<?php echo $oDefinition->sqlData['field_default_value']; ?>') {
    $this->sqlData[$translatedFieldName] = '';
    }
<?php }
if ($fieldTranslationIsActive) {
    /* Note: we do not need to know the base language - we just avoid copying content if the __LN field does not exist */ ?>
    $this->transformFieldTranslation('<?php echo $sFieldDatabaseName; ?>',$sActiveLanguagePrefix);
    if (isset($this->sqlData[$translatedFieldName]) && '' === $this->sqlData[$translatedFieldName]) {
    $this->sqlData[$translatedFieldName] = '<?php echo $oDefinition->sqlData['field_default_value']; ?>';
    }
    <?php
}
?>
$this-><?php echo $sFieldName; ?> = $this->sqlData['<?php echo $sFieldDatabaseName; ?>'];
}
if (isset($this->sqlData['<?php echo $additionalFieldName; ?>'])) {
<?php
if ($fieldTranslationIsActive) {
    ?>
    $translatedFieldName = '<?php echo $additionalFieldName; ?>__'.$sActiveLanguagePrefix;
    if (isset($this->sqlData[$translatedFieldName]) && $this->sqlData['<?php echo $sFieldDatabaseName; ?>__'.$sActiveLanguagePrefix] !== '' && $this->sqlData['<?php echo $sFieldDatabaseName; ?>__'.$sActiveLanguagePrefix] !== '<?php echo $oDefinition->sqlData['field_default_value']; ?>') { //only if image itself has translated value
    $this->sqlData['<?php echo $additionalFieldName; ?>'] = $this->sqlData[$translatedFieldName];
    }
    <?php
} ?>
$this-><?php echo $additionalFieldPropertyName; ?> = $this->sqlData['<?php echo $additionalFieldName; ?>'];
}
