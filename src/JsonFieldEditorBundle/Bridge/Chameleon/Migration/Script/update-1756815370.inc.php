<h1>Build #1756815370</h1>
<h2>Date: 2025-09-02</h2>
<div class="changelog">
    - #67553: add new cms field for json editor
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      '049_trans' => 'Json Editor',
      'fieldclass' => '\ChameleonSystem\JsonFieldEditorBundle\Bridge\Chameleon\Field\TCMSFieldJsonEditor',
      'constname' => 'CMSFIELD_JSON_EDITOR',
      'mysql_type' => 'VARCHAR',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> json_editor
</div>
<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldJsonEditor extends TCMSField
</div>
<div>Erzeugt einen Json-Editor, mit dem einfach, korrekte Json Objekte erstellt und in der Datenbank gespeichert werden können.</div>
<div>
  Um ein Schema und somit Inhalt für den Edior anlegen zu können, muss ein entsprechendes Twig-Template unter snippets-cms/Fields/jsonEditor angelegt werden. Zur orientierung kann hier das standard File "jsonEditorInputStandard.html.twig" und die README des JsonFieldEditorBundles genutzt werden, indem sich ein Schema Beispiel befindet.
</div>
<div>
  <ul>
    <li clas="parameter optional head">Optionale Parameter</li>
    <li>
    	<ul>
          <li class="parameter optional"><strong>layout=layout1</strong> - der name des angegebenen Layouts, muss dem eines in snippets-cms/Fields/jsonEditor angelegten Templates entsprechen. in diesem Beispiel layout1.html.twig. </li>
      </ul>
    </li>
  </ul>
</div>',
      'id' => '2508ec05-20d4-05ee-2c7a-08eb97658933',
  ])
;
TCMSLogChange::insert(__LINE__, $data);