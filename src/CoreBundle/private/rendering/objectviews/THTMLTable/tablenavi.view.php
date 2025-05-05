<?php
/**
 * @deprecated since 6.3.0 - not used anymore.
 */
/** @var $oHTMLTable THTMLTable */
/** @var $oRecordList TCMSRecordList */
/** @var $oColumns TIterator */

/** @var $sListIdentKey string */
/** @var $iCurrentPage int */
/** @var $sSearchTerm string */

/** @var $aCallTimeVars array */
$iActivePage = $oHTMLTable->GetCurrentPage();
$iNumberOfPages = $oHTMLTable->GetNumberOfPages();
if ($iNumberOfPages > 1) {
    ?>
<div class="tablenavi">
    <ul>
        <li class="first<?php if ($iActivePage <= 1) {
            echo ' inactive';
        } ?>"><a
            href="<?php echo $oHTMLTable->GetPageURL('first'); ?>">&lt;&lt;</a></li>
        <li class="back<?php if ($iActivePage <= 1) {
            echo ' inactive';
        } ?>"><a href="<?php echo $oHTMLTable->GetPageURL('back'); ?>">
            &lt;</a></li>
        <li class="pages">
            <?php
                $iShowPageCount = 6;

    $iStartPage = $iActivePage - floor($iShowPageCount / 2);
    if ($iStartPage < 1) {
        $iStartPage = 1;
    }

    $iEndPageCount = $iShowPageCount + $iStartPage;
    if ($iEndPageCount > $iNumberOfPages) {
        $iEndPageCount = $iNumberOfPages;
    }

    echo '<ul>';
    if ($iStartPage > 1) {
        echo '<li><a href="'.$oHTMLTable->GetPageURL('first').'">1</a></li>';
        echo '<li class="more">...</li>';
    }
    for ($i = $iStartPage; $i <= $iEndPageCount; ++$i) {
        $sActiveCSS = '';
        if ($i == $iActivePage) {
            $sActiveCSS = 'class="activepage"';
        }
        echo '<li '.$sActiveCSS.'><a href="'.$oHTMLTable->GetPageURL($i).'">'.$i.'</a></li>';
    }
    if ($iEndPageCount < $iNumberOfPages) {
        echo '<li class="more">...</li>';
        echo '<li><a href="'.$oHTMLTable->GetPageURL('last').'">'.$iNumberOfPages.'</a></li>';
    }
    echo '</ul>'; ?>
        </li>
        <li class="next<?php if ($iActivePage >= $iNumberOfPages) {
            echo ' inactive';
        } ?>"><a
            href="<?php echo $oHTMLTable->GetPageURL('next'); ?>">&gt;</a></li>
        <li class="last<?php if ($iActivePage >= $iNumberOfPages) {
            echo ' inactive';
        } ?>"><a
            href="<?php echo $oHTMLTable->GetPageURL('last'); ?>">&gt;&gt;</a></li>
    </ul>
    <div class="cleardiv">&nbsp;</div>
</div>
<?php
} ?>