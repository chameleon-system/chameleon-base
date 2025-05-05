<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_PageTreePaths($sTreePathCache, $row)
{
    $sTreePaths = ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_page_tree_node.no_node_assigned');
    $aAllPaths = explode("\n", $sTreePathCache);

    $sTreePaths = '';
    $count = count($aAllPaths);
    if ($count > 0) {
        // drop empty rows
        foreach ($aAllPaths as $key => $value) {
            if (empty($value)) {
                unset($aAllPaths[$key]);
            }
        }

        if (count($aAllPaths) > 0) {
            reset($aAllPaths);
            foreach ($aAllPaths as $key => $value) {
                $value = TGlobal::OutHTML($value);
                $sTreePaths .= '
                  <ol class="breadcrumb m-0 p-1"><li class="breadcrumb-item"><i class="fas fa-sitemap"></i>'.str_replace('/', '</li><li class="breadcrumb-item">', $value).'</li></ol>';
            }
        }
    }

    return $sTreePaths;
}
