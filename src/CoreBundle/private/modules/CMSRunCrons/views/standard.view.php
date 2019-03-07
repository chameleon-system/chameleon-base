<?php

if (!empty($sMessageOutput)) {
    echo '<div class="notice">';
    echo nl2br(TGlobal::OutHTML($sMessageOutput));
    echo '</div>';
}
