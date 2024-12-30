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
$sFormID = 'THTMLTableSearch'.md5(uniqid(rand(), true));
?>
<div class="THTMLTable">
    <div class="global-search-filter">
        <form name="<?=$sFormID; ?>" accept-charset="utf-8" method="post"
              action="<?=$oHTMLTable->GetGlobalSearchBaseURL(); ?>">
            <table>
                <tr>
                    <th class="perPage"><?=TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list.form_records_per_page')); ?></th>
                    <td class="perPage">
                        <select
                            name="<?=TGlobal::OutHTML($oHTMLTable->sListIdentKey); ?>[<?=THTMLTable::URL_PARAM_CHANGE_PAGE_SIZE; ?>]"
                            onchange="document.<?=TGlobal::OutHTML($sFormID); ?>.submit();">
                            <option value="10" <?php if (10 == $oHTMLTable->iPageSize) {
    echo 'selected="selected"';
} ?>>
                                10
                            </option>
                            <option value="20" <?php if (20 == $oHTMLTable->iPageSize) {
    echo 'selected="selected"';
} ?>>
                                20
                            </option>
                            <option value="50" <?php if (50 == $oHTMLTable->iPageSize) {
    echo 'selected="selected"';
} ?>>
                                50
                            </option>
                            <option value="100" <?php if (100 == $oHTMLTable->iPageSize) {
    echo 'selected="selected"';
} ?>>
                                100
                            </option>
                            <option value="250" <?php if (250 == $oHTMLTable->iPageSize) {
    echo 'selected="selected"';
} ?>>
                                250
                            </option>
                            <option value="500" <?php if (500 == $oHTMLTable->iPageSize) {
    echo 'selected="selected"';
} ?>>
                                500
                            </option>
                        </select>
                    </td>
                    <th class="searchtext"><?=TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Suchbegriff')); ?></th>
                    <td class="searchform"><input type="text"
                                                  name="<?=TGlobal::OutHTML($oHTMLTable->sListIdentKey); ?>[<?=THTMLTable::URL_PARAM_SEARCH_GLOBAL; ?>]"
                                                  value="<?=TGlobal::OutHTML($oHTMLTable->GetGlobalSearchTerm()); ?>"/>
                    </td>
                    <td class="searchbutton"><input type="submit"
                                                    value="<?=TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Suchen')); ?>"/></td>
                </tr>
            </table>
        </form>

    </div>
</div>