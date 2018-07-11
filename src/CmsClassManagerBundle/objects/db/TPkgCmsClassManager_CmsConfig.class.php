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
 * @deprecated since 6.2.0 - no longer used
 */
class TPkgCmsClassManager_CmsConfig extends TPkgCmsClassManager_CmsConfigAutoParent
{
    public static function TransformNoneDbClass(&$sClassName, &$sClassSubType, &$sClassType)
    {
        static $bLoadingTransformationManager = false;
        if (false == $bLoadingTransformationManager) {
            if (!class_exists('TdbPkgCmsClassManager', false)) {
                $bLoadingTransformationManager = true;
                TGlobal::LoadDBObjectClassDefinition('TdbPkgCmsClassManager', false, false);
                $bLoadingTransformationManager = false;
            }
            $sEntryPoint = TdbPkgCmsClassManager::GetEntryPointClassForClass($sClassName, $sClassSubType, $sClassType);
            if ($sEntryPoint) {
                $sClassName = $sEntryPoint;
                $sClassSubType = 'CMSDataObjects';
                $sClassType = 'Customer';
            }
        }
    }
}
