<h1>Build #1704289448</h1>
<h2>Date: 2024-01-03</h2>
<div class="changelog">
    - ref #838: update icon field to new CSS Icon field 
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      '049_trans' => 'CSS Icon',
      'force_auto_increment' => '0',
      'constname' => 'CMSFIELD_ICON',
      'mysql_type' => 'VARCHAR',
      'length_set' => '255',
      'base_type' => 'standard',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> beliebig</div>

<div class="php-class"><strong>PHP Klasse:</strong> ChameleonSystem\CoreBundle\Field\FieldIconFontSelector extends \TCMSFieldVarchar</div>

<div>Erzeugt eine Vorschau des ausgewählten Icons und einen Dialog zum Auswählen eines Icons aus einer oder mehreren angegebenen CSS Dateien.</div>

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
		<li class="parameter optional">iconFontCssUrls (Komma separierte Liste von relativen URLs zu icon font CSS Dateien)</li>
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
      'fieldclass' => '\ChameleonSystem\CoreBundle\Field\FieldIconFontSelector',
      'contains_images' => '0',
      'indextype' => 'none',
  ])
  ->setWhereEquals([
      'constname' => 'CMSFIELD_ICON',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
    ->setFields([
        '049_trans' => 'CSS Icon',
//        'constname' => 'CMSFIELD_ICON',
        'help_text' => '<<div class="field-name"><strong>Field Name:</strong> any</div>

<div class="php-class"><strong>PHP Class:</strong> ChameleonSystem\CoreBundle\Field\FieldIconFontSelector extends \TCMSFieldVarchar</div>
<div>Generates a preview of the selected icon and a dialog for selecting an icon from one or more specified CSS files.</div>
<div>&nbsp;</div>
<div>
<ul>
	<li class="parameter required head">Mandatory Parameters:</li>
	<li>
	<ul>
		<li class="parameter required">n/a</li>
	</ul>
	</li>
	<li>&nbsp;</li>
	<li class="parameter optional head">Optional Parameters:</li>
	<li>
	<ul>
		<li class="parameter optional">iconFontCssUrls (Comma-separated list of relative URLs to icon font CSS files)</li>
	</ul>
	</li>
	<li>
	<ul>
	</ul>
	</li>
</ul>
</div>

',
    ])
    ->setWhereEquals([
        'constname' => 'CMSFIELD_ICON',
    ])
;
TCMSLogChange::update(__LINE__, $data);
