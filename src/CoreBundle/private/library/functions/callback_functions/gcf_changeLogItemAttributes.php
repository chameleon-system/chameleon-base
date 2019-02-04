<?php

function gcf_changeLogItemAttributes($field, $row, $fieldName)
{
    $encodedPayload = base64_encode($row[$fieldName]);
    return sprintf('<span data-field-restorable-value="%s">%s</span>', $encodedPayload, $field);
}