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
class TCacheManagerStorage_Decorator implements ICacheManagerStorage
{
    /**
     * @var ICacheManagerStorage
     */
    private $oStorage = null;

    /**
     * decorate a storage.
     *
     * @param ICacheManagerStorage $oStorage
     *
     * @return ICacheManagerStorage
     */
    public function Decorate(ICacheManagerStorage $oStorage)
    {
        $this->oStorage = $oStorage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function GetContents($sKey)
    {
        return $this->oStorage->GetContents($sKey);
    }

    /**
     * {@inheritdoc}
     */
    public function SetContent($key, &$oContent, $aTableInfos = null, $isPage = false, $cleanKey = null, $iMaxLiveInSeconds = null)
    {
        return $this->oStorage->SetContent($key, $oContent, $aTableInfos, $isPage, $cleanKey, $iMaxLiveInSeconds);
    }

    /**
     * {@inheritdoc}
     */
    public function DeleteContent($key, $bIsInternalCall = false)
    {
        return $this->oStorage->DeleteContent($key, $bIsInternalCall);
    }

    /**
     * {@inheritdoc}
     */
    public function ClearCache()
    {
        return $this->oStorage->ClearCache();
    }

    /**
     * {@inheritdoc}
     */
    public function WriteMetaData($sKey, $aTableInfos = null, $isPage = false, $cleanKey = null, $iMaxLiveInSeconds = null, $sCacheContent = '')
    {
        return $this->oStorage->WriteMetaData($sKey, $aTableInfos, $isPage, $cleanKey, $iMaxLiveInSeconds, $sCacheContent);
    }
}
