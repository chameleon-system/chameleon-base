<h1>Build #1701162864</h1>
<h2>Date: 2023-11-28</h2>
<div class="changelog">
    <ul>
        <li>- ref #832: add preventReferenceCopy to property field ("Eigenschaften")</li>
        <li>- ref #832: add default preventReferenceCopy=true to pkg_article_category_group table's pkg_article_category field</li>
    </ul>
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
    ->setFields(
        [
            'help_text' => '<div class="field-name"><strong>Feldname:</strong> ZIELTABELLE oder beliebig, dann ist ein Feldkonfigurationsparameter nötig</div>

            <div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldPropertyTable extends TCMSFieldVarchar</div>

            <div>Erzeugt eine ein-/ausklappbare Liste, in der alle Datensätze angezeigt werden, deren Parent Key dem aktuellen Datensatz entspricht.</div>

            <div>Stellt eine Verbindung zu einer Tabelle her, bei der die Datensätze exklusiv verbunden werden. Das bedeutet, dass in verknüpften Datensätzen automatisch die ID des erzeugten Datensatzes (parent ID) hinterlegt wird.</div>

            <div>&nbsp;</div>

            <div class="important">
            <div><strong>Wichtig:</strong> Der Name des Feldes muss exakt dem Namen der zu verknüpfenden Tabelle entsprechen.</div>

            <div>In der zu verknüpfenden Tabelle muss es ein Feld vom Typ "Parent Key" geben.</div>

            <div>Zusätzlich sollte als Standardwert der Name der Zieltabelle gesetzt werden.</div>

            <div>Optionale Parameter beachten, falls der Name des Feldes nicht gleich dem Namen der Zieltabelle sein kann oder es in der Zieltabelle mehr als einen Parent Key auf diese Tabelle gibt und der Parent Key somit nicht eindeutig ist.</div>
            </div>

            <div>&nbsp;</div>

            <div>
            <ul>
                <li class="parameter required head">Pflicht-Parameter:</li>
                <li>
                <ul>
                    <li class="parameter required">unter bestimmten Umständen (siehe Text und Optionale Parameter)</li>
                </ul>
                </li>
                <li>&nbsp;</li>
                <li class="parameter optional head">Optionale Parameter:</li>
                <li>
                <ul>
                    <li class="parameter optional"><strong>bOnlyOneRecord=true</strong> - öffnet die Zieltabelle als 1:1 Verbindung.</li>
                    <li class="parameter optional"><strong>bOpenOnLoad=true</strong> - beim Aufruf des Editors wird die Liste bereits geöffnet angezeigt.</li>
                    <li class="parameter optional"><strong>connectedTableName=ZIELTABELLE</strong> - der Tabellenname der Zieltabelle ohne _id. Dieser Parameter muss gesetzt werden, wenn mehrere Felder in der Tabelle mit der gleichen Zieltabelle verknüpft werden sollen. Dadurch kann ein beliebiger Feldname vergeben werden.</li>
                    <li class="parameter optional"><strong>fieldNameInConnectedTable=NAME-DES-FELDES-IN-ZIELTABELLE</strong> - muss gesetzt werden, wenn sich in der Zieltabelle mehr als ein Parent Key auf die aktuelle Tabelle bezieht.</li>
                    <li class="parameter optional"><strong>preventReferenceDeletion=true </strong>- Eigenschaften werden nicht gelöscht sollte der Parent gelöscht werden.</li>
                    <li class="parameter optional"><strong>preventReferenceCopy=true </strong>- Eigenschaften werden nicht kopiert sollte der Parent kopiert werden.</li>
                </ul>
                </li>
            </ul>
            </div>
            ',
        ]
    )
    ->setWhereEquals(
        [
            'constname' => 'CMSFIELD_PROPERTY',
        ]
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
    ->setFields(
        [
            'help_text' => '<div class="field-name"><strong>Field name:</strong>&nbsp;TARGETTABLE or any other, then a field configuration parameter is required</div>

            <div class="php-class"><strong>PHP class:</strong> TCMSFieldPropertyTable extends TCMSFieldVarchar</div>

            <div>Creates a collapsing list that displays&nbsp;all records&nbsp;whose parent key corresponds to the current record.</div>

            <div>Connects to a table where the records are linked exclusively.&nbsp;This means that the ID of the generated record (parent ID) is automatically stored in linked data records.</div>

            <div>&nbsp;</div>

            <div class="important">
            <div><strong>Important:</strong>&nbsp;The name of the field must correspond exactly to the name of the table to be linked.</div>

            <div>The table to be linked must contain a field of the type "parent key".</div>

            <div>In addition, the name of the target table should be set as default value.</div>

            <div>Note optional parameters, if the name of the field cannot be the same as the name of the target table, or if the target table contains more than one parent key on this table, and thus, the parent key is not unique.</div>
            </div>

            <div>&nbsp;</div>

            <div>
            <ul>
                <li class="parameter required head">Required parameters:</li>
                <li>
                <ul>
                    <li class="parameter required">under certain circumstances&nbsp;(see text and optional parameters)</li>
                </ul>
                </li>
                <li>&nbsp;</li>
                <li class="parameter optional head">Optional&nbsp;parameters:</li>
                <li>
                <ul>
                    <li class="parameter optional"><strong>bOnlyOneRecord=true</strong> - opens the target table as 1:1 connection.</li>
                    <li class="parameter optional"><strong>bOpenOnLoad=true</strong> - the list is already open when the editor is called.</li>
                    <li class="parameter optional"><strong>connectedTableName=TARGETTABLE&nbsp;</strong>- the table name of the target table without _id. This parameter must be set, if multiple fields of the table shall be linked to the same target table. In this way one can assign any field name.</li>
                    <li class="parameter optional"><strong>fieldNameInConnectedTable=NAME-OF-THE-FIELD-IN-TARGETTABLE</strong>&nbsp;- must be set, if more than one parent key in the target table is referenced to the current table.</li>
                    <li class="parameter optional"><strong>preventReferenceDeletion=true </strong>- Properties will not be deleted, in case the parent is deleted.</li>
                    <li class="parameter optional"><strong>preventReferenceCopy=true </strong>- Properties will not be copied, in case the parent is copied.</li>
                </ul>
                </li>
            </ul>
            </div>
            ',
        ]
    )
    ->setWhereEquals(
        [
            'constname' => 'CMSFIELD_PROPERTY',
        ]
    );
TCMSLogChange::update(__LINE__, $data);


$confId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('pkg_article_category_group'), 'pkg_article_category');
// We need the old fieldtype_config to merge it with the new one
$oldFieldTypeConfig = TCMSLogChange::getDatabaseConnection()->fetchOne(
    'SELECT fieldtype_config FROM `cms_field_conf` WHERE `id` = :id',
    ['id' => $confId]

);
if (false === $oldFieldTypeConfig) {
    $oldFieldTypeConfig = '';
}
$newFieldTypeConfig = trim($oldFieldTypeConfig."\n"."preventReferenceCopy=true", "\n");

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'fieldtype_config' => $newFieldTypeConfig,
  ])
  ->setWhereEquals([
      'id' => $confId,
  ])
;
TCMSLogChange::update(__LINE__, $data);
