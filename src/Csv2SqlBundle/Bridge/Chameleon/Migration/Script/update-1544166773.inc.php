<h1>Build #1544166773</h1>
<h2>Date: 2018-12-07</h2>
<div class="changelog">
    - #92: Adapt help text (remove log) for Csv2Sql
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      '049_helptext' => 'Eingabemöglichkeiten:
- Dateiname
- Verzeichnis
- Muster

Alle Verzeichnisse sind relativ zu app/cmsdata/.

Ein Hinweis zur Ordnerstruktur:
Zu importierende Dateien müssen immer in einem Ordner "incoming" liegen. Parallel zu "incoming" sollte es "working" und "archive" geben.

Beispiel: "/wawi/incoming/my.csv" setzt voraus, dass es folgende Ordner gibt:

./app/cmsdata/wawi/incoming - Hier befindet sich die zu importierende Datei my.csv.
./app/cmsdata/wawi/working - Hier wird die Datei hin verschoben, wenn sie vom System verarbeitet wird.
./app/cmsdata/wawi/archive - In das Archiv wird die Datei geschoben, wenn die Verarbeitung abgeschlossen ist (die Datei erhält das Prefix YYYYMMDD_HHMMSS-).

Die gleiche Regel gilt, wenn anstelle einer Datei alle Dateien eines Ordners importiert werden sollen.

Beispiel: "/wawi/attribute/incoming" -> Import aller CSV-Dateien aus dem Ordner ./app/cmsdata/wawi/attribute/incoming setzt voraus, dass es folgende Ordner gibt:
./app/cmsdata/wawi/attribute/incoming
./app/cmsdata/wawi/attribute/working
./app/cmsdata/wawi/attribute/archive
',
  ])
  ->setWhereEquals([
      'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('pkg_csv2sql'), 'source'),
  ])
;
TCMSLogChange::update(__LINE__, $data);

