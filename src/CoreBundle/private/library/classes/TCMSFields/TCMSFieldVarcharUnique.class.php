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
 * std varchar text field (max 255 chars and index unique).
 * /**/
class TCMSFieldVarcharUnique extends TCMSFieldVarchar
{
    /**
     * returns the field value for database storage
     * overwrite this method to modify data on save.
     */
    public function GetSQLOnCopy()
    {
        $sData = parent::GetSQLOnCopy().'_COPY';

        return $sData;
    }
}
