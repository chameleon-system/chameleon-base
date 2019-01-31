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
 * holds a list of menus for the CMS (category boxes). the category boxes are split into
 * three columns (left, middle, and right). Use the property $sLocation to load
 * one of the menu columns.
 *
 * @deprecated since 6.3.0 - only used for deprecated classic main menu
/**/
class TCMSContentBox extends TAdbCmsContentBoxList
{
    /**
     * where the menu is located (left, middle, or right).
     *
     * @var string
     */
    public $sLocation = null;

    /**
     * Overwrites the Load function so that we may fetch the query using the $sLocation
     * sets the users backend language for the menu items.
     *
     * @param string $query
     */
    public function Load($query = null, array $queryParameters = array(), array $queryParameterTypes = array())
    {
        $language = null;
        /** @var $oUser TdbCmsUser */
        $oUser = &TdbCmsUser::GetActiveUser();
        if ($oUser) {
            $language = $oUser->GetFieldCmsLanguage();
            $this->SetLanguage($language->id);
        }
        if (is_null($query)) {
            $query = $this->_GetQuery($language);
        }
        if (!is_null($query)) {
            parent::Load($query, $queryParameters, $queryParameterTypes);
        }
    }

    /**
     * fetch the query used to get the menus for the column set by $sLocation.
     *
     * @param TdbCmsLanguage $language
     *
     * @return string
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    protected function _GetQuery(\TdbCmsLanguage $language = null)
    {
        $query = null;
        if (!is_null($this->sLocation)) {
            $orderField = self::getFieldTranslationUtil()->getTranslatedFieldName('cms_content_box', 'name', $language);
            $query = "SELECT * FROM `cms_content_box` WHERE `show_in_col`='".MySqlLegacySupport::getInstance()->real_escape_string($this->sLocation)."' ORDER BY `$orderField`";
        }

        return $query;
    }
}
