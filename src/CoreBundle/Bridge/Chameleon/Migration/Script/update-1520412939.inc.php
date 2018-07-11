<h1>Build #1520412939</h1>
<h2>Date: 2018-03-07</h2>
<div class="changelog">
    - Improve help text for cms_module.module_location
</div>
<?php

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_module'), 'module_location');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      '049_helptext' => 'Hiermit wird festgelegt, an welcher Stelle das System nach Dateien sucht, die zum Modul gehören (Seitendefinitionen, Views).

Der Wert sollte in Symfony Bundle-Syntax angegeben werden, z.B. @ChameleonSystemCoreBundle.

Für Chameleon-eigene Bundles sind auch diese Werte möglich: Core, Custom-Core oder ein Package-Name (z.B. pkgshop).
Für Module, die im AppBundle liegen, ist auch der Wert "Customer" möglich, empfohlen wird aber die oben beschriebene Bundle-Syntax.',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      '049_helptext' => 'Specifies the location in which the system looks for files associated with the module (page definitions, views).

The value should be given in Symfony bundle syntax, e.g. @ChameleonSystemCoreBundle.

For Chameleon-internal bundles these values are also valid: Core, Custom-Core or a package name (e.g. pkgshop).
For modules that are located in the AppBundle, the value "Customer" may be set, although it is recommended to use the bundle syntax described above.',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);
