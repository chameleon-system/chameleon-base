<h1>Build #1538989279</h1>
<h2>Date: 2018-10-08</h2>
<div class="changelog">
    - #61: Change type and help text (correct super class)
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
  ->setFields([
      'base_type' => 'mlt',
      'help_text' => '<div class="field-name"><strong>Field name:</strong>&nbsp;any</div>

<div class="php-class"><strong>PHP class:</strong> TCMSFieldDownloads extends TCMSFieldLookupMultiselect</div>

<div>Creates a button that allows several&nbsp;documents to be linked to the record.</div>

<div>The documents must be uploaded via the document manager before.</div>

<div>&nbsp;</div>

<div>
<ul>
	<li class="parameter required head">Required parameter:</li>
	<li>
	<ul>
		<li class="parameter required">n/a</li>
	</ul>
	</li>
	<li>&nbsp;</li>
	<li class="parameter optional head">Optional&nbsp;parameter:</li>
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
  ])
  ->setWhereEquals([
      'fieldclass' => 'TCMSFieldDownloads',
  ])
;
TCMSLogChange::update(__LINE__, $data);

