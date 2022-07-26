<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Psr\Log\LoggerInterface;

class TPkgCsv2SqlManager
{
    /**
     * Mail profile.
     */
    const IMPORT_ERROR_LOG_MAIL = 'shop-import-data';
    /**
     * error-log filename.
     *
     * @deprecated since 6.3.0 - not used anymore
     */
    const IMPORT_ERROR_LOG_FILE = 'TPkgCsv2SqlManager.log';

    /**
     * call this method if you want to import all files (manages validation, and error handling)
     * note: code was moved from TPkgCsv2Sql_CmsListManagerPkgCsv2sql::ProcessImport so we can call it via cron-job as well.
     *
     * @static
     *
     * @return array<string, string|array>
     */
    public static function ProcessAll()
    {
        $aImportErrors = array();
        $aAllErr = array();

        $aData = array();
        $aData['oImportName'] = 'CSV-2-SQL Datenimport:';
        $aData['successMessage'] = '';

        $logger = self::getLogger();

        $logger->info('TPkgCsv2SqlManager: ProcessAll Start');
        $aValidationErrors = self::ValidateAll();
        $logger->info('TPkgCsv2SqlManager: ValidateAll end');
        if (0 == count($aValidationErrors)) {
            // all good, import
            $aImportErrors = self::ImportAll();
            $aData['successMessage'] = TGlobal::OutHTML(TGlobal::Translate('chameleon_system_csv2sql.msg.import_completed'));
        }
        $logger->info('TPkgCsv2SqlManager: ImportAll end');

        $aAllErr = TPkgCsv2Sql::ArrayConcat($aAllErr, $aValidationErrors);
        $aAllErr = TPkgCsv2Sql::ArrayConcat($aAllErr, $aImportErrors);
        if (count($aAllErr) > 0) {
            //send all errors by email
            self::SendErrorNotification($aAllErr);
        }
        $logger->info('TPkgCsv2SqlManager: SendErrorNotification end');

        //View vars
        $aData['aValidationErrors'] = (array) $aValidationErrors;
        $aData['aImportErrors'] = (array) $aImportErrors;

        return $aData;
    }

    /**
     * Import all csv files to database tables.
     *
     * Manager::ImportAll()
     * get list of import handler
     * set Log-File-name
     * for each handler, call Import()
     * if Log-File is not empty at end of Import, send E-Mail PKG-CSV-2-SQL-ERRORS-LOGGED and the Log-File as attachment
     *
     * @return array
     */
    public static function ImportAll()
    {
        $logger = self::getLogger();

        $aErrors = array();
        // get list of import handler
        /** @var $oCsv2SqlList TdbPkgCsv2sqlList */
        $oCsv2SqlList = TdbPkgCsv2sqlList::GetList();
        $oCsv2SqlList->GoToStart();
        while ($oListItem = $oCsv2SqlList->Next()) {
            $logger->info('TPkgCsv2SqlManager: Import '.$oListItem->fieldName);

            $aItemErrors = $oListItem->Import();
            $aErrors = TPkgCsv2Sql::ArrayConcat($aErrors, $aItemErrors);
        }

        return $aErrors;
    }

    /**
     * Validate all csv files.
     *
     * Manager::ValidateAll()
     *  get list of import handler
     *  all files found?
     *  YES: for every hanlder, call validate.
     *     merge validate results an return
     *  NO: only bestand present?
     *    YES: validate bestand and return result
     *    NO: generate error "PKG-CSV-2-SQL-MISSING-FILES" with list of files missing and return
     *
     *
     * @return array
     */
    public static function ValidateAll()
    {
        $logger = self::getLogger();

        $aErrors = array();

        //get list of import handler
        /** @var $oCsv2SqlList TdbPkgCsv2sqlList */
        $oCsv2SqlList = TdbPkgCsv2sqlList::GetList();
        $oCsv2SqlList->GoToStart();
        while ($oListItem = $oCsv2SqlList->Next()) {
            $logger->info('TPkgCsv2SqlManager: Validating '.$oListItem->fieldName);
            $aItemErrors = $oListItem->Validate();
            $aErrors = TPkgCsv2Sql::ArrayConcat($aErrors, $aItemErrors);
        }

        return $aErrors;
    }

    /**
     * Merge all logs to big one.
     *
     * @deprecated since 6.3.0 - not supported anymore
     *
     * @return void
     */
    protected static function MergeLogs()
    {
    }

    /**
     * Send import log (on error!).
     *
     * @param mixed[] $aErrors
     *
     * @return void
     */
    public static function SendErrorNotification($aErrors)
    {
        $sMailBody = __CLASS__.'-Report'."\r\n\r\n";
        $sErrorLines = '';
        if (is_array($aErrors) && count($aErrors)) {
            foreach ($aErrors as $iK => $mVal) {
                if (is_array($mVal)) {
                    $mVal = '<pre>'.print_r($mVal, true).'</pre>';
                }
                $sNum = str_pad((string) $iK, 5, ' ', STR_PAD_RIGHT);
                $sErrorLines .= $sNum.': '.$mVal."\r\n";
            }
        }
        $sMailBody .= $sErrorLines;

        //send report by mail
        $oMailProfile = TdbDataMailProfile::GetProfile(self::IMPORT_ERROR_LOG_MAIL);
        if (\count($aErrors) > 0) {
            $oMailProfile->SetSubject('FEHLER: '.$oMailProfile->sqlData['subject']);
            $sMailBody .= "\r\nACHTUNG: \r\nEs sind Fehler aufgetreten. Überprüfen Sie auch Log-Informationen.\r\n";
        }

        $aMailData = array('sReport' => '<pre>'.$sMailBody.'</pre>');
        $oMailProfile->AddDataArray($aMailData);
        $oMailProfile->SendUsingObjectView('emails', 'Customer');
    }

    private static function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('monolog.logger.csv2sql');
    }
}
