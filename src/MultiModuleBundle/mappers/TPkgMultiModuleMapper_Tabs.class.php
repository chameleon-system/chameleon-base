<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgMultiModuleMapper_Tabs extends AbstractViewMapper
{
    /**
     * A mapper has to specify its requirements by providing th passed MapperRequirements instance with the
     * needed information and returning it.
     *
     * example:
     *
     * $oRequirements->NeedsSourceObject("foo",'stdClass','default-value');
     * $oRequirements->NeedsSourceObject("bar");
     * $oRequirements->NeedsMappedValue("baz");
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('aSetItems', 'array'); // array of TdbPkgMultiModuleSetItem
        $oRequirements->NeedsSourceObject('sContent', 'string'); // active content
        $oRequirements->NeedsSourceObject('bNoActive', 'boolean', false); // show no tab as active
    }

    /**
     * To map values from models to views the mapper has to implement iVisitable.
     * The ViewRender will pass a prepared MapeprVisitor instance to the mapper.
     *
     * The mapper has to fill the values it is responsible for in the visitor.
     *
     * example:
     *
     * $foo = $oVisitor->GetSourceObject("foomodel")->GetFoo();
     * $oVisitor->SetMapperValue("foo", $foo);
     *
     *
     * To be able to access the desired source object in the visitor, the mapper has
     * to declare this requirement in its GetRequirements method (see IViewMapper)
     *
     * @param bool $bCachingEnabled - if set to true, you need to define your cache trigger that invalidate the view rendered via mapper. if set to false, you should NOT set any trigger
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        $aTabHeaderList = [];
        $oVisitor->SetMappedValue('sContent', $oVisitor->GetSourceObject('sContent'));

        /** @var array<string, TdbPkgMultiModuleSetItem>  $aSetItems */
        $aSetItems = $oVisitor->GetSourceObject('aSetItems');

        /** @var bool $bShowNoActive */
        $bShowNoActive = $oVisitor->GetSourceObject('bNoActive');

        $bActive = true;
        if ($bShowNoActive) {
            $bActive = false;
        }
        foreach ($aSetItems as $sSetItemId => $oSetItem) {
            if ($oSetItem && $bCachingEnabled) {
                $oCacheTriggerManager->addTrigger($oSetItem->table, $oSetItem->id);
            }

            $sURL = $oSetItem->GetFieldAlternativeTabUrlForAjaxPageURL();
            if (empty($sURL)) {
                $sURL = '#';
            }

            $aTabHeader = [
                'sTitle' => $oSetItem->fieldName,
                'bIsActive' => $bActive,
                'sURL' => $sURL,
                'sAjaxURL' => $oSetItem->GetAjaxURLForContainingModule(false),
                'sClass' => str_replace(' ', '', $oSetItem->fieldName),
                'sSystemName' => $oSetItem->fieldSystemName,
            ];
            $aTabHeaderList[] = $aTabHeader;
            $bActive = false;
        }

        $oVisitor->SetMappedValue('aTabs', $aTabHeaderList);
    }
}
