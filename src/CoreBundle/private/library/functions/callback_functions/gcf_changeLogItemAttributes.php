<?php

function gcf_changeLogItemAttributes($field, $row, $fieldName)
{
    $originalFieldValue = unserialize($row[$fieldName], ['allowed_classes' => []]);
    $serializedFieldPayload = json_encode(['value' => $originalFieldValue], JSON_UNESCAPED_UNICODE);
    $encodedFieldPayload = htmlspecialchars($serializedFieldPayload, ENT_QUOTES);

    return sprintf('<span data-field-restorable-value="%s">%s</span>', $encodedFieldPayload, $originalFieldValue);
}