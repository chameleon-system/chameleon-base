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
 * extend any interfaces (import and export) you build for the cms from this class.
 * to get an instance of the right interface, use the static factory GetInterfaceManagerObject in the database object of the table
 * Example: $oInterface = TdbCmsInterfaceManager::GetInterfaceManagerObject(1);
 * this would return the interface object defined by record id 1 in the table.
 */
class TCMSInterfaceManagerBase extends TCMSRecord
{
    /**
     * Contains the parameters of the interface as key=>value pair.
     *
     * @var array<string, mixed>
     */
    protected $aParameter;
    /**
     * set to true if the interface encountered an error.
     *
     * @var bool
     */
    protected $bHasErrors = false;
    /**
     * write any messages into this array so that the calling class can display the info to the user.
     *
     * @var array
     */
    protected $aMessages = [];

    /**
     * @param string $id
     * @param string $iLanguage
     */
    public function __construct($id = null, $iLanguage = null)
    {
        parent::__construct('cms_interface_manager', $id, $iLanguage);
    }

    /**
     * return import infos.
     *
     * @return array
     */
    public function GetEventInfos()
    {
        return $this->aMessages;
    }

    /**
     * @return void
     */
    protected function PostLoadHook()
    {
        parent::PostLoadHook();
        $oParameterList = $this->GetProperties('cms_interface_manager_parameter', 'TdbCmsInterfaceManagerParameter');
        while ($oParameter = $oParameterList->Next()) {
            $this->aParameter[$oParameter->GetName()] = $oParameter->sqlData['value'];
        }
    }

    /**
     * @param string $sKey
     *
     * @return false|mixed
     */
    protected function GetParameter($sKey)
    {
        if (is_array($this->aParameter) && array_key_exists($sKey, $this->aParameter)) {
            return $this->aParameter[$sKey];
        } else {
            return false;
        }
    }

    /**
     * do any initialization work that needs to be done before you want to run the import.
     *
     * @return void
     */
    public function Init()
    {
    }

    /**
     * Run the import.
     * This method should not be overwritten in child classes.
     *
     * @return bool true if the import is successful, else false
     */
    final public function RunImport()
    {
        $bImportOk = false;
        if ($this->PrepareImport()) {
            $bImportOk = $this->PerformImport();
        }
        $this->Cleanup($bImportOk);

        return $bImportOk;
    }

    /**
     * prepare the import - setup temp tables, download product feeds, etc
     * return false if the preparation failed.
     *
     * @return bool
     */
    protected function PrepareImport()
    {
        $bPreparationOk = true;

        return $bPreparationOk;
    }

    /**
     * perform the actual import work. return true if the import succeeds, else false.
     *
     * @return bool
     */
    protected function PerformImport()
    {
        $bImportSucceeded = true;

        return $bImportSucceeded;
    }

    /**
     * this method is always called at the end of RunImport (even if the import failed) to do any cleanup work.
     *
     * @param bool $bImportSucceeded - set to true if the import succeeded
     *
     * @return bool
     */
    protected function Cleanup($bImportSucceeded)
    {
        return $bImportSucceeded;
    }
}
