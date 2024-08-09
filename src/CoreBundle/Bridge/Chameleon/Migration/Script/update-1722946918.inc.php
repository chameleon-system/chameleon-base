<h1>Build #1722946918</h1>
<h2>Date: 2024-08-06</h2>
<div class="changelog">
    - #64178: new field type "Maps-Koordinaten" with OpenStreetMap instead of Google Maps
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      '049_trans' => 'Maps-Koordinaten',
      'mysql_standard_value' => '',
      'force_auto_increment' => '0',
      'fieldclass' => '\ChameleonSystem\CoreBundle\Field\FieldGeoCoordinates',
      'indextype' => 'index',
      'constname' => 'CMSFIELD_GEO_COORD',
      'mysql_type' => 'VARCHAR',
      'length_set' => '255',
      'base_type' => 'standard',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> beliebig</div>

<div class="php-class"><strong>PHP Klasse:</strong>&nbsp;FieldGeoCoordinates extends TCMSField</div>

<div>Erzeugt zwei Textfelder für die Eingabe der Geo-Koordinaten Breitengrad (latitude) und Längengrad (longitude).</div>

<div>Zusätzlich wird ein Button generiert, welcher ein PopUp mit OpenStreet Maps-Karte öffnet, in welchem dann gesucht werden kann.</div>

<div>&nbsp;</div>

<div>Ermöglicht die Angabe von Längen- und Breitengraden auf manuellem Wege oder über Suchen und Anklicken in einer OpenStreet Maps-Karte.</div>

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
      'contains_images' => '0',
      'class_type' => 'Core',
      'class_subtype' => '',
      'id' => '343b02d4-f02c-c533-be6f-f063d62bc982',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
  ->setFields([
      '049_trans' => 'Maps Coordination',
      'help_text' => '',
  ])
  ->setWhereEquals([
      'id' => '343b02d4-f02c-c533-be6f-f063d62bc982',
  ])
;
TCMSLogChange::update(__LINE__, $data);


$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields([
        'classname' => 'chameleon_system_core.module.map_coordinates',
        'name' => 'Map Koordinaten Backendmodul',
        'description' => '',
        'icon_font_css_class' => '',
        'view_mapper_config' => 'standard=MapCoordinates/standard.html.twig',
        'mapper_chain' => '',
        'view_mapping' => '',
        'revision_management_active' => '0',
        'is_copy_allowed' => '0',
        'show_in_template_engine' => '0',
        'is_restricted' => '0',
        'icon_list' => 'application.png',
        'position' => '0',
        'id' => 'a692affe-4283-2dd3-5a1d-1733d7b95dc8',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
    ->setFields([
        'name' => 'Map Coordinates Backendmodul',
    ])
    ->setWhereEquals([
        'id' => 'a692affe-4283-2dd3-5a1d-1733d7b95dc8',
    ])
;
TCMSLogChange::update(__LINE__, $data);
