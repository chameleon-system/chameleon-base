<?php
function matchText($sSearchWords, $text)
{
    $words = explode(' ', $sSearchWords);
    // now grab 10 words around every search term found
    $iWordOffset = 0;
    foreach ($words as $word) {
        $iWordOffset = stripos($text, $word, $iWordOffset);
        if (false !== $iWordOffset) {
            $beforeOffset = $iWordOffset - 250;
            if ($beforeOffset < 0) {
                $beforeOffset = 0;
            }
            $afterOffset = $iWordOffset + strlen($word) + 250;
            if ($afterOffset >= strlen($text)) {
                $afterOffset = strlen($text);
            }
            $sMatchText = substr($text, $beforeOffset, $afterOffset - $beforeOffset);
            // now make sure we end with an alpha char
            if ($beforeOffset > 0) {
                $sMatchText = substr($sMatchText, strpos($sMatchText, ' '));
            }
            // now mark found word
            $sMatchText = str_ireplace($word, '<span class="searchword" style="font-weight:bold">'.$word.'</span>', $sMatchText);
            echo $sMatchText.'...';
        }
    }
}
?>
<h3><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.module_search.result_headline')); ?></h3>
<?php
/** @var TCMSRecordList $oResults */
if (!is_null($oResults) && $oResults->Length() > 0) {
    $count = 0;
    while ($oResult = $oResults->Next()) {
        ++$count;
        $title = 'no pagetitle';
        if (!empty($oResult->sqlData['pagetitle'])) {
            $title = $oResult->sqlData['pagetitle'];
        } ?>
    <div class="MTSearchResult">
        <div class="MTSearchResultLeftBlock">
            <div class="MTSearchResultTitle"><span><?=$count; ?>:</span></div>
        </div>
        <div class="MTSearchResultRightBlock">
            <div class="MTSearchResultTitle"><a href="<?=TGlobal::OutHTML($oResult->sqlData['url']); ?>"><?=$title; ?></a></div>
            <div class="MTSearchResultText"><?php matchText(TGlobal::OutHTML($data['q']), $oResult->sqlData['content']); ?></div>
            <div class="MTSearchResultURL">&raquo; <a href="<?=TGlobal::OutHTML($oResult->sqlData['url']); ?>"><?php
                if (strlen($oResult->sqlData['url']) > 70) {
                    echo substr($oResult->sqlData['url'], 0, 70).'...';
                } else {
                    echo $oResult->sqlData['url'];
                } ?></a></div>
        </div>
        <div class="cleardiv">&nbsp;</div>
    </div>
    <?php
    }
} else {
    echo '<div class="errorMessage">'.TGlobal::Translate('chameleon_system_core.module_search.no_results').'</div>';
}
?>
