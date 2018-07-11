<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsTextBlockCmsPortal extends TPkgCmsTextBlockCmsPortalAutoParent
{
    /**
     * Get array with rendered text blocks from portal.
     *
     * @param int $iWidth
     *
     * @return array
     */
    public function GetPortalCmsTextBlockArray($iWidth = 600)
    {
        $key = array('class' => __CLASS__, 'method' => 'GetPortalCmsTextBlockArray', 'id' => $this->id, 'width' => $iWidth);
        $cache = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.cache');
        $cacheKey = $cache->getKey($key);
        $aTextBlockArray = $cache->get($cacheKey);

        if (null === $aTextBlockArray) {
            $oTextBlockList = $this->GetPortalCmsTextBlockList();
            $aTextBlockArray = $oTextBlockList->GetRenderedTextBlockArray($iWidth);
            $trigger = array(array('table' => 'pkg_cms_text_block', 'id' => null));
            $cache->set($cacheKey, $aTextBlockArray, $trigger);
        }

        return $aTextBlockArray;
    }

    /**
     *Get all text blocks belong to portal.
     *
     * @return TdbPkgCmsTextBlockList
     */
    protected function GetPortalCmsTextBlockList()
    {
        $oTextBlockList = $this->GetFromInternalCache(sha1($this->id.'_PortalTextBlockList'));
        if (is_null($oTextBlockList)) {
            $oTextBlockList = TdbPkgCmsTextBlockList::GetPortalTextBlockList($this->id);
            $this->SetInternalCache(sha1($this->id.'_PortalTextBlockList'), $oTextBlockList);
        }

        return $oTextBlockList;
    }

    /**
     * Get single text block by system name.
     *
     * @param string $sSystemName
     * @param int    $iWidth
     *
     * @return string
     */
    public function GetPortalCmsTextBlockText($sSystemName, $iWidth = 600)
    {
        $sTextBlockText = $this->GetFromInternalCache(sha1($this->id.'_PortalTextBlockText'.$iWidth.$sSystemName));
        if (is_null($sTextBlockText)) {
            // try to load from cache
            $oTextBlock = TdbPkgCmsTextBlock::GetInstanceFromSystemName($sSystemName, $this->id);
            if ($oTextBlock) {
                $sTextBlockText = $oTextBlock->Render('standard', 'Customer', array('iWidth' => $iWidth));
            } else {
                $sTextBlockText = TGlobal::Translate('chameleon_system_cms_text_block.error.not_found', array('%name%' => $sSystemName));
            }
            $this->SetInternalCache(sha1($this->id.'_PortalTextBlockText'.$iWidth.$sSystemName), $sTextBlockText);
        }

        return $sTextBlockText;
    }
}
