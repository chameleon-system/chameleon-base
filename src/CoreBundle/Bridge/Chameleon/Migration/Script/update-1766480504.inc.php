<h1>Build #1766480504</h1>
<h2>Date: 2025-12-23</h2>
<div class="changelog">
    - ref #69054: add backend messages for validation of configured MLT tables <br>
    - update field type descriptions with new configuration key name
</div>
<?php

TCMSLogChange::AddBackEndMessage(\TCMSFieldLookupMultiselect::TABLEEDITOR_TABLE_NAME_MUST_END_WITH_MLT_MESSAGE_NAME, 'Der Tabellenname "[{tableName}]" in der muss auf "_mlt" enden.');
TCMSLogChange::AddBackEndMessage(\TCMSFieldLookupMultiselect::TABLEEDITOR_TABLE_NAME_TOO_LONG_MESSAGE_NAME, 'Der Tabellenname "[{tableName}]" hat [{tableNameLength}] Zeichen, darf aber nur max. 64 Zeichen haben.');
TCMSLogChange::AddBackEndMessage(\TCMSFieldLookupMultiselect::TABLE_ALREADY_EXISTS_SHORT_MESSAGE_NAME, 'Eine Tabelle mit dem SQL-Namen "[{tableName}]" existiert bereits.', messageTypeId: 3); // warning

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      //'049_trans' => 'Werteliste aus Tabelle (Mehrfach-Auswahl möglich)',
      //'fieldclass' => 'TCMSFieldLookupMultiselect',
      //'constname' => 'CMSFIELD_MULTITABLELIST',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> ZIELTABELLE_mlt oder beliebig, dann ist ein Feldkonfigurationsparameter nötig</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldLookupMultiselect extends TCMSField</div>

<div>Erzeugt eine ein-/ausklappbare Liste, in der alle verknüpften Datensätze der Zieltabelle angezeigt werden.</div>

<div>Ermöglicht die Auswahl mehrerer Datensätze aus einer verknüpften Tabelle (PopUp). In den Datensätzen kann gesucht werden.</div>

<div>Erstellt eine zusätzliche Tabelle "TABELLE-DES-FELDES_ZIELTABELLE_mlt",&nbsp;kann durch Konfiguration beliebig geändert werden.</div>

<div>&nbsp;</div>

<div class="important"><strong>Wichtig:</strong> Der Name des Feldes muss exakt dem Namen der zu verknüpfenden Tabelle entsprechen, optional gefolgt von "_mlt" (ZIELTABELLE_mlt), es sei denn, der Parameter connectedTableName wird definiert (weitere Informationen unten).</div>

<div class="important">Bei der Verwendung des Parameters connectedTableName darf der Feldnamen nicht auf _mlt enden.</div>

<div class="important">Allerdings ist es nicht möglich, hierbei einen Namen anzugeben, der insgesamt länger als 64-Zeichen lang ist, da dieser in MySQL nicht korrekt gespeichert werden kann und somit zu Fehlern führt.</div>

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
		<li class="parameter optional"><strong>connectedTableName=ZIELTABELLE</strong> - der Tabellenname der Zieltabelle. Dieser Parameter muss gesetzt werden, wenn mehrere Felder in der Tabelle mit der gleichen Zieltabelle verknüpft werden sollen. Dadurch kann ein beliebiger Feldname vergeben werden. Damit die zusätzliche Tabelle eindeutig bleibt, enthält dann diese Tabelle auch noch den Feldnamen.</li>
		<li class="parameter optional"><strong>mltTableName=MATCH_TABELLE_MLT</strong> - die zusätzliche Tabelle, welche Einträge von aktueller Tabelle und Zieltabelle miteinander verknüpft. Nützlich, wenn der generische Tabellenname aus beiden Tabellen (evtl. zzgl. Feldname)&nbsp;zu lang werden würde (mehr als 64 Zeichen).&nbsp;Muss auf "_mlt" enden.&nbsp;</li>
		<li class="parameter optional"><strong>bOpenOnLoad=true</strong> - beim Aufruf des Editors wird die Liste bereits geöffnet angezeigt.</li>
		<li class="parameter optional"><strong>bAllowCustomSortOrder=true</strong> - gibt an, ob das Feld manuell sortierbar ist. Sonstige eingestellte Sortierungen werden verworfen.</li>
	</ul>
	</li>
