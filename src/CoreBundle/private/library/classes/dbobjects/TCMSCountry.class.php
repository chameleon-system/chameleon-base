<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSCountry extends TCMSRecord
{
    public function __construct($id = null, $iLanguage = null)
    {
        parent::TCMSRecord('t_country', $id, $iLanguage);
    }

    public function GetWikiLink($bCheckIfLinkExists = false)
    {
        $sName = $this->sqlData['name'];
        if (!empty($this->sqlData['wikipedia_name'])) {
            $sName = $this->sqlData['wikipedia_name'];
        }
        $sName = str_replace(' ', '_', $sName);
        $sName = urlencode($sName);

        $sLink = 'http://en.wikipedia.org/wiki/'.$sName;

        if ($bCheckIfLinkExists) {
            if (!file_exists($sLink)) {
                $sLink = false;
            }
        }

        return $sLink;
    }
}
