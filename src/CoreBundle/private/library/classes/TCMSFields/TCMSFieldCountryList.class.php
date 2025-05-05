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
 * a country selector.
 * /**/
class TCMSFieldCountryList extends TCMSFieldLookup
{
    public function GetOptions()
    {
        // same as lookup, but we need to change the table from t_country to country
        $tmp = $this->name;
        $this->name = 't_country_id';
        parent::GetOptions();
        $this->name = $tmp;
    }

    /**
     * return the new charset latin1 so that we get more memory
     * size for a record.
     *
     * @return string
     */
    public function _GetSQLCharset()
    {
        return ' CHARACTER SET latin1 COLLATE latin1_general_ci';
    }
}
