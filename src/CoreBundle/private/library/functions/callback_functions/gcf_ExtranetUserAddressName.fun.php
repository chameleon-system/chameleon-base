<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_ExtranetUserAddressName($fieldname, $row, $fieldName)
{
    $sName = TGlobal::OutHTML($row['lastname'].', '.$row['firstname'].' ('.$row['street'].' '.$row['streetnr'].', '.$row['postalcode'].' '.$row['city'].')');

    return $sName;
}
