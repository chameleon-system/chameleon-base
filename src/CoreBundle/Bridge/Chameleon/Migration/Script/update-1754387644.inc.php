<h1>Build #1754387644</h1>
<h2>Date: 2025-08-05</h2>
<div class="changelog">
    - #67320: update help text for general list_query field
</div>
<?php

/*
Previous text content:
Hier können Sie ein SQL-Statement hinterlegen, mit dem Sie steuern können, was in der Liste angezeigt wird. Bitte achten Sie darauf, dass Ihre Query mindestens ein ID-Feld zur Verfügung stellt.
ACHTUNG! In der Query dürfen keine Aliases verwendet werden (also kein 'left join tablex AS x'). Es muss also immer über den vollen Tabellennamen auf die Felder/Tabellen zugegriffen werden (also zB 'left join tablex on myothertable.id = tablex.myid')
 */

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        //'translation' => 'Query der Listenansicht',
        '049_helptext' => 'Hier können Sie ein SQL-Statement hinterlegen, mit dem Sie steuern können, was in der Liste angezeigt wird. Bitte achten Sie darauf, dass Ihre Query mindestens ein ID-Feld zur Verfügung stellt.

ACHTUNG! In der Query durften früher keine Aliase verwendet werden (also kein \'left join tablex AS x\'). Es musste also immer über den vollen Tabellennamen auf die Felder/Tabellen zugegriffen werden (also zB \'left join tablex on myothertable.id = tablex.myid\').
Dies sollte mittlerweile nicht mehr der Fall sein. Es könnte aber dennoch Außnahmen geben, weshalb empfohlen wird, die Verwendung von Aliasen vorab zu testen, insbesondere in komplexeren Query-Kontexten.',
    ])
    ->setWhereEquals([
        'name' => 'list_query',
    ]);
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_tbl_conf`
               CHANGE `list_query`
                      `list_query` LONGTEXT NOT NULL COMMENT 'Query der Listenansicht'";
TCMSLogChange::RunQuery(__LINE__, $query);
