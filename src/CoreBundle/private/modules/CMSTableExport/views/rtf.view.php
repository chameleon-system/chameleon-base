<?php

if (!empty($data['RtfDownloadUrl'])) {
    echo '<iframe src="'.$data['RtfDownloadUrl'].'" style="border: none; width: 0px; height: 0px;" frameborder="0"></iframe>
    <h2>Die Datei wurde generiert.</h2>';
}
