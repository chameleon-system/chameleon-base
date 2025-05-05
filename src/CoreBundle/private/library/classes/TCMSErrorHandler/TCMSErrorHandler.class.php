<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

class TCMSErrorHandler
{
    /**
     * this is the main function that will be called after triggering an error
     * you can define via whitelist wich error types should be logged e.g. E_ERROR or E_WARNING
     * you can also define via blacklist which messages should not be logged so you can prevent errorlog spamming
     * e.g. some warnings or notices could not be fixed and will also logged if you try to suppress them with a @ character.
     */
    public static function ShutdownHandler()
    {
        if (function_exists('error_get_last')) {
            $aError = error_get_last();

            $aServer = self::GetServerVariables();

            if (!empty($aError)) {
                $sErrorLogFilePath = ERROR_LOG_FOLDER.ERROR_LOG_FILE;

                if (self::IsErrorTypeInWhitelist($aError['type']) && !self::IsMessageBlacklisted($aError['message']) && self::PrepareDirectoryAndFile()) {
                    if (file_exists($sErrorLogFilePath) && filesize($sErrorLogFilePath) > MAX_ERROR_LOG_SIZE) {
                        if (!self::ShrinkErrorFile()) {
                            unlink($sErrorLogFilePath);
                        }
                    }
                    $pFile = fopen($sErrorLogFilePath, 'ab');
                    $sIdentifier = $aServer['REMOTE_ADDR'].' at '.date('Y-m-d H:i:s');
                    $oGlobal = TGlobal::instance();

                    fwrite($pFile, "===============================================================================\n");
                    fwrite($pFile, $sIdentifier."\n");
                    fwrite($pFile, 'errordetail: '.self::GetErrorTypeAsString($aError['type']).' '.print_r($aError, true)."\n");
                    fwrite($pFile, 'Request URI:  '.print_r($aServer['REQUEST_URI'], true)."\n");
                    fwrite($pFile, 'GET/POST:  '.print_r($oGlobal->GetUserData(), true)."\n");
                    fwrite($pFile, "===============================================================================\n\n");
                    fclose($pFile);
                    if (E_ERROR == $aError['type']) {
                        $sMessage = 'Error from '.$aServer['HTTP_HOST'].' ('.$sIdentifier.'): '.$aError['message']."\n\n";
                        $sMessage .= "Errordetail: \n\n";
                        $sMessage .= 'Filename: '.$aError['file']."\n";
                        $sMessage .= 'Line: '.$aError['line']."\n";
                        $sMessage .= 'Message: '.$aError['message']."\n";
                        $sMessage .= 'Referer URL: '.$aServer['HTTP_REFERER']."\n";
                        $sMessage .= 'Request URI: '.$aServer['REQUEST_URI']."\n";
                        $sMessage .= 'Identifier: '.$sIdentifier."\n";
                        $sMessage .= "\n";
                        $sMessage .= 'Note: There is an errorlog "'.$sErrorLogFilePath."\" where you can find error details.\n";
                        $sMessage .= 'Search for "'.$sIdentifier.'" to find the error.';

                        $sSubject = 'Error-Notification '.$aServer['HTTP_HOST'].': '.$aError['message'].' '.md5($aError['file'].$aError['line'].$aServer['REQUEST_URI']);

                        self::SendMailToDeveloper($sSubject, $sMessage);

                        if (file_exists($aServer['DOCUMENT_ROOT'].'/'.FATAL_PHP_FILE)) {
                            $sUrl = '/'.FATAL_PHP_FILE;
                            $sUrl .= '?sIdentifier='.urlencode($sIdentifier);
                            self::getRedirect()->redirect($sUrl, Response::HTTP_TEMPORARY_REDIRECT);
                        } else {
                            ob_end_clean();
                            echo 'The System crashed fatal';
                            echo 'The responsible developer is notified about your error via e-mail.<br /><br />';
                        }
                    }
                }
            }
        }
    }

    /**
     * transforms the error code into an human readable string (PHP constant name).
     *
     * @param int $iErrorType
     * @param array<int, string> $aErrorLookup - optional: your own error type lookuplist
     *
     * @return string
     */
    protected static function GetErrorTypeAsString($iErrorType, $aErrorLookup = [])
    {
        if (!is_array($aErrorLookup) || 0 == count($aErrorLookup)) {
            $aErrorLookup = [1 => 'E_ERROR', 2 => 'E_WARNING', 4 => 'E_PARSE', 8 => 'E_NOTICE', 16 => 'E_NOTICE', 32 => 'E_COMPILE_ERROR', 64 => 'E_COMPILE_WARNING', 256 => 'E_USER_ERROR', 512 => 'E_USER_WARNING', 1024 => 'E_USER_NOTICE', 2048 => 'E_STRICT', 4096 => 'E_RECOVERABLE_ERROR', 8192 => 'E_DEPRECATED', 16384 => 'E_USER_DEPRECATED', 30719 => 'E_ALL'];
        }
        if (array_key_exists($iErrorType, $aErrorLookup)) {
            $sErrorType = $aErrorLookup[$iErrorType];
        } else {
            $sErrorType = 'NOT DEFINED';
        }

        return $sErrorType;
    }

