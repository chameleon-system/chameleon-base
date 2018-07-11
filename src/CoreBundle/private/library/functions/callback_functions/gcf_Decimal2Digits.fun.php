<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_Decimal2Digits($field, $row, $fieldName)
{
    $returnString = number_format($field, 2, ',', '.');

    return TGlobal::OutHTML($returnString);
}
