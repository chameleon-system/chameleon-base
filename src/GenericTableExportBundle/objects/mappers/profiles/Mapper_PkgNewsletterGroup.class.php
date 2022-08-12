<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Mapper_PkgNewsletterGroup extends AbstractViewMapper
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
     *
     * @param IMapperRequirementsRestricted $oRequirements
     *
     * @return void
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('exportdata');
        $oRequirements->NeedsSourceObject('sqlData');
    }

    /**
     * If you want to use this mapper add this query to query field in your export itme.
     *
     *
     *     SELECT  `pkg_newsletter_user`. * , `pkg_newsletter_confirmation` . `registration_date` AS regDate  ,
     *    `pkg_newsletter_confirmation` . `confirmation` AS confirmed  ,
     *    `pkg_newsletter_confirmation` . `confirmation_date` AS confDate  ,
     *    `pkg_newsletter_confirmation`.`pkg_newsletter_group_id` AS groupId
     *    FROM `pkg_newsletter_user`
     *    LEFT JOIN `pkg_newsletter_user_pkg_newsletter_confirmation_mlt` ON `pkg_newsletter_user_pkg_newsletter_confirmation_mlt`.`source_id` = `pkg_newsletter_user`.`id`
     *    LEFT JOIN `pkg_newsletter_confirmation` ON `pkg_newsletter_confirmation`.`id` = `pkg_newsletter_user_pkg_newsletter_confirmation_mlt`.`target_id`
     *    LEFT JOIN `pkg_newsletter_group` AS group_confirm ON `group_confirm`.`id` = `pkg_newsletter_confirmation`.`pkg_newsletter_group_id`
     *    WHERE 1 = '1'
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
     * @param \IMapperVisitorRestricted     $oVisitor
     * @param bool                          $bCachingEnabled      - if set to true, you need to define your cache trigger that invalidate the view rendered via mapper. if set to false, you should NOT set any trigger
     * @param IMapperCacheTriggerRestricted $oCacheTriggerManager
     *
     * @return void
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        /** @var TdbPkgNewsletterUser $oExportData */
        $oExportData = $oVisitor->GetSourceObject('exportdata');
        if (null === $oExportData) {
            return;
        }
        $aSQLData = $oVisitor->GetSourceObject('sqlData');
        $oSalutation = $oExportData->GetFieldDataExtranetSalutation();
        if ($oSalutation) {
            $aSQLData['data_extranet_salutation_id'] = $oSalutation->GetName();
        }
        $oNewsletterGroupList = $oExportData->GetFieldPkgNewsletterGroupList();
        $oNewsletterGroup = $oNewsletterGroupList->FindItemWithProperty('id', $oExportData->sqlData['groupId']);
        $aSQLData['groupName'] = '';
        if (false != $oNewsletterGroup) {
            $aSQLData['groupName'] = $oNewsletterGroup->GetName();
            $aSQLData['groupId'] = $oNewsletterGroup->id;
        }
        $oVisitor->SetMappedValue('sqlData', $aSQLData);
    }
}
