<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Mapper_ShopOrder extends AbstractViewMapper
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
     *
     * @abstract
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('exportdata');
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
        /** @var TdbShopOrder $oExportData */
        $oExportData = $oVisitor->GetSourceObject('exportdata');
        if (null === $oExportData) {
            return;
        }

        $oBillingSalutation = $oExportData->GetFieldAdrBillingSalutation();
        if ($oBillingSalutation) {
            $oVisitor->SetMappedValue('adr_billing_salutation', $oBillingSalutation->fieldName);
        }

        $oShippingSalutation = $oExportData->GetFieldAdrBillingSalutation();
        if ($oShippingSalutation) {
            $oVisitor->SetMappedValue('adr_shipping_salutation', $oShippingSalutation->fieldName);
        }

        $oBillingCountry = $oExportData->GetFieldAdrBillingCountry();
        if ($oBillingCountry) {
            $oVisitor->SetMappedValue('adr_billing_country', $oBillingCountry->fieldName);
        }

        $oShippingCountry = $oExportData->GetFieldAdrShippingCountry();
        if ($oShippingCountry) {
            $oVisitor->SetMappedValue('adr_shipping_country', $oShippingCountry->fieldName);
        }

        $aOrderItems = [];
        $oOrderItemList = $oExportData->GetFieldShopOrderItemList();
        while ($oOrderItem = $oOrderItemList->Next()) {
            $aOrderItems[$oOrderItem->id] = $oOrderItem->sqlData;
        }
        $oVisitor->SetMappedValue('aOrderItemList', $aOrderItems);
    }
}
