<?php

$aRow = [];
$iTotalWidth = 0;
for ($i = 0; $i < $iMaxGroupCount; ++$i) {
    ++$iTotalWidth;
    $aRow[] = '';
}
foreach ($aNameColumns as $sName) {
    ++$iTotalWidth;
    $aRow[] = $sName;
    if ($bShowDiffColumn) {
        $aRow[] = 'Delta';
        ++$iTotalWidth;
    }
}
echo '"'.implode('"'.$sSeparator.'"', $aRow).'"';
echo "\n";
foreach ($aBlocks as $oBlock) {
    echo $oBlock->Render('csv.body', $aNameColumns, $iMaxGroupCount, $bShowDiffColumn, '', $sSeparator);
    echo "\n";
}
