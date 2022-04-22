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
 * holds a record from the "cms_tpl_page_cms_master_pagedef_spot" table.
/**/
class TCMSTplPageCmsMasterPagedefSpot extends TCMSRecord
{
    public function __construct($table = null, $id = null)
    {
        parent::__construct('cms_tpl_page_cms_master_pagedef_spot', $id);
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSTplPageCmsMasterPagedefSpot()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }


    /**
     * returns if model and view are allowed for spot.
     *
     * @param string $sModel
     * @param string $sView
     *
     * @return bool
     */
    public function CheckAccess($sModel, $sView)
    {
        $oCmsMasterPagedefSpot = TdbCmsMasterPagedefSpot::GetNewInstance();
        $oCmsMasterPagedefSpot->Load($this->fieldCmsMasterPagedefSpotId);

        return $oCmsMasterPagedefSpot->CheckAccess($sModel, $sView);
    }
}
