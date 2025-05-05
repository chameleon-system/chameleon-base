<?php if ($data['oImage']) {
    ?><img src="<?php echo TGlobal::OutHTML($data['oImage']->GetFullURL()); ?>"
                                    alt="<?php echo TGlobal::OutHTML($data['oImage']->aData['description']); ?>"
                                    width="<?php echo TGlobal::OutHTML($data['oImage']->aData['width']); ?>"
                                    height="<?php echo TGlobal::OutHTML($data['oImage']->aData['height']); ?>"/><?php
} ?>