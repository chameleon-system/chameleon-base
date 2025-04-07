<h1>Build #1744018554</h1>
<h2>Date: 2025-04-07</h2>
<div class="changelog">
    - ref #66254: modify description for additional configuration field's option parameter
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
    ->setFields([
        //'049_trans' => 'Werteliste aus Tabelle (erweitert)',
        //'constname' => 'CMSFIELD_EXTENDEDTABLELIST',
        'help_text' => '<div class="field-name"><strong>Feldname:</strong> ZIELTABELLE oder ZIELTABELLE_id oder beliebig, dann ist ein Feldkonfigurationsparameter nötig</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldExtendedLookup extends TCMSFieldLookup</div>

<div>Erzeugt ein readonly-Feld, welches den verknüpften Datensatz anzeigt.</div>

<div>Um einen Datensatz zu verknüpfen, wird ein "Aus Liste auswählen"-Button erzeugt.</div>

<div>Beim Verknüpfen eines Datensatzes wird ein PopUp erzeugt. In der Liste kann gesucht werden. Die Darstellung entspricht einer normalen Listendarstellung, lässt sich also z.B. um Vorschaubilder und andere Felder erweitern.</div>

<div>Um die Verknüpfung zu entfernen, wird ein "Zurücksetzen"-Button erzeugt.</div>

<div>&nbsp;</div>

<div class="important"><strong>Wichtig:</strong> Der Name des Feldes muss exakt dem Namen der zu verknüpfenden Tabelle entsprechen (ZIELTABELLE). Optional kann ein "_id" folgen (ZIELTABELLE_id), es sei denn, der Parameter connectedTableName wird definiert (weitere Informationen unten).</div>

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
		<li class="parameter optional"><span class="strike-through"><strong>restriction=feld_name=[{wert}]</strong> - Feldname, auf den einzuschränken ist und optional ein weiterer Wert, welcher ebenfalls ein Feldname sein kann (dann wird der Wert von diesem Feld aus dem aktuellen Record genommen).</span> (<strong>geerbt von TCMSFieldLookup, aber nicht verwendet</strong>)</li>
		<li class="parameter optional"><strong>connectedTableName=ZIELTABELLE</strong> - der Tabellenname der Zieltabelle ohne _id. Dieser Parameter muss gesetzt werden, wenn mehrere Felder in der Tabelle mit der gleichen Zieltabelle verknüpft werden sollen. Dadurch kann ein beliebiger Feldname vergeben werden.</li>
		<li class="parameter optional"><span class="strike-through"><strong>ReloadOnChange=1/0</strong> - nach einer Änderung wird der Datensatz neu geladen.</span> (<strong>geerbt von TCMSFieldLookup, aber nicht verwendet</strong>)</li>
		<li class="parameter optional"><strong>targetListClass=KLASSENNAME</strong> - für die Listendarstellung für dieses Feld zu verwendende Name der PHP-Custom-Klasse&nbsp;(FQN), die von der Standard-Klasse TCMSListManagerFullGroupTable erbt (oder indirekt von&nbsp;Erben wie z.B.&nbsp;TCMSListManagerShopArticles)</li>
	</ul>
	</li>
	<li>
	<ul>
	</ul>
	</li>
</ul>
</div>
', // inserted: "targetListClass=KLASSENNAME ..."
    ])
    ->setWhereEquals([
        'id' => '44',
    ])
;
TCMSLogChange::update(__LINE__, $data);
