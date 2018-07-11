<?php if ($data['oImage']) {
    ?><img src="<?=TGlobal::OutHTML($data['oImage']->GetFullURL()); ?>"
                                    alt="<?=TGlobal::OutHTML($data['oImage']->aData['description']); ?>"
                                    width="<?=TGlobal::OutHTML($data['oImage']->aData['width']); ?>"
                                    height="<?=TGlobal::OutHTML($data['oImage']->aData['height']); ?>"/><?php
} ?>