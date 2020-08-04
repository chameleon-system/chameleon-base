<h1>Build #1596547737</h1>
<h2>Date: 2020-08-04</h2>
<div class="changelog">
    - ref #610: change help text for extended lookup
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
    ->setFields(
        [
            'help_text' => '<div class="field-name"><strong>Feldname:</strong> beliebig</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldExtendedLookupMultiTable extends TCMSFieldExtendedLookup</div>

<div>Erzeugt ein readonly-Feld, welches den verknüpften Datensatz anzeigt.</div>

<div>Um einen Datensatz zu verknüpfen, wird ein "Aus Liste auswählen"-Button erzeugt.</div>

<div>Beim Verknüpfen eines Datensatzes wird ein PopUp erzeugt. In der Liste kann gesucht werden. Die Darstellung entspricht einer normalen Listendarstellung, lässt sich also z.B. um Vorschaubilder und andere Felder erweitern.</div>

<div>Um die Verknüpfung zu entfernen, wird ein "Zurücksetzen"-Button erzeugt.</div>

<div>&nbsp;</div>

<div class="important"><strong>Wichtig:</strong> Der Feldkonfigurationsparameter "<strong>sTables</strong>" muss zwingend gesetzt werden.</div>

<div>&nbsp;</div>

<div>
<ul>
	<li class="parameter required head">Pflicht-Parameter:</li>
	<li>
	<ul>
		<li class="parameter required"><strong class="important">sTables=table1,table2,table3...</strong> - hier können n Tabellen <strong class="important">(mindestens eine)</strong>, durch Komma getrennt, angegeben werden. Die Tabellen müssen mit ihrem <strong class="important">mysql Tabellennamen</strong> angegeben werden.</li>
	</ul>
	</li>
	<li>&nbsp;</li>
	<li class="parameter optional head">Optionale Parameter:</li>
	<li>
	<ul>
		<li class="parameter optional"><strong>sTableDisplayNames=table1-display-name,table2-display-name,table3-display-name</strong> - hier kann ein Anzeigename für jede Tabelle, aus dem Parameter sTables definiert werden. <strong class="important">Wichtig: Die Anzahl muss gleich mit der Anzahl an Tabellen in sTables sein.</strong> Andernfalls wird das Feld "deutsche Bezeichnung" von der Tabelle verwendet.</li>
		<li class="parameter optional"><span class="strike-through"><strong>restriction=feld_name=wert</strong> - Schränkt die angebotene Liste der Datensätze anhand des Feldes "feld_name" auf den übergebenen "wert" ein. Anstelle eines festen Strings kann auch ein Feld des aktiven Datensatzes durch [{}] Klammerung referenziert werden. Beispiel: restriction=feld_in_zieltabelle=[{feld_in_aktueller_tabelle}] schränkt die Liste auf Datensätze ein, bei denen das Feld "feld_in_zieltabelle" den gleichen Wert hat, wie das Feld "feld_in_aktueller_tabelle" im aktuellen Datensatz.</span></li>
		<li class="parameter optional"><span class="strike-through"><strong>ReloadOnChange=1/0</strong> - nach einer Änderung wird der Datensatz neu geladen.</span> (<strong>geerbt von TCMSExtendedLookup -&gt; TCMSFieldLookup, aber nicht verwendet</strong>)</li>
	</ul>
	</li>
	<li>
	<ul>
	</ul>
	</li>
</ul>
</div>
',
        ]
    )
    ->setWhereEquals(
        [
            'constname' => 'CMSFIELD_EXTENDEDMULTITABLELIST',
        ]
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
    ->setFields(
        [
            'help_text' => '<div class="field-name"><strong>Field name:</strong>&nbsp;any</div>

<div class="php-class"><strong>PHP class:</strong> TCMSFieldExtendedLookupMultiTable extends TCMSFieldExtendedLookup</div>

<div>Creates a readonly-field showing the linked record.</div>

<div>To link a record, a "select from list"-button is created.</div>

<div>When linking a record, a&nbsp;pop-up is created. The list can be searched. The list is displayed like usually, that means e.g. thumbnails or other fields can be added.</div>

<div>To remove the link, a "reset"-button is generated.</div>

<div>&nbsp;</div>

<div class="important"><strong>Important:</strong>&nbsp;The field configuration parameter "<strong>sTables</strong>" is required.</div>

<div>&nbsp;</div>

<div>
<ul>
	<li class="parameter required head">Required parameters:</li>
	<li>
	<ul>
		<li class="parameter required"><strong class="important">sTables=table1,table2,table3...</strong> - you can specify n tables (<strong>at least one</strong>), separated by comma. The tables must be specified with their <strong class="important">mysql table name</strong>.</li>
	</ul>
	</li>
	<li>&nbsp;</li>
	<li class="parameter optional head">Optional&nbsp;parameters:</li>
	<li>
	<ul>
		<li class="parameter optional"><strong>sTableDisplayNames=table1-display-name,table2-display-name,table3-display-name</strong> - here you can define a display name for each table, from the parameter sTables. <strong>Important</strong><strong class="important">: The number must equal the number of tables in sTables.</strong>&nbsp;Otherwise, the field "German name" is used by the table.</li>
		<li class="parameter optional"><span class="strike-through"><strong>restriction=feld_name=[{value}]</strong> - Restricts the offered list of records to the transferred "value" using the field "field_name". Instead of a fixed string, a field of the active record can also be referenced by [{}] parentheses. Example: restriction=field_in_target_table=[{field_in_current_table}] restricts the list to records where the field "field_in_target_table" has the same value as the field "field_in_current_table" in the current record.</span></li>
		<li class="parameter optional"><span class="strike-through"><strong>ReloadOnChange=1/0</strong> - after a change, the record is reloaded.</span>&nbsp;(<strong>inherited from&nbsp;TCMSExtendedLookup -&gt; TCMSFieldLookup, but not used</strong>)</li>
	</ul>
	</li>
	<li>
	<ul>
	</ul>
	</li>
</ul>
</div>
',
        ]
    )
    ->setWhereEquals(
        [
            'constname' => 'CMSFIELD_EXTENDEDMULTITABLELIST',
        ]
    );
TCMSLogChange::update(__LINE__, $data);

