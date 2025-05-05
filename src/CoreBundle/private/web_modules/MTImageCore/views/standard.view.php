<?php if (!empty($data['sLink'])) {
    ?><a href="<?php echo TGlobal::OutHTML($data['sLink']); ?>"><?php
} ?>
    <img src="<?php echo TGlobal::OutHTML($data['oImage']->GetFullURL()); ?>"
         width="<?php echo TGlobal::OutHTML($data['oImage']->aData['width']); ?>"
         height="<?php echo TGlobal::OutHTML($data['oImage']->aData['height']); ?>" vspace="0" hspace="0"
         alt="<?php echo TGlobal::OutHTML($data['oImage']->aData['description']); ?>" border="0"/>
<?php if (!empty($data['sLink'])) {
    ?></a><?php
} ?>