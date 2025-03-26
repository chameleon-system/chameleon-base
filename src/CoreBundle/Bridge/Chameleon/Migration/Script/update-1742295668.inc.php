<h1>Build #1742295668</h1>
<h2>Date: 2025-03-18</h2>
<div class="changelog">
    - add missing field type for multi table list restriction
</div>
<?php

$cmsFieldMultiTableListRestrictionId = '35113f9b-5b59-1823-e6d9-a335ecd3f4c3';

$connection = TCMSLogChange::getDatabaseConnection();

if (0 === TCMSLogChange::GetFieldType('CMSFIELD_MULTITABLELIST_RESTRICTION')) {
    $data = $connection->fetchAssociative(
        'SELECT * FROM cms_field_type WHERE id=?',
        [$cmsFieldMultiTableListRestrictionId]
    );
    if (false !== $data) {
        $connection->delete('cms_field_type', ['id' => $cmsFieldMultiTableListRestrictionId]);
        $data['id'] = \TTools::GetUUID();
        $connection->insert('cms_field_type', $data);
        $connection->update(
            'cms_field_conf',
            ['cms_field_type_id' => $data['id']],
            ['cms_field_type_id' => $cmsFieldMultiTableListRestrictionId]
        );
    }

    $data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
        ->setFields([
            'id' => $cmsFieldMultiTableListRestrictionId,
            '049_trans' => 'Werteliste aus Tabelle für Einschränkungen (Mehrfach-Auswahl möglich)',
            'mysql_standard_value' => '50',
            'force_auto_increment' => '0',
            'fieldclass' => 'TCMSFieldLookupMultiSelectRestriction',
            'indextype' => 'none',
            'constname' => 'CMSFIELD_MULTITABLELIST_RESTRICTION',
            'mysql_type' => '',
            'length_set' => '',
            'base_type' => 'mlt',
            'help_text' => '<div class="field-name">
<div class="field-name"><strong>Feldname:</strong> ZIELTABELLE_mlt oder beliebig, dann ist ein Feldkonfigurationsparameter nötig</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldLookupMultiSelectRestriction extends TCMSFieldLookupMultiselect</div>

<div>Erzeugt eine ein-/ausklappbare Liste, in der alle verknüpften Datensätze der Zieltabelle angezeigt werden.</div>

<div>Diese ermöglicht die Auswahl mehrerer Datensätze aus einer verknüpften Tabelle (PopUp). In den Datensätzen kann gesucht werden.</div>

<div>Erstellt eine zusätzliche Tabelle "TABELLE-DES-FELDES_ZIELTABELLE_mlt" und ein zusätzliches Feld "NAME-DES-FELDES_inverse_empty", dass für die Logik bei einer leeren Auswahl zuständig ist.</div>

<div>Um die Option "Logik bei leerer Liste umkehren" bei einer leeren Auswahl zu nutzen, müssen die vom Feldtyp bereitgestellten Funktionen "GetField...WithInverseEmptySelectionLogicList" oder&nbsp; "GetField...WithInverseEmptySelectionLogicIdList" verwendet werden. Ist das Feld aktiviert, liefern die Funktionen den Wert null zurück sollte keine Auswahl gemacht worden sein. Ist das Feld deaktiviert, liefern die Funktionen eine leere Liste sollte keine Auswahl gemacht worden sein.</div>

<div class="important"><strong>Wichtig:</strong> Der Name des Feldes muss exakt dem Namen der zu verknüpfenden Tabelle entsprechen, optional gefolgt von "_mlt" (ZIELTABELLE_mlt), es sei denn, der Parameter connectedTableName wird definiert (weitere Informationen unten).</div>

<div class="important">Bei der Verwendung des Parameters connectedTableName, darf der Feldnamen nicht auf _mlt enden.</div>

<div class="important">Allerdings ist es nicht möglich hierbei einen Namen anzugeben, der insgesamt länger als 64-Zeichen ist, da dieser in MySQL nicht korrekt gespeichert werden kann und somit zu Fehlern führt.</div>

<div>&nbsp;</div>

<div>
<ul>
	<li class="parameter required head">Pflicht-Parameter:</li>
	<li>
	<ul>
		<li class="parameter required">unter bestimmten Umständen (siehe Text und optionale Parameter)</li>
	</ul>
	</li>
	<li>&nbsp;</li>
	<li class="parameter optional head">Optionale Parameter:</li>
	<li>
	<ul>
		<li class="parameter optional"><strong>connectedTableName=ZIELTABELLE</strong> - der Tabellenname der Zieltabelle. Dieser Parameter muss gesetzt werden, wenn mehrere Felder in der Tabelle mit der gleichen Zieltabelle verknüpft werden sollen. Dadurch kann ein beliebiger Feldname vergeben werden.</li>
		<li class="parameter optional"><strong>bOpenOnLoad=true</strong> - beim Aufruf des Editors wird die Liste bereits geöffnet angezeigt.</li>
		<li class="parameter optional"><strong>bAllowCustomSortOrder=true</strong> - gibt an, ob das Feld manuell sortierbar ist. Sonstige eingestellte Sortierungen werden verworfen.</li>
	</ul>
	</li>
</ul>
</div>
</div>
',
            'contains_images' => '0',
        ]);
    TCMSLogChange::insert(__LINE__, $data);
}

$fieldsToBeUpdated = [
        'shop_article_mlt',
        'shop_category_mlt',
        'data_extranet_group_mlt',
        'data_extranet_user_mlt',
];

foreach($fieldsToBeUpdated as $fieldName) {
    if (
        true === TCMSLogChange::TableExists('shop_discount')
        && true === TCMSLogChange::FieldExists(
            'shop_discount',
            $fieldName
        )
    ) {
        $mltFieldId = TCMSLogChange::GetTableFieldId(
            TCMSLogChange::GetTableId('shop_discount'),
            $fieldName
        );
        $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
            ->setFields([
                'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_MULTITABLELIST_RESTRICTION'),
            ])
            ->setWhereEquals([
                'id' => $mltFieldId,
            ]);
        TCMSLogChange::update(__LINE__, $data);
    }
}




