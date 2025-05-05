<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

abstract class AbstractPkgExtranetMapper_Address extends AbstractViewMapper
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
        $oRequirements->NeedsSourceObject('oAddressObject', 'TdbDataExtranetUserAddress');
    }

    /**
     * To map values from models to views the mapper has to implement iVisitable.
     * The ViewRender will pass a prepared MapperVisitor instance to the mapper.
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
        if ($bCachingEnabled) {
            /** @var TdbDataExtranetUserAddress $oAddress */
            $oAddress = $oVisitor->GetSourceObject('oAddressObject');
            if ($oAddress) {
                if (!empty($oAddress->id)) {
                    $oCacheTriggerManager->addTrigger($oAddress->table, $oAddress->id);
                }
                if (!empty($oAddress->fieldDataExtranetSalutationId)) {
                    $oCacheTriggerManager->addTrigger('data_extranet_salutation_id', $oAddress->fieldDataExtranetSalutationId);
                }
                if (!empty($oAddress->fieldDataCountryId)) {
                    $oCacheTriggerManager->addTrigger('data_country', $oAddress->fieldDataCountryId);
                }
            }
        }
    }
}
