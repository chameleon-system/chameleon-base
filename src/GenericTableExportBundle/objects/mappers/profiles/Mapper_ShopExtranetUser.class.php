<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Mapper_ShopExtranetUser extends AbstractViewMapper
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
        $oRequirements->NeedsSourceObject('sqlData');
    }

    /**
     * If you want to use this mapper add this query to query field in your export itme.
     *
     * SELECT  `pkg_newsletter_user`. * , `pkg_newsletter_confirmation` . `registration_date` AS regDate  ,
     * `pkg_newsletter_confirmation` . `confirmation` AS confirmed  ,
     * `pkg_newsletter_confirmation` . `confirmation_date` AS confDate  ,
     * `pkg_newsletter_confirmation`.`pkg_newsletter_group_id` AS groupId
     * FROM `pkg_newsletter_user`
     * LEFT JOIN `pkg_newsletter_user_pkg_newsletter_confirmation_mlt` ON `pkg_newsletter_user_pkg_newsletter_confirmation_mlt`.`source_id` = `pkg_newsletter_user`.`id`
     * LEFT JOIN `pkg_newsletter_confirmation` ON `pkg_newsletter_confirmation`.`id` = `pkg_newsletter_user_pkg_newsletter_confirmation_mlt`.`target_id`
     * LEFT JOIN `pkg_newsletter_group` AS group_confirm ON `group_confirm`.`id` = `pkg_newsletter_confirmation`.`pkg_newsletter_group_id`
     * WHERE 1 = '1'
     *
     *
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
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        /** @var TdbDataExtranetUser $oExportData */
        $oExportData = $oVisitor->GetSourceObject('exportdata');
        $aBilling = [];
        $aShipping = [];
        if (null === $oExportData) {
            return;
        }
        $aSQLData = $oVisitor->GetSourceObject('sqlData');
        $oSalutation = $oExportData->GetFieldDataExtranetSalutation();
        if ($oSalutation) {
            $aSQLData['data_extranet_salutation_id'] = $oSalutation->GetName();
        }
        $oBillingAddress = $oExportData->GetFieldDefaultBillingAddress();
        if (false === is_null($oBillingAddress)) {
            $aBilling = $oBillingAddress->sqlData;
            $aBilling['data_extranet_salutation_id'] = $this->getSalutationName($oBillingAddress);
            $aBilling['data_country_id'] = $this->getCountryName($oBillingAddress);
        }
        $oShippingAddress = $oExportData->GetFieldDefaultShippingAddress();
        if (false === is_null($oShippingAddress)) {
            $aShipping = $oShippingAddress->sqlData;
            $aShipping['data_extranet_salutation_id'] = $this->getSalutationName($oShippingAddress);
            $aShipping['data_country_id'] = $this->getCountryName($oShippingAddress);
        }
        $oVisitor->SetMappedValue('sqlData', $aSQLData);
        $oVisitor->SetMappedValue('sqlDataShipping', $aShipping);
        $oVisitor->SetMappedValue('sqlDataBilling', $aBilling);
    }

    /**
     * @param TdbDataExtranetUserAddress $oAddress
     *
     * @return string
     */
    protected function getCountryName($oAddress)
    {
        $sCountry = '';
        $oCountry = $oAddress->GetFieldDataCountry();
        if (false === is_null($oCountry)) {
            $sCountry = $oCountry->GetName();
        }

        return $sCountry;
    }

    /**
     * @param TdbDataExtranetUserAddress $oAddress
     *
     * @return string
     */
    protected function getSalutationName($oAddress)
    {
        $sSalutation = '';
        $oSalutation = $oAddress->GetFieldDataExtranetSalutation();
        if (false === is_null($oSalutation)) {
            $sSalutation = $oSalutation->GetName();
        }

        return $sSalutation;
    }
}
