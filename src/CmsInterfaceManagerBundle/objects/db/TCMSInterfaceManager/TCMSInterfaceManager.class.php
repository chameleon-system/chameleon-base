<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSInterfaceManager extends TAdbCmsInterfaceManager
{
    /**
     * factory that returns the interface subclass defined in the cms_interface_manager table
     * for this id.
     *
     * @param int $id
     *
     * @return TCMSInterfaceManagerBase|null
     */
    public static function GetInterfaceManagerObject($id)
    {
        $oInterface = null;
        $oInterfaceData = TdbCmsInterfaceManager::GetNewInstance();
        /** @var TdbCmsInterfaceManager $oInterfaceData */
        if ($oInterfaceData->Load($id)) {
            $sClassName = $oInterfaceData->fieldClass;
            $oInterface = new $sClassName();
            /* @var TCMSInterfaceManagerBase $oInterface */
            $oInterface->LoadFromRow($oInterfaceData->sqlData);
        }

        return $oInterface;
    }

    /**
     * factory that returns the interface subclass defined in the cms_interface_manager table
     * for this system name.
     *
     * @param int $sSystemName
     *
     * @return TCMSInterfaceManagerBase|null
     */
    public static function GetInterfaceManagerObjectBySystemName($sSystemName)
    {
        $oInterface = null;
        $oInterfaceData = TdbCmsInterfaceManager::GetNewInstance();
        /** @var TdbCmsInterfaceManager $oInterfaceData */
        if ($oInterfaceData->LoadFromField('systemname', $sSystemName)) {
            $sClassName = $oInterfaceData->fieldClass;
            $oInterface = new $sClassName();
            /* @var TCMSInterfaceManagerBase $oInterface */
            $oInterface->LoadFromRow($oInterfaceData->sqlData);
        }

        return $oInterface;
    }
}
