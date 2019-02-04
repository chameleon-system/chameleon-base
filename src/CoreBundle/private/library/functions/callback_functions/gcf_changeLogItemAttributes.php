<?php

function gcf_changeLogItemAttributes($field, $row, $fieldName)
{
    $originalFieldValue = unserialize($row[$fieldName], ['allowed_classes' => []]);
    $encodedPayload = base64_encode($originalFieldValue);

    return sprintf('<span data-field-restorable-value="%s">%s</span>', $encodedPayload, $originalFieldValue);
}