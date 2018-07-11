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
interface ICacheManagerStorage
{
    /**
     * @param string $sKey
     *
     * @return mixed
     */
    public function GetContents($sKey);

    /**
     * @param string      $key
     * @param mixed       $oContent
     * @param array|null  $aTableInfos
     * @param bool        $isPage
     * @param string|null $cleanKey
     * @param int|null    $iMaxLiveInSeconds
     *
     * @return bool
     */
    public function SetContent($key, &$oContent, $aTableInfos = null, $isPage = false, $cleanKey = null, $iMaxLiveInSeconds = null);

    /**
     * @param string $key
     * @param bool   $bIsInternalCall
     *
     * @return bool
     */
    public function DeleteContent($key, $bIsInternalCall = false);

    public function ClearCache();

    /**
     * @param string      $sKey
     * @param array|null  $aTableInfos
     * @param bool        $isPage
     * @param string|null $cleanKey
     * @param int|null    $iMaxLiveInSeconds
     * @param string      $sCacheContent
     *
     * @return mixed
     */
    public function WriteMetaData($sKey, $aTableInfos = null, $isPage = false, $cleanKey = null, $iMaxLiveInSeconds = null, $sCacheContent = '');
}
