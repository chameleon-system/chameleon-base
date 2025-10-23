<h1>Build #1756815370</h1>
<h2>Date: 2025-09-02</h2>
<div class="changelog">
    - #67553: add new cms field for json editor
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      '049_trans' => 'Json Editor',
      'fieldclass' => '\ChameleonSystem\FieldJsonEditorBundle\Bridge\Chameleon\Field\TCMSFieldJsonEditor',
      'constname' => 'CMSFIELD_JSON_EDITOR',
      'mysql_type' => 'LONGTEXT',
      'length_set' => '',
      'base_type' => 'standard',
      'indextype' => 'index',
      'class_type' => 'Core',
      'class_subtype' => '',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> json_editor
</div>
<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldJsonEditor extends TCMSField
</div>
<div>Erzeugt einen Json-Editor, mit dem einfach, korrekte Json Objekte erstellt und in der Datenbank gespeichert werden können.</div>
',
      'id' => '2508ec05-20d4-05ee-2c7a-08eb97658933',
  ])
;
TCMSLogChange::insert(__LINE__, $data);