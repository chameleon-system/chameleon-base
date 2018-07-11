<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_CmsTplPageCmsMasterPagedefSpot($name, $row)
{
    $oCmsTplModuleInstance = TdbCmsTplModuleInstance::GetNewInstance();
    $oCmsTplModuleInstance->Load($row['cms_tpl_module_instance_id']);

    $name = TGlobal::OutHTML($oCmsTplModuleInstance->fieldName.' - '.$row['view']);

    return $name;
}
