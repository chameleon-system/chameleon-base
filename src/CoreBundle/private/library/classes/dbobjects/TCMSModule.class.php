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
 * holds a record from the "cms_tpl_module" table.
/**/
class TCMSModule extends TCMSRecord
{
    public function TCMSModule($table = null, $id = null)
    {
        parent::TCMSRecord('cms_module', $id);
    }

    /**
     * returns the url including optional javascript for popups.
     *
     * @return string
     */
    public function GetModuleLink($connectID = 'headerLine', $urlParams = '')
    {
        $pagedefType = $this->sqlData['module_location'];
        $url = PATH_CMS_CONTROLLER.'?pagedef='.urlencode($this->sqlData['module']);
        if (!empty($this->sqlData['parameter'])) {
            $url .= '&amp;'.$this->sqlData['parameter'];
        }
        if (!empty($pagedefType)) {
            $url .= '&amp;_pagedefType='.$pagedefType;
        }
        $url = TGlobal::OutHTML($url).$urlParams;

        return $url;
    }
}
