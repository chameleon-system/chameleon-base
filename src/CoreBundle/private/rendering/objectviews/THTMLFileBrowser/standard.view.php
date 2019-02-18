<?php
/**
 * @deprecated since 6.3.0 - not used anymore.
 */
/** @var $oTable THTMLFileBrowser */
/** @var $aActions array */
$oLocal = &TCMSLocal::GetActive();
$activePageService = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
?>
<div class="THTMLFileBrowser">
    <div class="standard">
        <?=$sMsg; ?>
        <form name="<?=TGlobal::OutHTML($oTable->sListId); ?>" accept-charset="UTF-8" action="<?= $activePageService->getLinkToActivePageRelative(); ?>"
              method="post">
            <input type="hidden" name="listid" value="<?=TGlobal::OutHTML($oTable->sListId); ?>"/>
            <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($sControllingModuleSpotName); ?>]" value=""/>

            <div class="filter">
                <table>
                    <tr>
                        <th class="perPage"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list.form_records_per_page')); ?></th>
                        <td class="perPage">
                            <select name="iNumberOfRecsPerPage<?=TGlobal::OutHTML($oTable->sListId); ?>"
                                    onchange="document.<?=TGlobal::OutHTML($oTable->sListId); ?>.submit();">
                                <option
                                    value="20" <?php if (20 == $oTable->iNumberOfRecsPerPage) {
    echo 'selected="selected"';
} ?>>
                                    20
                                </option>
                                <option
                                    value="50" <?php if (50 == $oTable->iNumberOfRecsPerPage) {
    echo 'selected="selected"';
} ?>>
                                    50
                                </option>
                                <option
                                    value="100" <?php if (100 == $oTable->iNumberOfRecsPerPage) {
    echo 'selected="selected"';
} ?>>
                                    100
                                </option>
                                <option
                                    value="250" <?php if (250 == $oTable->iNumberOfRecsPerPage) {
    echo 'selected="selected"';
} ?>>
                                    250
                                </option>
                                <option
                                    value="500" <?php if (500 == $oTable->iNumberOfRecsPerPage) {
    echo 'selected="selected"';
} ?>>
                                    500
                                </option>
                            </select>
                        </td>
                        <th class="searchtext"><?=TGlobal::OutHTML(TGlobal::Translate('Suchbegriff')); ?></th>
                        <td class="searchform"><input type="text" name="sFilter<?=TGlobal::OutHTML($oTable->sListId); ?>"
                                                      value="<?=TGlobal::OutHTML($oTable->sFilter); ?>"/></td>
                        <td class="searchbutton"><input type="submit"
                                                        value="<?=TGlobal::OutHTML(TGlobal::Translate('Suchen')); ?>"/>
                        </td>
                    </tr>
                </table>
            </div>
            <table class="listtable">
                <tr>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><?=TGlobal::OutHTML(TGlobal::Translate('Dateinamen')); ?></th>
                    <th><?=TGlobal::OutHTML(TGlobal::Translate('Datum')); ?></th>
                    <th align="right"><?=TGlobal::OutHTML(TGlobal::Translate('Größe (KB)')); ?></th>
                </tr>
                <?php if (count($aActions) > 0) {
    ?>
                <tr class="actionlocationtop">
                    <td><img src="/chameleon/blackbox/images/icons/arrow_turn_down.png" border="" alt=""/></td>
                    <td colspan="4">
                        <a href="#"
                           onclick="THTMLFileBrowserSelectAll(document.<?=TGlobal::OutHTML($oTable->sListId); ?>,true);return false;"><?=TGlobal::OutHTML(TGlobal::Translate('Alle auswählen')); ?></a>
                        | <a href="#"
                             onclick="THTMLFileBrowserSelectAll(document.<?=TGlobal::OutHTML($oTable->sListId); ?>,false);return false;"><?=TGlobal::OutHTML(TGlobal::Translate('Auswahl aufheben')); ?></a>
                        <select name="actionselectortop"
                                onchange="if (confirm('<?=TGlobal::OutJS(TGlobal::Translate('Wollen Sie diese Aktion wirklich auf alle ausgewählten Einträge anwenden?')); ?>')) {document.<?=TGlobal::OutHTML($oTable->sListId); ?>.elements['module_fnc[<?=TGlobal::OutHTML($sControllingModuleSpotName); ?>]'].value=this.value;document.<?=TGlobal::OutHTML($oTable->sListId); ?>.submit();}">
                            <option value=""><?=TGlobal::OutHTML(TGlobal::Translate('markierte Einträge:')); ?></option>
                            <?php foreach ($aActions as $sMethod => $sName) {
        ?>
                            <option value="<?=TGlobal::OutHTML($sMethod); ?>"><?=TGlobal::OutHTML($sName); ?></option>
                            <?php
    } ?>
                        </select>
                    </td>
                </tr>
                <?php
} ?>
                <?php
                $iCount = 0;
                while ($oItem = $oTable->Next()) {
                    $sURL = $oItem->GetURL();
                    ++$iCount;
                    $sClass = ($iCount % 2) ? 'odd' : 'even'; ?>
                    <tr class="<?=$sClass; ?>">
                        <th class="actionColumn"><input type="checkbox" name="aSelectedFiles[]"
                                                        value="<?=TGlobal::OutHTML($oItem->sFileName); ?>"/></th>
                        <td>
                            <?php if ($sURL) {
                        ?><a href="<?=$sURL; ?>"
                                                    target="_blank"><?php
                    } ?><?php if (!empty($oItem->sTypeIcon)) {
                        ?>
                            <img src="<?=TGlobal::OutHTML($oItem->sTypeIcon); ?>"
                                 alt="<?=TGlobal::OutHTML($oItem->sExtension); ?>"
                                 border="0"/><?php
                    } else {
                        echo '?';
                    } ?><?php if ($sURL) {
                        ?></a><?php
                    } ?>
                        </td>
                        <td><?=TGlobal::OutHTML($oItem->sFileName); ?></td>
                        <td><?=TGlobal::OutHTML($oLocal->FormatDate($oItem->sCreated)); ?></td>
                        <td align="right"><?=TGlobal::OutHTML($oLocal->FormatNumber($oItem->dSizeByte / 1024, 2)); ?></td>
                    </tr>
                    <?php
                }
                ?>
                <?php if (count($aActions) > 0) {
                    ?>
                <tr class="actionlocationbottom">
                    <td><img src="/chameleon/blackbox/images/icons/arrow_turn_up.png" border="" alt=""/></td>
                    <td colspan="4">
                        <a href="#"
                           onclick="THTMLFileBrowserSelectAll(document.<?=TGlobal::OutHTML($oTable->sListId); ?>,true);return false;"><?=TGlobal::OutHTML(TGlobal::Translate('Alle auswählen')); ?></a>
                        | <a href="#"
                             onclick="THTMLFileBrowserSelectAll(document.<?=TGlobal::OutHTML($oTable->sListId); ?>,false);return false;"><?=TGlobal::OutHTML(TGlobal::Translate('Auswahl aufheben')); ?></a>
                        <select name="actionselectorbottom"
                                onchange="if (confirm('<?=TGlobal::OutJS(TGlobal::Translate('Wollen Sie diese Aktion wirklich auf alle ausgewählten Einträge anwenden?')); ?>')) {document.<?=TGlobal::OutHTML($oTable->sListId); ?>.elements['module_fnc[<?=TGlobal::OutHTML($sControllingModuleSpotName); ?>]'].value=this.value;document.<?=TGlobal::OutHTML($oTable->sListId); ?>.submit();}">
                            <option value=""><?=TGlobal::OutHTML(TGlobal::Translate('markierte Einträge:')); ?></option>
                            <?php foreach ($aActions as $sMethod => $sName) {
                        ?>
                            <option value="<?=TGlobal::OutHTML($sMethod); ?>"><?=TGlobal::OutHTML($sName); ?></option>
                            <?php
                    } ?>
                        </select>
                    </td>
                </tr>
                <?php
                } ?>
            </table>

            <div class="tablenavi">

                <ul>
                    <li class="first<?php if ($oTable->iCurrentPage <= 1) {
                    echo ' inactive';
                }?>"><a
                        href="<?=$oTable->GetPageURL(0); ?>">&lt;&lt;</a></li>
                    <li class="back<?php if ($oTable->iCurrentPage <= 1) {
                    echo ' inactive';
                }?>"><a
                        href="<?=$oTable->GetPreviousPageURL(); ?>">&lt;</a></li>
                    <li class="pages">
                        <?php
                        $iShowPageCount = 6;
                        $iTotalNumberOfPages = 1;
                        if ($oTable->iNumberOfRecsPerPage > 0) {
                            $iTotalNumberOfPages = ceil($oTable->Length() / $oTable->iNumberOfRecsPerPage);
                        }

                        $iStartPage = $oTable->iCurrentPage - floor($iShowPageCount / 2);
                        if ($iStartPage < 1) {
                            $iStartPage = 1;
                        }

                        $iEndPageCount = $iShowPageCount + $iStartPage;
                        if ($iEndPageCount > $iTotalNumberOfPages) {
                            $iEndPageCount = $iTotalNumberOfPages;
                        }

                        echo '<ul>';
                        if ($iStartPage > 1) {
                            echo '<li><a href="'.$oTable->GetPageURL(0).'">1</a></li>';
                            echo '<li class="more">...</li>';
                        }
                        for ($i = $iStartPage; $i <= $iEndPageCount; ++$i) {
                            $sActiveCSS = '';
                            if (($i - 1) == $oTable->iCurrentPage) {
                                $sActiveCSS = 'class="activepage"';
                            }
                            echo '<li '.$sActiveCSS.'><a href="'.$oTable->GetPageURL($i - 1).'">'.$i.'</a></li>';
                        }
                        if ($iEndPageCount < $iTotalNumberOfPages) {
                            echo '<li class="more">...</li>';
                            echo '<li><a href="'.$oTable->GetPageURL($iTotalNumberOfPages - 1).'">'.$iTotalNumberOfPages.'</a></li>';
                        }
                        echo '</ul>';
                        ?>
                    </li>
                    <li class="next<?php if ($oTable->iCurrentPage >= $iTotalNumberOfPages) {
                            echo ' inactive';
                        }?>"><a
                        href="<?=$oTable->GetNextPageURL(); ?>">&gt;</a></li>
                    <li class="last<?php if ($oTable->iCurrentPage >= $iTotalNumberOfPages) {
                            echo ' inactive';
                        }?>"><a
                        href="<?=$oTable->GetPageURL($iTotalNumberOfPages - 1); ?>">&gt;&gt;</a></li>
                </ul>
                <div class="cleardiv">&nbsp;</div>
            </div>

        </form>
    </div>
</div>
