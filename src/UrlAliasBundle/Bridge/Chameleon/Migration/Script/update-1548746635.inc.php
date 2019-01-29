<h1>Build #1548746635</h1>
<h2>Date: 2019-01-29</h2>
<div class="changelog">
    - #286: Improve documentation (and type) of url alias table fields
</div>
<?php

$sourceUrlFieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_url_alias'), 'source_url');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      '049_helptext' => 'The URL of the client request. A full relative path must be given - so starting with a /. This may include parameters.',
  ])
  ->setWhereEquals([
      'id' => $sourceUrlFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);


$exactMatchFieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_url_alias'), 'exact_match');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        '049_helptext' => 'If set no additional trailing (path) parts will be allowed. Otherwise a substring match is performed.
Activating this is advisable for performance reasons.

A trailing slash / or parameters are not influenced by this.',
    ])
    ->setWhereEquals([
        'id' => $exactMatchFieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);


$targetUrlFieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_url_alias'), 'target_url');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        '049_helptext' => 'If a request url is matched this is the resulting redirect url. It can also include parameters.
Only mapped parameters will be further added to it.',
    ])
    ->setWhereEquals([
        'id' => $targetUrlFieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);


$ignoreParameterFieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_url_alias'), 'ignore_parameter');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        '049_helptext' => 'The presence of any of these parameters in the requested url will not prohibit matching. They will be removed before the redirect.
Separate multiple parameters with commas or breaks.
Enter a star (*) if you want to ignore all parameters that are not listed in the source url.',
    ])
    ->setWhereEquals([
        'id' => $ignoreParameterFieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);


$parameterMappingFieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_url_alias'), 'parameter_mapping');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        '049_helptext' => 'Specifies additional parameters that if present in the requested url will add a mapped parameter to the target url.
These parameters are automatically included in the parameters to be ignored. Separate multiple mappings with breaks.

Example:
id = item_id
requested: /url?id=5
redirected: /new_url//?item_id=5',
    ])
    ->setWhereEquals([
        'id' => $parameterMappingFieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);
// TODO commas above, breaks here

// TODO request url vs source url
// TODO what if a target parameter of a mapping is also in the source url?
// TODO a parameter to be ignored occurs also in the ones to be mapped?
