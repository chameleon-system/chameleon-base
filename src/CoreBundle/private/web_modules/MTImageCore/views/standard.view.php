<?php if (!empty($data['sLink'])) {
    ?><a href="<?=TGlobal::OutHTML($data['sLink']); ?>"><?php
} ?>
    <img src="<?=TGlobal::OutHTML($data['oImage']->GetFullURL()); ?>"
         width="<?=TGlobal::OutHTML($data['oImage']->aData['width']); ?>"
         height="<?=TGlobal::OutHTML($data['oImage']->aData['height']); ?>" vspace="0" hspace="0"
         alt="<?=TGlobal::OutHTML($data['oImage']->aData['description']); ?>" border="0"/>
<?php if (!empty($data['sLink'])) {
        ?></a><?php
    } ?>