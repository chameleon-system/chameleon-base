<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * converts boolean 0/1 field values to human readable form yes/no.
 *
 * @param string $field - the field value
 * @param array $row - the complete record
 * @param string $fieldName
 *
 * @return string
 */
function gcf_YesNoField($field, $row, $fieldName)
{
    $result = TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_boolean.no'));

    if ('1' == $field) {
        $result = TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_boolean.yes'));
    }

    return $result;
}