    /**
     * this function will move all available variable from $_SERVER to a custom array
     * we need to do it in this way because e.g. the HTTP_REFFER is not set everytime.
     *
     * @return array
     */
    protected static function GetServerVariables()
    {
        $aServerVariables = [];
        $aServerVariables['REMOTE_ADDR'] = self::GetVariableFromServerArray('REMOTE_ADDR');
        $aServerVariables['HTTP_REFERER'] = self::GetVariableFromServerArray('HTTP_REFERER');
        $aServerVariables['REQUEST_URI'] = self::GetVariableFromServerArray('REQUEST_URI');
        $aServerVariables['HTTP_HOST'] = self::GetVariableFromServerArray('HTTP_HOST');
        $aServerVariables['DOCUMENT_ROOT'] = self::GetVariableFromServerArray('DOCUMENT_ROOT');

        return $aServerVariables;
    }

    /**
     * tries to fetch the given key from the $_SERVER array - if the key does not exist it will return
     * an empty string.
     *
     * @param string $sKey
     *
     * @return string
     */
    protected static function GetVariableFromServerArray($sKey)
    {
        $sValue = '';
        if (array_key_exists($sKey, $_SERVER) && !empty($_SERVER[$sKey])) {
            $sValue = $_SERVER[$sKey];
        }

        return $sValue;
    }

    /**
     * checks the given error type for being in whitelist or not.
     *
     * @param int $iErrorType
     * @param array $aWhiteList - optional: your own whitelist with error types
     *
     * @return bool
     */
    protected static function IsErrorTypeInWhitelist($iErrorType, $aWhiteList = [])
    {
        if (!is_array($aWhiteList) || 0 == count($aWhiteList)) {
            $aWhiteList = [E_ERROR, E_PARSE, E_USER_ERROR];
        }

        return in_array($iErrorType, $aWhiteList);
    }

    /**
     * checks the given error message for being in blacklist or not with the use of a simple regex.
     *
     * @param string $sErrorMessage
     * @param array $aBlackList - optional: your own blacklist with messages
     *
     * @return bool
     */
    protected static function IsMessageBlacklisted($sErrorMessage = '', $aBlackList = [])
    {
        if (!is_array($aBlackList) || 0 == count($aBlackList)) {
            $aBlackList = ['open_basedir restriction in effect'];
        }

        $bMessageIsBlacklisted = false;
        foreach ($aBlackList as $sMessage) {
            if (preg_match('/.('.$sMessage.')./', $sErrorMessage)) {
                $bMessageIsBlacklisted = true;
            }
        }

        return $bMessageIsBlacklisted;
    }

    /**
     * checks for existence of the needed folder that is required for writing the file
     * checks for writing permissions
     * will also create folder if it does not exist
     * note: this will not create the file - should be done later when writing via fopen.
     */
    protected static function PrepareDirectoryAndFile()
    {
        $bLogFileWritable = false;
        umask(0);
        $sFolder = ERROR_LOG_FOLDER;
        if ('/' == substr($sFolder, -1)) {
            $sFolder = substr($sFolder, 0, -1);
        }

        if (!file_exists($sFolder)) {
            $sParentFolder = substr($sFolder, 0, strrpos($sFolder, '/'));
            if (is_writable($sParentFolder)) {
                mkdir($sFolder);
            }
        }

        if (!file_exists($sFolder.'/'.ERROR_LOG_FILE)) {
            $pTmp = fopen($sFolder.'/'.ERROR_LOG_FILE, 'w');
            fclose($pTmp);
        }

        if (file_exists($sFolder.'/'.ERROR_LOG_FILE) && is_writeable($sFolder.'/'.ERROR_LOG_FILE)) {
            $bLogFileWritable = true;
        }

        return $bLogFileWritable;
    }

    /**
     * Moves the current logfile to logfile.old (deletes any logfile.old if
     * existing).
     *
     * @return bool
     */
    protected static function ShrinkErrorFile()
    {
        $bSuccess = true;

        $sFileName = ERROR_LOG_FOLDER.ERROR_LOG_FILE;
        $sOldFileName = $sFileName.'.old';

        if (file_exists($sOldFileName)) {
            unlink($sOldFileName);
        }

        if (!rename($sFileName, $sOldFileName)) {
            $bSuccess = false;
        }

        return $bSuccess;
    }

    /**
     * sends an email to the developer email that is set in the config.inc.php in the config folder of the customer.
     *
     * @param string $sSubject
     * @param string $sMessage
     *
     * @return bool
     */
    protected static function SendMailToDeveloper($sSubject, $sMessage)
    {
        $bWasSend = false;
        $developmentEmail = ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.development_email');
        if (false !== $developmentEmail) {
            $bWasSend = mail($developmentEmail, $sSubject, $sMessage);
        }

        return $bWasSend;
    }

    /**
     * @return ICmsCoreRedirect
     */
    private static function getRedirect()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
