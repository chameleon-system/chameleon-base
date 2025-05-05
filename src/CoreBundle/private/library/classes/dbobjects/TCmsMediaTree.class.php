<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;

class TCmsMediaTree extends TCmsMediaTreeAutoParent
{
    public function GetFullServerPath()
    {
        $sPath = realpath(PATH_OUTBOX_MEDIA_LIBRARY_SEO_LINKS);
        $sPath = $sPath.$this->GetPathCache();

        return $sPath;
    }

    public function GetNodeNameAsDirName()
    {
        $sName = trim($this->fieldName);
        $sName = $this->getUrlNormalizationUtil()->normalizeUrl($sName);
        $sName = mb_strtolower($sName);
        if (empty($sName)) {
            $sName = '-';
        }
        $sName = TTools::sanitizeFilename($sName);
        if (empty($this->fieldParentId)) {
            $sLangIsoName = TGlobal::GetLanguagePrefix();
            if (!empty($sLangIsoName) && !empty($sLangIsoName)) {
                $sName = $sName.'/'.$sLangIsoName;
            }
        }

        return $sName;
    }

    /**
     * @return string
     */
    public function GetPathCache()
    {
        if (empty($this->fieldPathCache) || is_null($this->fieldPathCache)) {
            $this->fieldPathCache = $this->GetNodeNameAsDirName();
            $sParentPath = '';
            if (!empty($this->fieldParentId)) {
                $oParent = $this->GetFieldParent();
                if ($oParent) {
                    $sParentPath = $oParent->GetPathCache();
                    if ('/' != substr($sParentPath, -1)) {
                        $sParentPath .= '/';
                    }
                }
            }
            if (!empty($sParentPath)) {
                $this->fieldPathCache = $sParentPath.$this->fieldPathCache;
            } else {
                $this->fieldPathCache = '/'.$this->fieldPathCache;
            }
            $this->sqlData['path_cache'] = $this->fieldPathCache;
        }

        return $this->fieldPathCache;
    }

    /**
     * @return UrlNormalizationUtil
     */
    private function getUrlNormalizationUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }
}
