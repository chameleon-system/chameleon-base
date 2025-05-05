<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_CMSUser($name, $row)
{
    $sfirstName = '';
    if (isset($row['firstname'])) {
        $sfirstName = $row['firstname'];
    }
    if (empty($name) || empty($sfirstName)) {
        $name = $name.$row['firstname'];
    } else {
        $name = $name.', '.$row['firstname'];
    }

    // if(!empty($row['company'])) $name .= " [".$row['company']."]";
    return $name;
}
