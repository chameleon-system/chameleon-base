<h1>Build #1726738633</h1>
<h2>Date: 2024-09-19</h2>
<div class="changelog">
    - #62779: some old projects still have a truncated fieldclass for field type ‘image with cropping’
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
//      '049_trans' => 'Bild mit Ausschnitt',
      'fieldclass' => '\ChameleonSystem\ImageCropBundle\Bridge\Chameleon\Field\TCMSFieldMediaWithImageCrop',
  ])
  ->setWhereEquals([
      'constname' => 'CMSFIELD_EXTENDEDTABLELIST_MEDIA_CROP',
  ])
;
TCMSLogChange::update(__LINE__, $data);
