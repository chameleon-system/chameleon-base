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
 * Mapper for changelog items. Needs the pkgCmsChangeLog package installed.
 * /**/
class Mapper_PkgCmsChangelog extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('exportdata');
        $oRequirements->NeedsSourceObject('sqlData');
    }

    /**
     * {@inheritdoc}
     *
     * To use this mapper add this query to query field in your export item:
     *
     *
     *    SELECT cs.cms_tbl_conf AS Geaenderte_Tabelle, cs.modified_name AS Geaenderter_Datensatz, cs.modify_date AS Aenderungsdatum, cs.cms_user AS Geaendert_durch, cs.change_type AS Aenderung, ci.cms_field_conf AS Geaendertes_Feld, ci.value_old AS Alter_Wert, ci.value_new AS Neuer_Wert
     *    FROM pkg_cms_changelog_set AS cs
     *    LEFT OUTER JOIN pkg_cms_changelog_item AS ci ON cs.id = ci.pkg_cms_changelog_set_id
     *    ORDER BY cs.modify_date DESC
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        /** @var TdbPkgNewsletterUser $oExportData */
        $oExportData = $oVisitor->GetSourceObject('exportdata');
        if (null === $oExportData) {
            return;
        }
        $aSQLData = $oVisitor->GetSourceObject('sqlData');

        $aSQLData['Geaenderte_Tabelle'] = TCMSChangeLogFormatter::formatTableName($aSQLData['Geaenderte_Tabelle']);
        $aSQLData['Geaendert_durch'] = TCMSChangeLogFormatter::formatUser($aSQLData['Geaendert_durch']);
        $aSQLData['Aenderung'] = TCMSChangeLogFormatter::formatChangeType($aSQLData['Aenderung']);
        $sFieldId = $aSQLData['Geaendertes_Feld'];
        $aSQLData['Geaendertes_Feld'] = TCMSChangeLogFormatter::formatFieldName($sFieldId);
        $aSQLData['Alter_Wert'] = TCMSChangeLogFormatter::formatFieldValue($sFieldId, $aSQLData['Alter_Wert']);
        $aSQLData['Neuer_Wert'] = TCMSChangeLogFormatter::formatFieldValue($sFieldId, $aSQLData['Neuer_Wert']);

        $oVisitor->SetMappedValue('sqlData', $aSQLData);
    }
}
