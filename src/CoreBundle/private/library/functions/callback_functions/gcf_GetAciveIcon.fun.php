<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_GetAciveIcon($value, $row)
{
    if ('1' == $value) {
        return '<i class="fas fa-check-circle text-success" title="'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_boolean.yes')).'"></i>';
    }

    return '<i class="fas fa-times-circle text-danger" title="'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_boolean.no')).'"></i>';
}
