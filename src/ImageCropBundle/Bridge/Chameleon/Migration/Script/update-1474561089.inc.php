<h1>update - Build #1474561089</h1>
<h2>Date: 2016-09-22</h2>
<div class="changelog">
    add image crop extension<br/>
</div>
<?php
TCMSLogChange::AddVirtualNonDbExtension(
    __LINE__,
    'TCMSImage',
    '\ChameleonSystem\ImageCropBundle\Bridge\Chameleon\TCMSImageImageCrop'
);
