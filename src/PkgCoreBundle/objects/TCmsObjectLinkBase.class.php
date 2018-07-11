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
 * @deprecated since 6.2.0 - no longer used.
 */
class TCmsObjectLinkBase implements ICmsObjectLink
{
    /**
     * fetch the object record for given table name and id and return the link
     * the instantiated object must implement ICmsLinkableObject interface
     * otherwise an TCmsObjectLinkException_InvalidTargetClass exception will be thrown.
     *
     * @param string      $sTableName
     * @param string      $sId
     * @param bool        $bAbsolute  set to true to include the domain in the link
     * @param null|string $sAnchor
     *
     * @throws TCmsObjectLinkException_InvalidTargetClass
     *
     * @return string
     */
    public function getLink($sTableName, $sId, $bAbsolute = false, $sAnchor = null)
    {
        $oRecord = $this->getRecord($sTableName, $sId);
        if (null !== $oRecord && $oRecord instanceof ICmsLinkableObject) {
            $sLink = $oRecord->getLink($bAbsolute, $sAnchor);
        } else {
            throw new TCmsObjectLinkException_InvalidTargetClass('The object '.get_class($oRecord).' for table '.$sTableName.' must implement ICmsLinkableObject');
        }

        return $sLink;
    }

    /**
     * instantiate object for given table name and try to load the record given by $sId
     * returns null if object could not be instantiated or record could not be loaded.
     *
     * @param $sTableName
     * @param $sId
     *
     * @return mixed|null
     */
    private function getRecord($sTableName, $sId)
    {
        $oRecord = $this->getObject($sTableName);
        if (null !== $oRecord && false === $oRecord->Load($sId)) {
            $oRecord = null;
        }

        return $oRecord;
    }

    /**
     * transforms given table name into object class name and fetches a new instance
     * returns null if object could not be instantiated.
     *
     * @param $sTableName
     *
     * @return mixed|null
     */
    private function getObject($sTableName)
    {
        $oObject = null;
        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableName);
        if (!empty($sClassName)) {
            $oObject = call_user_func(array($sClassName, 'GetNewInstance'));
        }

        return $oObject;
    }
}
