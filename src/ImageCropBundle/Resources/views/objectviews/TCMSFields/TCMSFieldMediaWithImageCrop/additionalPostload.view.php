<?php
/**
 * @var $sFieldFullName              string
 * @var $sFieldVisibility            string
 * @var $additionalFieldName         string
 * @var $additionalFieldPropertyName string
 * @var $oDefinition                 TCMSFieldDefinition
 */
?>
if (isset($this->sqlData['<?= $additionalFieldName; ?>'])) {
<?php
if (isset($oDefinition->sqlData['is_translatable']) && '1' == $oDefinition->sqlData['is_translatable']) {
    ?>
    $this->transformFieldTranslation('<?= $additionalFieldName; ?>',$sActiveLanguagePrefix);
<?php
} ?>
    $this-><?= $additionalFieldPropertyName; ?> = $this->sqlData['<?= $additionalFieldName; ?>'];
}