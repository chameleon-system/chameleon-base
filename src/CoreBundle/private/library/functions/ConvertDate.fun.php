<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// -----------------------------------------------------------------------------
/* function: ConvertDate
*  converts sql data to german data, or german data to sql
*  input   : date (string) - date to convert
*            dir (string) - sql2g -> sql to german, g2sql -> german to sql
*  output  : [return] (string) - converted string
*  20.06.2002, v1.0
*/
// ..............................................................................

function ConvertDate($date, $dir)
{
    $cdate = '';
    if (!empty($date)) {
        // german format: dd.mm.yyyy HH:MM:SS
        // sql format:    YYYY-MM-DD HH:MM:SS
        $cdate = '';
        if (0 == strcmp($dir, 'g2sql')) {
            $parts = explode(' ', $date); // split into date and time
            $list = explode('.', $parts[0]);

            $cdate = $list[2].'-'.$list[1].'-'.$list[0];
            if (isset($parts[1]) && !empty($parts[1])) {
                $cdate .= ' '.$parts[1];
            }
        } elseif (0 == strcmp($dir, 'sql2g')) {
            $parts = explode(' ', $date); // split into date and time
            $list = explode('-', $parts[0]);
            $cdate = $list[2].'.'.$list[1].'.'.$list[0];
            if (!empty($parts[1])) {
                $cdate .= ' '.$parts[1];
            }
            if (0 == strcmp($cdate, '..')) {
                $cdate = '';
            }
        } else {
            $cdate = "unknown format: '".$dir."'";
        }
    }

    return $cdate;
}
