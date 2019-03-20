<?php

if (false !== $data['oPageImages']) {
    while ($oPageImage = $data['oPageImages']->Next()) {
        /** @var $oPageImage TCMSRecord */
        $oImage = $oPageImage->GetImage(0);
        if (!is_null($oImage)) {
            echo $oImage->renderImage(300, 300);
        }
    }
}
