<h1>Build #1702543392</h1>
<h2>Date: 2023-12-14</h2>
<div class="changelog">
    - ref #61998: add markdown text fieldtype
</div>
<?php

// check if fieldtype already exists
$fieldTypeId = TCMSLogChange::GetFieldType('CMSFIELD_MARKDOWNTEXT', false);

if ('' === $fieldTypeId) {
    $data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
      ->setFields([
          '049_trans' => 'Markdown-Text',
          'force_auto_increment' => '0',
          'indextype' => 'none',
          'constname' => 'CMSFIELD_MARKDOWNTEXT',
          'mysql_type' => 'LONGTEXT',
          'length_set' => '',
          'base_type' => 'standard',
          'help_text' => '<div class="field-name"><strong>Feldname:</strong> beliebig</div>
    
    <div class="php-class"><strong>PHP Klasse:</strong>&nbsp;MarkdownEditorField&nbsp;extends TCMSFieldText</div>
    
    <div>&nbsp;</div>
    
    <div>Stellt einen Markdown Text Editor zur Verfügung.<br />
    Um das Feld in der Website auszugeben, muss der markdown Twig Filter verwendet werden:<br />
    Bsp.: {{ categoryDescription | markdown | raw }}</div>
    
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
            <li class="parameter optional">n/a</li>
        </ul>
        </li>
        <li>
        <ul>
        </ul>
        </li>
    </ul>
    </div>
    ',
          'mysql_standard_value' => '',
          'fieldclass' => 'ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Fields\MarkdownEditorField',
          'contains_images' => '0',
          'id' => '80c149c6-f780-bc1b-a1cf-85fb021d5406',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);
}
