<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;

class MTMerklisteCore extends TUserModelBase
{
    public const SESSION_STORE_NAME = __CLASS__;
    protected $aMerklistenItems = [];

    protected $bAllowHTMLDivWrapping = true;

    public function Init()
    {
        parent::Init();
        $this->aMerklistenItems = self::GetListFromSession();
        // load items...
    }

    public function Execute()
    {
        parent::Execute();
        $this->data['oItems'] = $this->LoadItems();

        return $this->data;
    }

    protected function LoadItems()
    {
        $oItems = false;
        if (count($this->aMerklistenItems) > 0) {
            $oItems = new TCMSRecordList();
            /* @var $oItems TCMSRecordList */
            $oItems->sTableName = $this->GetItemTableName();

            $oItems->sTableObject = 'MTGlobalListItem';
            $oItems->SetLanguage($this->getLanguageService()->getActiveLanguageId());

            $itemList = array_keys($this->aMerklistenItems);
            $itemList = TTools::MysqlRealEscapeArray($itemList);

            $itemList = "'".implode("','", $itemList)."'";
            $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($oItems->sTableName).'` WHERE `id` IN ('.$itemList.')';
            $oItems->Load($query);
        }

        return $oItems;
    }

    protected function GetItemTableName()
    {
        return 'SET IN CHILD!';
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['Add', 'Remove'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    public function Add()
    {
        $itemId = $this->global->GetUserData('item');
        if (!array_key_exists($itemId, $this->aMerklistenItems)) {
            $this->aMerklistenItems[$itemId] = ['set' => true];
        }
        $this->SaveListToSession();
    }

    public function Remove()
    {
        reset($this->aMerklistenItems);
        $itemId = $this->global->GetUserData('item');
        if (array_key_exists($itemId, $this->aMerklistenItems)) {
            unset($this->aMerklistenItems[$itemId]);
        }

        reset($this->aMerklistenItems);
        $this->SaveListToSession();
    }

    protected function SaveListToSession()
    {
        $_SESSION[self::SESSION_STORE_NAME] = $this->aMerklistenItems;
    }

    /**
     * static function returns marked items from session as an array.
     *
     * @return array
     */
    public static function GetListFromSession()
    {
        if (!array_key_exists(self::SESSION_STORE_NAME, $_SESSION)) {
            $_SESSION[self::SESSION_STORE_NAME] = [];
        }

        // $this->aMerklistenItems = $_SESSION[self::SESSION_STORE_NAME];
        return $_SESSION[self::SESSION_STORE_NAME];
    }

    /**
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }
}
