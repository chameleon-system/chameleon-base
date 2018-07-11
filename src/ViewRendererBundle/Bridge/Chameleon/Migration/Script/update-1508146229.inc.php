<h1>update - Build #1508146229</h1>
<h2>Date: 2017-10-16</h2>
<div class="changelog">
</div>
<?php

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('pkg_cms_theme'), 'snippet_chain');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(array(
        'translation' => 'Snippet chain',
        '049_helptext' => 'In this field a list of directories in which to search for snippets, views and layouts can be defined. Each entry overrides the previous entries, so the last entry is searched first. A path can be given in two ways:

1. Relative to the theme directory under src/themes
2. In Symfony bundle syntax (@AcmeBundle/path)

Example:
@ChameleonSystemCoreBundle/Resources/views
yourdefaulttheme
../extensions
yourportalspecifictheme

Using this configuration, the system would first look into src/themes/yourportalspecifictheme, then into src/extensions, then into src/themes/yourdefaulttheme, then into vendor/chameleon-system/core/Resources/views.
Depending on the resource type, the system expects specific sub-directories (e.g. /layoutTemplates for templates).',
    ))
    ->setWhereEquals(array(
        'id' => $fieldId,
    ))
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields(array(
      '049_helptext' => 'Hier kann angegeben werden, welche Verzeichnisse in welcher Reihenfolge nach Snippets, Views und Layouts durchsucht werden sollen. Jeder Eintrag überschreibt die vorherigen, im letzten Eintrag wird also zuerst gesucht. Ein Pfad kann auf zwei verschiedene Arten angegeben werden:

1. Relativ zum Theme-Verzeichnis unter src/themes
2. In Symfony-Bundle-Syntax (@AcmeBundle/path)

Beispiel:
@ChameleonSystemCoreBundle/Resources/views
yourdefaulttheme
../extensions
yourportalspecifictheme

Diese Konfiguration würde zuerst in src/themes/yourportalspecifictheme schauen, danach in src/extensions, dann in src/themes/yourdefaulttheme, dann in vendor/chameleon-system/core/Resources/views.
Je nach gesuchter Ressource wird ein bestimmtes Unterverzeichnis erwartet (z.B. /layoutTemplates für Templates).',
  ))
  ->setWhereEquals(array(
      'id' => $fieldId,
  ))
;
TCMSLogChange::update(__LINE__, $data);
