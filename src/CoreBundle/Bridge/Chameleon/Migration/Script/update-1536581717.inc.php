<h1>Build #1536581717</h1>
<h2>Date: 2018-09-10</h2>
<div class="changelog">
    - Configure minimum password length
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
  ->setFields([
      '049_trans' => 'Password (cleartext)',
      'help_text' => '<div class="field-name"><strong>Field name:</strong>&nbsp;any</div>

<div class="php-class"><strong>PHP class:</strong> TCMSFieldPassword extends TCMSFieldVarchar</div>

<div>Creates two text fields for password and password repetition. Besides, a&nbsp;display indicates the quality (security) of the password.</div>

<div>Furthermore, the field indicates whether a password has already been entered or not.</div>

<div>&nbsp;</div>

<div class="important"><strong>Important:</strong>&nbsp;The password is stored in cleartext form&nbsp;in the database! In almost all cases it makes more sense to use the field "Password (secure)"</div>

<div>&nbsp;</div>

<div>
<ul>
	<li class="parameter required head">Required parameters:</li>
	<li>
	<ul>
		<li class="parameter required">n/a</li>
	</ul>
	</li>
	<li>&nbsp;</li>
	<li class="parameter optional head">Optional&nbsp;parameters:</li>
	<li>
	<ul>
		<li class="parameter optional"><strong>minimumLength=Number</strong> - the minimum number of characters for the field (default 6).</li>
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
      'constname' => 'CMSFIELD_PASSWORD',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
    ->setFields([
        '049_trans' => 'Passwort (Klartext)',
        'help_text' => '<div class="field-name"><strong>Feldname:</strong> beliebig</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldPassword extends TCMSFieldVarchar</div>

<div>Erzeugt zwei Textfelder für Passwort und Passwort-Wiederholung. Außerdem wird ein Indikator für die Passwort-Qualität dargestellt.</div>

<div>Das Feld zeigt zusätzlich an, ob bereits ein Passwort hinterlegt ist oder nicht.</div>

<div>&nbsp;</div>

<div class="important"><strong>Wichtig:</strong> Das Passwort wird im Klartext in der Datenbank gespeichert! In praktisch allen Fällen ist es sinnvoller, das Feld "Passwort (sicher)" zu verwenden.</div>

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
		<li class="parameter optional"><strong>minimumLength=Zahl</strong> - die minimale Zeichenzahl für das Feld (Standard 6).</li>
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
        'constname' => 'CMSFIELD_PASSWORD',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
  ->setFields([
      '049_trans' => 'Password (secure)',
      'help_text' => '<div class="field-name"><strong>Field name:</strong>&nbsp;any</div>

<div class="php-class"><strong>PHP class:</strong> TCMSFieldPasswordEncrypted extends TCMSFieldPassword</div>

<div>Creates two text fields for password and password repetition. Besides, a&nbsp;display indicates the quality (security) of the password.</div>

<div>Furthermore, the field indicates whether a password has already been entered or not.</div>

<div>&nbsp;</div>

<div class="important"><strong>Important:</strong>&nbsp;The password is stored in hashed form in the data base.</div>

<div>&nbsp;</div>

<div>
<ul>
	<li class="parameter required head">Required parameters:</li>
	<li>
	<ul>
		<li class="parameter required">n/a</li>
	</ul>
	</li>
	<li>&nbsp;</li>
	<li class="parameter optional head">Optional&nbsp;parameters:</li>
	<li>
	<ul>
		<li class="parameter optional"><strong>minimumLength=Number</strong> - the minimum number of characters for the field (default 6).</li>
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
      'constname' => 'CMSFIELD_CRYPTPASSWORD',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      '049_trans' => 'Passwort (sicher)',
      'help_text' => '<div class="field-name"><strong>Feldname:</strong> beliebig</div>

<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldPasswordEncrypted extends TCMSFieldPassword</div>

<div>Erzeugt zwei Textfelder für Passwort und Passwort-Wiederholung. Außerdem wird ein Indikator für die Passwort-Qualität dargestellt.</div>

<div>Das Feld zeigt zusätzlich an, ob bereits ein Passwort hinterlegt ist oder nicht.</div>

<div>&nbsp;</div>

<div class="important"><strong>Wichtig:</strong> Das Passwort wird gehasht/gesichert in der Datenbank gespeichert.</div>

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
		<li class="parameter optional"><strong>minimumLength=Zahl</strong> - die minimale Zeichenzahl für das Feld (Standard 6).</li>
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
      'constname' => 'CMSFIELD_CRYPTPASSWORD',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'fieldtype_config' => 'minimumLength=10',
    ])
    ->setWhereEquals([
        'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_user'), 'crypted_pw'),
    ])
;
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::addInfoMessage('Minimum password length may now be configured in the fields "Password (secure)" and "Password (cleartext)".', TCMSLogChange::INFO_MESSAGE_LEVEL_INFO);
