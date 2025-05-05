<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCmsConfigImagemagick extends TCmsConfigImagemagickAutoParent
{
    private static $EnableAffects = false;

    public static function SetEnableEffects($bEnableEffects)
    {
        self::$EnableAffects = $bEnableEffects;
    }

    public static function GetEnableEffects()
    {
        return self::$EnableAffects;
    }

    /**
     * return matching instance for image size.
     *
     * @static
     *
     * @param int $iWidth
     * @param int $iHeight
     *
     * @return TdbCmsConfigImagemagick
     */
    public static function GetActiveInstance($iWidth, $iHeight)
    {
        if (false === self::GetEnableEffects()) {
            return null;
        }
        static $aSizes = null;
        $iImageSize = (int) $iWidth * $iHeight;
        if (is_null($aSizes)) {
            $aSizes = TdbCmsConfigImagemagick::GetSizeDefinitionAsArray();
        }
        $oSize = null;
        reset($aSizes);
        if (count($aSizes) > 0) {
            $oSmallestSize = null;
            foreach (array_keys($aSizes) as $iSize) {
                $iSizeAsNumber = (int) $iSize;
                if (is_null($oSmallestSize) || $oSmallestSize->fieldFromImageSize > $iSizeAsNumber) {
                    $oSmallestSize = $aSizes[$iSize];
                }
                if ($iSizeAsNumber <= $iImageSize) {
                    $oSize = $aSizes[$iSize];
                    break;
                }
            }
        }

        return $oSize;
    }

    /**
     * @return array|null
     */
    public static function GetSizeDefinitionAsArray()
    {
        $aSizeDef = [];
        $oSizes = TdbCmsConfigImagemagickList::GetList();
        $oSizes->ChangeOrderBy(['`cms_config_imagemagick`.`from_image_size`' => 'DESC']);
        while ($oSize = $oSizes->Next()) {
            $aSizeDef[$oSize->fieldFromImageSize] = $oSize;
        }

        return $aSizeDef;
    }
}
