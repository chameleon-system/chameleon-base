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

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        '049_helptext' => 'Possible entries:
- filename
- directory
- patterns

All directories are relative to app/cmsdata/.

A note on the folder structure:
Files to be imported must always be located in an "incoming" folder. Parallel to "incoming" there should be "working","archive" and "logs".

Example: "/wawi/incoming/my.csv" requires the following folders:

./app/cmsdata/wawi/incoming - Here is the file my.csv which can be imported.
./app/cmsdata/wawi/working - The file is moved here, when processed by the system.
./app/cmsdata/wawi/archive - The file is moved to the archive when processing is complete (the file is prefixed with YYYYMMDD_HHMMSS- )

The same rule applies if you want to import all files in a folder instead of one file.

Example: "/wawi/attribute/incoming" -> Import of all CSV files from the folder ./app/cmsdata/wawi/attribute/incoming requires that the following folders exist:
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