</ul>
</div>
',
  ])
  ->setWhereEquals([
      'id' => '18',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      //'049_trans' => 'Werteliste aus Tabelle - Checkboxes (Mehrfach-Auswahl möglich)',
      //'fieldclass' => 'TCMSFieldLookupMultiselectCheckboxes',
      //'constname' => 'CMSFIELD_MULTITABLELIST_CHECKBOXES',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> ZIELTABELLE_mlt oder beliebig, dann ist ein Feldkonfigurationsparameter nötig.</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldLookupMultiselectCheckboxes extends TCMSFieldLookupMultiselect</div>

<div>Erzeugt ein Feld mit allen verfügbaren Datensätzen aus der Zieltabelle und ermöglicht die Auswahl mehrerer Datensätze. Die Datensätze werden als CheckBox-Felder ausgegeben.</div>

<div>Dieses Feld eignet sich für Verknüpfungen auf Tabellen mit wenigen Einträgen.</div>

<div>Erstellt eine zusätzliche Tabelle "TABELLE-DES-FELDES_ZIELTABELLE_mlt",&nbsp;kann durch Konfiguration beliebig geändert werden.</div>

<div>&nbsp;</div>

<div class="important"><strong>Wichtig:</strong> Der Name des Feldes muss exakt dem Namen der zu verknüpfenden Tabelle entsprechen, optional gefolgt von "_mlt" (ZIELTABELLE_mlt), es sei denn, der Parameter connectedTableName wird definiert (weitere Informationen unten).</div>

<div class="important">Bei der Verwendung des Parameters connectedTableName darf der Feldnamen nicht auf _mlt enden.</div>

<div class="important">Allerdings ist es nicht möglich, hierbei einen Namen anzugeben, der insgesamt länger als 64-Zeichen lang ist, da dieser in MySQL nicht korrekt gespeichert werden kann und somit zu Fehlern führt.</div>

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
		<li class="parameter optional"><span class="strike-through"><strong>bOpenOnLoad=true</strong> - beim Aufruf des Editors wird die Liste bereits geöffnet angezeigt.</span> (<strong>geerbt von TCMSFieldLookupMultiselect, aber nicht verwendet</strong>)</li>
		<li class="parameter optional"><strong>connectedTableName=ZIELTABELLE</strong> - der Tabellenname der Zieltabelle. Dieser Parameter muss gesetzt werden, wenn mehrere Felder in der Tabelle mit der gleichen Zieltabelle verknüpft werden sollen. Dadurch kann ein beliebiger Feldname vergeben werden.&nbsp;Damit die zusätzliche Tabelle eindeutig bleibt, enthält dann diese Tabelle auch noch den Feldnamen.</li>
		<li class="parameter optional"><strong>mltTableName=MATCH_TABELLE_MLT</strong> - die zusätzliche Tabelle, welche Einträge von aktueller Tabelle und Zieltabelle miteinander verknüpft. Nützlich, wenn der generische Tabellenname aus beiden Tabellen (evtl. zzgl. Feldname)&nbsp;zu lang werden würde (mehr als 64 Zeichen).&nbsp;Muss auf "_mlt" enden.</li>
		<li class="parameter optional"><strong>bAllowCustomSortOrder=true</strong> - gibt an, ob das Feld manuell sortierbar ist. Sonstige eingestellte Sortierungen werden verworfen.</li>
		<li class="parameter optional"><strong>restriction=feld_name=[{wert}]</strong> - Feldname, auf den einzuschränken ist und optional ein weiterer Wert, welcher ebenfalls ein Feldname sein kann (dann wird der Wert von diesem Feld aus dem aktuellen Record genommen).</li>
	</ul>
	</li>
</ul>
</div>
',
  ])
  ->setWhereEquals([
      'id' => '46',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      //'049_trans' => 'Feldauswahl aus einer Tabelle',
      //'fieldclass' => 'TCMSFieldLookupMultiselectCheckboxesSelectFieldsFromTable',
      //'constname' => 'CMSFIELD_FIELDSELECT_MLT',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> cms_field_conf_mlt</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldLookupMultiselectCheckboxesSelectFieldsFromTable extends TCMSFieldLookupMultiselectCheckboxes</div>

<div>Erzeugt ein Feld mit allen verfügbaren Feldern aus einer Tabelle und ermöglicht eine Mehrfachauswahl dieser. Die Datensätze werden als CheckBox-Felder ausgegeben.</div>

<div>&nbsp;</div>

<div class="important"><strong>Wichtig:</strong> Das Feld muss zwingend den Namen <strong>cms_field_conf_mlt</strong> haben.</div>

<div>&nbsp;</div>

<div>
<ul>
	<li class="parameter required head">Pflicht-Parameter:</li>
	<li>
	<ul>
		<li class="parameter required"><strong>sShowFieldsFromTable=tabellenname</strong> - gibt an, um welche Zieltabelle es sich handelt - Beispiel: sShowFieldsFromTable=shop_article</li>
	</ul>
	</li>
	<li>&nbsp;</li>
	<li class="parameter optional head">Optionale Parameter:</li>
	<li>
	<ul>
		<li class="parameter optional"><strong>mltTableName=MATCH_TABELLE_MLT</strong> - die zusätzliche Tabelle, welche Einträge von aktueller Tabelle und Zieltabelle miteinander verknüpft. Nützlich, wenn der generische Tabellenname aus beiden Tabellen (evtl. zzgl. Feldname) zu lang werden würde (mehr als 64 Zeichen). Muss auf "_mlt" enden.</li>
		<li class="parameter optional"><span class="strike-through"><strong>bOpenOnLoad=true</strong> - beim Aufruf des Editors wird die Liste bereits geöffnet angezeigt.</span> (<strong>geerbt von TCMSFieldLookupMultiselect, aber nicht verwendet</strong>)</li>
		<li class="parameter optional"><strong>bAllowCustomSortOrder=true</strong> - gibt an, ob das Feld manuell sortierbar ist. Sonstige eingestellte Sortierungen werden verworfen.</li>
		<li class="parameter optional"><strong>restriction=feld_name=[{wert}]</strong> - Feldname, auf den einzuschränken ist und optional ein weiterer Wert, welcher ebenfalls ein Feldname sein kann (dann wird der Wert von diesem Feld aus dem aktuellen Record genommen).</li>
	</ul>
	</li>
	<li>
	<ul>
	</ul>
	</li>
</ul>
</div>
',
  ])
  ->setWhereEquals([
      'id' => '3c29f1be-51f6-b5f5-1203-cba0e627df64',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      //'049_trans' => 'Tags',
      //'fieldclass' => 'TCMSFieldLookupMultiselectTags',
      //'constname' => 'CMSFIELD_TAGS',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> cms_tags_mlt oder beliebig, dann ist ein Feldkonfigurationsparameter nötig.</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldLookupMultiselectTags extends TCMSFieldLookupMultiselect</div>

<div>Erzeugt ein Textfeld mit Lookup auf alle Datensätze in cms_tags, welche über eine Autocomplete-Funktion in dem Textfeld angezeigt bzw. durchsucht werden können.</div>

<div>Erstellt eine zusätzliche Tabelle "TABELLE-DES-FELDES_cms_tags_mlt".</div>

<div>&nbsp;</div>

<div class="important"><strong>Wichtig:</strong> Das Feld muss&nbsp;den Namen cms_tags_mlt&nbsp;haben, es sei denn der Parameter connectedTableName wird definiert (weitere Informationen unten).</div>

<div class="important">Bei der Verwendung des Parameters connectedTableName darf der Feldnamen nicht auf _mlt enden.&nbsp;</div>

<div>&nbsp;</div>

<div>
<ul>
	<li class="parameter required head">Pflicht-Parameter:</li>
	<li>
	<ul>
		<li class="parameter required">n/a</li>
	</ul>
	</li>
	<li>&nbsp;</li>
	<li class="parameter optional head">Optionale Parameter:</li>
	<li>
	<ul>
		<li class="parameter optional"><strong>connectedTableName=cms_tags</strong> - der Tabellenname der Zieltabelle muss immer cms_tags sein. Dieser Parameter muss gesetzt werden, wenn mehrere Tag Felder in der gleichen Tabelle existieren. Dadurch kann ein beliebiger Feldname vergeben werden. Damit die zusätzliche Tabelle eindeutig bleibt, enthält dann diese Tabelle auch noch den Feldnamen.</li>
		<li class="parameter optional"><strong>mltTableName=MATCH_TABELLE_MLT</strong> - die zusätzliche Tabelle, welche Einträge von aktueller Tabelle und Zieltabelle miteinander verknüpft. Nützlich, wenn der generische Tabellenname aus beiden Tabellen (evtl. zzgl. Feldname) zu lang werden würde (mehr als 64 Zeichen). Muss auf "_mlt" enden.</li>
		<li class="parameter optional"><span class="strike-through"><strong>bOpenOnLoad=true</strong> - beim Aufruf des Editors wird die Liste bereits geöffnet angezeigt.</span> (<strong>geerbt von TCMSFieldLookupMultiselect, aber nicht verwendet</strong>)</li>
		<li class="parameter optional"><span class="strike-through"><strong>bAllowCustomSortOrder=true</strong> - gibt an, ob das Feld manuell sortierbar ist. Sonstige eingestellte Sortierungen werden verworfen.</span> (<strong>geerbt von TCMSFieldLookupMultiselect, aber nicht verwendet</strong>)</li>
	</ul>
	</li>
	<li>
	<ul>
	</ul>
	</li>
</ul>
</div>
',
  ])
  ->setWhereEquals([
      'id' => '52',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      //'049_trans' => 'Werteliste aus Tabelle Unique',
      //'fieldclass' => 'TCMSFieldLookupMultiselectCheckboxesUnique',
      //'constname' => 'CMSFIELD_MULTITABLELIST_CHECKBOXES_UNIQUE',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> ZIELTABELLE_mlt oder beliebig, dann ist ein Feldkonfigurationsparameter nötig</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldLookupMultiselectCheckboxesUnique extends TCMSFieldLookupMultiselectCheckboxes</div>

<div>Erzeugt ein Feld mit allen verfügbaren Datensätzen aus der Zieltabelle und ermöglicht die Auswahl mehrerer Datensätze. Die Datensätze werden als CheckBox-Felder ausgegeben.</div>

<div>Es werden nur Datensätze aus einer verknüpften Tabelle angezeigt, die nicht schon von einem Datensatz in dieser Tabelle ausgewählt wurden.</div>

<div>Dieses Feld eignet sich für Verknüpfungen auf Tabellen mit wenigen Einträgen.</div>

<div>Erstellt eine zusätzliche Tabelle "TABELLE-DES-FELDES_ZIELTABELLE_mlt",&nbsp;kann durch Konfiguration beliebig geändert werden.</div>

<div>&nbsp;</div>

<div class="important"><strong>Wichtig:</strong> Der Name des Feldes muss exakt dem Namen der zu verknüpfenden Tabelle entsprechen, optional gefolgt von "_mlt" (ZIELTABELLE_mlt), es sei denn, der Parameter connectedTableName wird definiert (weitere Informationen unten).</div>

<div class="important">Bei der Verwendung des Parameters connectedTableName&nbsp;darf der Feldnamen nicht auf _mlt enden.&nbsp;</div>

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
		<li class="parameter optional"><strong>connectedTableName=ZIELTABELLE</strong> - der Tabellenname der Zieltabelle. Dieser Parameter muss gesetzt werden, wenn mehrere Felder in der Tabelle mit der gleichen Zieltabelle verknüpft werden sollen. Dadurch kann ein beliebiger Feldname vergeben werden. Damit die zusätzliche Tabelle eindeutig bleibt, enthält dann diese Tabelle auch noch den Feldnamen. (<strong>geerbt von TCMSFieldLookupMultiselectCheckboxes</strong>)</li>
		<li class="parameter optional"><strong>mltTableName=MATCH_TABELLE_MLT</strong> - die zusätzliche Tabelle, welche Einträge von aktueller Tabelle und Zieltabelle miteinander verknüpft. Nützlich, wenn der generische Tabellenname aus beiden Tabellen (evtl. zzgl. Feldname) zu lang werden würde (mehr als 64 Zeichen). Muss auf "_mlt" enden.</li>
		<li class="parameter optional"><span class="strike-through"><strong>bOpenOnLoad=true</strong> - beim Aufruf des Editors wird die Liste bereits geöffnet angezeigt.</span> (<strong>geerbt von TCMSFieldLookupMultiselectCheckboxes -&gt; TCMSFieldLookupMultiselect, aber nicht verwendet</strong>)</li>
		<li class="parameter optional"><strong>bAllowCustomSortOrder=true</strong> - gibt an, ob das Feld manuell sortierbar ist. Sonstige eingestellte Sortierungen werden verworfen. (<strong>geerbt von TCMSFieldLookupMultiselectCheckboxes</strong>)</li>
		<li class="parameter optional"><strong>restriction=feld_name=[{wert}]</strong> - Feldname, auf den einzuschränken ist und optional ein weiterer Wert, welcher ebenfalls ein Feldname sein kann (dann wird der Wert von diesem Feld aus dem aktuellen Record genommen). (<strong>geerbt von TCMSFieldLookupMultiselectCheckboxes</strong>)</li>
	</ul>
	</li>
	<li>
	<ul>
	</ul>
	</li>
</ul>
</div>
',
  ])
  ->setWhereEquals([
      'id' => 'bf4fa829-7c7a-55ba-680a-f6376e8657f3',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      //'049_trans' => 'Werteliste aus Tabelle für Einschränkungen (Mehrfach-Auswahl möglich)',
      //'fieldclass' => 'TCMSFieldLookupMultiSelectRestriction',
      //'constname' => 'CMSFIELD_MULTITABLELIST_RESTRICTION',
      'help_text' => '<div class="field-name">
<div class="field-name"><strong>Feldname:</strong> ZIELTABELLE_mlt oder beliebig, dann ist ein Feldkonfigurationsparameter nötig</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldLookupMultiSelectRestriction extends TCMSFieldLookupMultiselect</div>

<div>Erzeugt eine ein-/ausklappbare Liste, in der alle verknüpften Datensätze der Zieltabelle angezeigt werden.</div>

<div>Diese ermöglicht die Auswahl mehrerer Datensätze aus einer verknüpften Tabelle (PopUp). In den Datensätzen kann gesucht werden.</div>

<div>Erstellt eine zusätzliche Tabelle "TABELLE-DES-FELDES_ZIELTABELLE_mlt" und ein zusätzliches Feld "NAME-DES-FELDES_inverse_empty", das&nbsp;für die Logik bei einer leeren Auswahl zuständig ist.</div>

<div>Um die Option "Logik bei leerer Liste umkehren" bei einer leeren Auswahl zu nutzen, müssen die vom Feldtyp bereitgestellten Funktionen "GetField...WithInverseEmptySelectionLogicList" oder&nbsp; "GetField...WithInverseEmptySelectionLogicIdList" verwendet werden. Ist das Feld aktiviert, liefern die Funktionen den Wert null zurück, sollte keine Auswahl gemacht worden sein. Ist das Feld deaktiviert, liefern die Funktionen eine leere Liste, sollte keine Auswahl gemacht worden sein.</div>

<div class="important"><strong>Wichtig:</strong> Der Name des Feldes muss exakt dem Namen der zu verknüpfenden Tabelle entsprechen, optional gefolgt von "_mlt" (ZIELTABELLE_mlt), es sei denn, der Parameter connectedTableName wird definiert (weitere Informationen unten).</div>

<div class="important">Bei der Verwendung des Parameters connectedTableName&nbsp;darf der Feldnamen nicht auf _mlt enden.</div>

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
		<li class="parameter optional"><strong>mltTableName=MATCH_TABELLE_MLT</strong> - die zusätzliche Tabelle, welche Einträge von aktueller Tabelle und Zieltabelle miteinander verknüpft. Nützlich, wenn der generische Tabellenname aus beiden Tabellen (evtl. zzgl. Feldname) zu lang werden würde (mehr als 64 Zeichen). Muss auf "_mlt" enden.</li>
		<li class="parameter optional"><strong>bOpenOnLoad=true</strong> - beim Aufruf des Editors wird die Liste bereits geöffnet angezeigt.</li>
		<li class="parameter optional"><strong>bAllowCustomSortOrder=true</strong> - gibt an, ob das Feld manuell sortierbar ist. Sonstige eingestellte Sortierungen werden verworfen.</li>
	</ul>
	</li>
</ul>
</div>
</div>
',
  ])
  ->setWhereEquals([
      'id' => 'd2ced315-bdd4-eed5-1148-e8304ac69120',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      //'049_trans' => 'Dokumenten-Manager',
      //'fieldclass' => 'TCMSFieldDownloads',
      //'constname' => 'CMSFIELD_DOCUMENTS',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> beliebig</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldDownloads extends TCMSFieldLookupMultiselect</div>

<div>Erzeugt einen Button, der das Verbinden mehrerer Dokumente mit dem Datensatz ermöglicht.</div>

<div>Die Dokumente müssen vorher über den Dokumenten-Manager hochgeladen werden.</div>

<div>&nbsp;</div>

<div>
<ul>
	<li class="parameter required head">Pflicht-Parameter:</li>
	<li>
	<ul>
		<li class="parameter required">n/a</li>
	</ul>
	</li>
	<li>&nbsp;</li>
	<li class="parameter optional head">Optionale Parameter:</li>
	<li>
	<ul>
		<li class="parameter optional"><strong>mltTableName=MATCH_TABELLE_MLT</strong> - die zusätzliche Tabelle, welche Einträge von aktueller Tabelle und Zieltabelle&nbsp;"cms_document"&nbsp;miteinander verknüpft. Nützlich, wenn der generische Tabellenname aus beiden Tabellen (zzgl. Feldname) zu lang werden würde (mehr als 64 Zeichen). Muss auf "_mlt" enden.</li>
	</ul>
	</li>
	<li>
	<ul>
	</ul>
	</li>
</ul>
</div>
',
  ])
  ->setWhereEquals([
      'id' => '24',
  ])
;
TCMSLogChange::update(__LINE__, $data);
