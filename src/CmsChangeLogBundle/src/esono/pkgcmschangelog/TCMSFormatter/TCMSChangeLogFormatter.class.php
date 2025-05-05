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
 * Formats values for display in e.g. a table or an export.
 * /**/
class TCMSChangeLogFormatter
{
    /**
     * @param string $sTableId
     *
     * @return string
     */
    public static function formatTableName($sTableId)
    {
        if (!$sTableId) {
            return '';
        }
        $oTableConf = new TCMSTableConf($sTableId);

        return $oTableConf->sqlData['translation'];
    }

    /**
     * @param string $sDateTime
     *
     * @return string
     */
    public static function formatDateTime($sDateTime)
    {
        if (!$sDateTime) {
            return '';
        }
        /* @var $oField TCMSFieldDateTime */
        $oField = new TCMSFieldDateTime();
        $oField->data = $sDateTime;

        return $oField->toString();
    }

    /**
     * @param string $sUserId
     *
     * @return string
     */
    public static function formatUser($sUserId)
    {
        if (!$sUserId) {
            return '';
        }
        $oUser = TdbCmsUser::GetNewInstance();
        $oUser->Load($sUserId);

        return $oUser->fieldFirstname.' '.$oUser->fieldName;
    }

    /**
     * @param string $sChangeType
     *
     * @return string|null
     */
    public static function formatChangeType($sChangeType)
    {
        switch ($sChangeType) {
            case 'UPDATE': return ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.state.changed');
            case 'INSERT': return ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.state.new');
            case 'DELETE': return ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.state.removed');
        }
    }

    /**
     * @param string $sFieldId
     *
     * @return string
     */
    public static function formatFieldName($sFieldId)
    {
        $oFieldConf = TdbCmsFieldConf::GetNewInstance($sFieldId);
        if (!$oFieldConf) {
            return '';
        }

        return $oFieldConf->fieldTranslation;
    }

    /**
     * @param string $sFieldId
     * @param string $sValue
     *
     * @return string
     */
    public static function formatFieldValue($sFieldId, $sValue)
    {
        $oFieldConf = TdbCmsFieldConf::GetNewInstance($sFieldId);
        if (!$oFieldConf) {
            return '';
        }
        $oTableConf = new TCMSTableConf($oFieldConf->fieldCmsTblConfId);

        $oField = $oTableConf->GetField($oFieldConf->fieldName, $oFieldConf);
        if (!$oField) {
            return '';
        }
        $oField->data = unserialize($sValue);

        return $oField->toString();
    }
}
