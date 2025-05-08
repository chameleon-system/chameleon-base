<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Exception\ModuleException;
use ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\Corebundle\Util\UrlUtil;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * static toolset.
 *
 * /**/
class TTools
{
    /**
     * retrieves the maximum file size that is possible to upload really in MB.
     *
     * @return int
     */
    public static function getUploadMaxSize()
    {
        $maxUploadSize = self::SanitizeIniNumberValue(ini_get('upload_max_filesize'));
        $maxPostSize = self::SanitizeIniNumberValue(ini_get('post_max_size'));

        return min($maxPostSize, $maxUploadSize);
    }

    /**
     * sanitizes an INI value resolving K,M and G suffixes to the corresponding value in Bytes.
     *
     * @param string $sValue - the value (usually received from ini_get())
     *
     * @return int
     */
    public static function SanitizeIniNumberValue($sValue)
    {
        $sValue = trim($sValue);
        $sSuffix = strtolower(substr($sValue, strlen($sValue) - 1, 1));
        if (!in_array($sSuffix, ['k', 'm', 'g'])) {
            return (int) $sValue;
        }
        $iValue = (int) substr($sValue, 0, strlen($sValue) - 1);
        switch ($sSuffix) {
            case 'g':
                $iValue *= 1024;
                // no break
            case 'm':
                $iValue *= 1024;
                // no break
            case 'k':
                $iValue *= 1024;
        }

        return $iValue;
    }

    /**
     * retrieves the maximum file size that is possible to upload in bytes.
     *
     * @return int
     */
    public static function GetUploadMaxSizeBytes()
    {
        $maxUploadSize = self::getUploadMaxSize();
        if (strlen($maxUploadSize) <= 4) { // it`s an MB for sure
            $maxUploadSize = ($maxUploadSize * 1024) * 1024; // convert to bytes
        }

        return (int) $maxUploadSize;
    }

    /**
     * marks a substring ($sKeyword) in the haystack ($sHTML) by placing $sMarkingStart and $sMarkingEnd around it
     * it will ONLY mark the substring between HTML Tags - so no Tags will be broken
     * The search is case in sensitive.
     *
     * @param string $sKeyword - the substring to mark
     * @param string $sHTML - the complete html string
     * @param string $sMarkingStart - start of marking
     * @param string $sMarkingEnd - end of marking
     *
     * @return string
     */
    public static function HighlightHTMLString($sKeyword, $sHTML, $sMarkingStart = '<span class="keyword">', $sMarkingEnd = '</span>')
    {
        $tmpSearchTerm = $sKeyword;
        $tmpSearchTerm = preg_quote($tmpSearchTerm, '/');
        $sPattern = "/([^>]*)>([^<]*)({$tmpSearchTerm})([^<]*)<([^>]*)/usi";

        $sReplacePatter = '$1>$2'.$sMarkingStart.$sKeyword.$sMarkingEnd.'$4<$5';

        return preg_replace($sPattern, $sReplacePatter, $sHTML);
    }

    /**
     * This method replaces invalid characters from a file name, like this:.
     *
     * - remove leading and trailing dots and whitespace
     * - remove schemes (everything looking like xxx:// at the beginning of the filename)
     * - replace forbidden characters with underscores, including spaces and all dots except the last
     * - the last dot is treated as file extension and thus preserved
     * - force file extension if specified (an existing file extension will be replaced; if the passed filename did not
     *   have an extension, the forced extension is appended)
     * - if the result is an empty filename, "none" is returned instead
     *
     * Allowed characters are 0-9, a-z, A-Z, underscore and hyphen.
     * The passed filename is treated as UTF-8.
     *
     * @param string $filename
     * @param string|null $forceExtension
     *
     * @return string
     */
    public static function sanitizeFilename($filename = '', $forceExtension = null)
    {
        $newFilename = trim($filename);
        if (null === $forceExtension && preg_match('#^[\w-]+$#', $filename)) {
            return $filename;
        }
        $newFilename = preg_replace('#^.*://#u', '', $newFilename);
        $newFilename = preg_replace('#^[\s.]*#u', '', $newFilename);
        $newFilename = preg_replace('#[\s.]*$#u', '', $newFilename);
        $lastDotPosition = mb_strrpos($newFilename, '.');
        $newFilename = preg_replace('#[^0-9a-zA-Z_-]#u', '_', $newFilename);
        if (empty($newFilename)) {
            $newFilename = 'none';
        }
        if (null === $forceExtension) {
            if (false !== $lastDotPosition) {
                $newFilename[$lastDotPosition] = '.';
            }
        } else {
            if (false !== $lastDotPosition) {
                $newFilename = substr($newFilename, 0, $lastDotPosition);
            }
            $newFilename .= '.';
            $newFilename .= $forceExtension;
        }

        return $newFilename;
    }

    /**
     * Converts an array to a string. objects will be serialized and base64_encoded.
     *
     * @param array $aArray
     *
     * @return string
     */
    public static function ArrayToString($aArray)
    {
        $sString = 'array(';
        $position = 0;
        $lastArrayPos = count($aArray) - 1;
        foreach (array_keys($aArray) as $key) {
            if (is_int($key)) {
                $sString .= $key.'=>';
            } else {
                $sString .= "'".$key."'=>";
            }
            if (is_array($aArray[$key])) {
                $sString .= self::ArrayToString($aArray[$key]);
            } elseif (is_null($aArray[$key])) {
                $sString .= 'null';
            } else {
                if (is_int($aArray[$key]) || is_float($aArray[$key])) {
                    $sString .= $aArray[$key];
                } elseif (is_object($aArray[$key])) {
                    $sString .= "'".base64_encode(serialize($aArray[$key]))."'";
                } else {
                    $tmpVal = str_replace("'", "\\'", $aArray[$key]);
                    $sString .= "'".$tmpVal."'";
                }
            }
            if ($position < $lastArrayPos) {
                $sString .= ', ';
            }
            ++$position;
        }
        $sString .= ')';

        return $sString;
    }

    /**
     * fetches contents of a module.
     *
     * @param string $sModule - class name of the module
     * @param string $sView - view to use
     * @param array $aParameters - other parameters (like instanceID)
     * @param string $sSpotName - module spot name
     *
     * @return string
     *
     * @throws ModuleException
     */
    public static function CallModule($sModule, $sView, $aParameters = [], $sSpotName = 'tmpmodule')
    {
        $oModuleLoader = self::GetModuleLoaderObject($sModule, $sView, $aParameters, $sSpotName);
        $oModuleLoader->InitModules($sSpotName);

        return $oModuleLoader->GetModule($sSpotName, true);
    }

    /**
     * Fetches a new module loader instance.
     *
     * @param string $sModule - class name of the module
     * @param string $sView - view to use
     * @param array $aParameters - other parameters (like instanceID)
     * @param string $sSpotName - module spot name
     *
     * @return TModuleLoader
     */
    public static function GetModuleLoaderObject($sModule, $sView, $aParameters = [], $sSpotName = 'tmpmodule')
    {
        $oModuleLoader = self::getSubModuleLoader();
        $aModuleParameters = ['model' => $sModule, 'view' => $sView];
        $aModuleParameters = array_merge($aModuleParameters, $aParameters);
        $moduleList = [$sSpotName => $aModuleParameters];
        $oModuleLoader->LoadModules($moduleList);

        return $oModuleLoader;
    }

    /**
     * fetches module object.
     *
     * @param string $sModule - class name of the module
     * @param string $sView - view to use
     * @param array $aParameters - other parameters (like instanceID)
     * @param string $sSpotName - module spot name
     *
     * @return TModelBase
     */
    public static function GetModuleObject($sModule, $sView, $aParameters = [], $sSpotName = 'tmpmodule')
    {
        $oModuleLoader = self::GetModuleLoaderObject($sModule, $sView, $aParameters, $sSpotName);
        $oModule = $oModuleLoader->GetPointerToModule($sSpotName);

        return $oModule;
    }

    /**
     * Returns true if the email address is valid.
     *
     * @param string $email
     *
     * @return bool
     */
    public static function IsValidEMail($email)
    {
        $validator = self::getValidator();
        $constraints = [
            new Email(),
            new NotBlank(),
        ];
        $errors = $validator->validate($email, $constraints);

        if ($errors->count() > 0) {
            return false;
        }

        /*
         * Split on @ characters that are not quoted with a backslash
         */
        $parts = preg_split('/(?<!\\\\)@/', $email);
        if (2 !== count($parts)) {
            return false;
        }

        list($localPart, $domainPart) = $parts;

        /*
         * Only allow characters specified by RFC 5322 in the local part. In theory other characters have been allowed
         * for some time now, but in practice this seems not to be supported by anything (including PHPMailer and
         * SwiftMailer), so we want to make sure that only email addresses are allowed that are very likely to be
         * "practically valid".
         * Unfortunately neither filter_var nor the Symfony email validator return results as we would like them (allow
         * special characters in the domain part, but not in the local part), so we need to use a custom regex.
         *
         * Differing from RFCs We also do not allow < and > to clearly only allow email addresses without a plain text
         * name.
         */
        if (0 === preg_match('/^[A-Za-z0-9!#$%&\'*+\/=?^_`{|}~."(),:;@[\]\\\\-]+$/', $localPart)) {
            return false;
        }

        /*
         * Disallow some more characters from the domain part (RFC 3696). We don't bother with escaping and other
         * shenanigans but simply reject addresses that include these characters. Seriously, who uses those?
         */
        if (1 === preg_match('/[;\/?:@&=+$, "<>#%]+/', $domainPart)) {
            return false;
        }

        return true;
    }

    /**
     * returns true if field exists in table
     * note: does not check existence of field configuration record if $bCheckConfig isn`t true.
     *
     * @param string $sTableName
     * @param string $sFieldName
     * @param bool $bCheckFieldConfig - optional param, if set to true cms_field_conf will be searched for the field instead of SHOW FIELDS
     *
     * @return bool
     */
    public static function FieldExists($sTableName, $sFieldName, $bCheckFieldConfig = false)
    {
        $returnVal = false;
        if ($bCheckFieldConfig) {
            $tableId = self::GetCMSTableId($sTableName);
            $query = "SELECT `id` FROM `cms_field_conf` WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($tableId)."' AND `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sFieldName)."'";
            $result = MySqlLegacySupport::getInstance()->query($query);
            if (1 === MySqlLegacySupport::getInstance()->num_rows($result)) {
                $returnVal = true;
            }
        } else {
            static $requestCache = [];
            if (array_key_exists($sTableName, $requestCache) && array_key_exists($sFieldName, $requestCache[$sTableName])) {
                $returnVal = $requestCache[$sTableName][$sFieldName];
            } else {
                $query = 'SHOW FIELDS FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."` LIKE '".MySqlLegacySupport::getInstance()->real_escape_string($sFieldName)."'";
                $result = MySqlLegacySupport::getInstance()->query($query);

                if (false === $result) {
                    throw new Exception('Error in query: '.$query.' - '.MySqlLegacySupport::getInstance()->error());
                }

                if (1 === MySqlLegacySupport::getInstance()->num_rows($result)) {
                    $returnVal = true;
                }

                if (!array_key_exists($sTableName, $requestCache)) {
                    $requestCache[$sTableName] = [];
                }
                $requestCache[$sTableName][$sFieldName] = $returnVal;
            }
        }

        return $returnVal;
    }

    /**
     * returns true if we are in the cms, and have requested to edit a page (ie, not view it).
     *
     * @return bool
     */
    public static function CMSEditRequest()
    {
        $oGlobal = TGlobal::instance();
        $requestModuleChooser = ($oGlobal->UserDataExists('__modulechooser') && ('true' == $oGlobal->GetUserData('__modulechooser')));

        $securityHelperAccess = self::getSecurityHelperAccess();

        return $requestModuleChooser && $securityHelperAccess->isGranted(CmsUserRoleConstants::CMS_USER);
    }

    /**
     * delete directory recursive (optional).
     *
     * @param string $path start path
     * @param bool $recursive delete recursive
     */
    public static function DelDir($path, $recursive = false)
    {
        if ($dir = @opendir($path)) {
            while (false !== ($file = readdir($dir))) {
                if ('.' !== $file && '..' !== $file) {
                    $fullPath = $path.'/'.$file;
                    if (is_file($fullPath)) {
                        unlink($fullPath);
                    } elseif (is_dir($fullPath) && $recursive) {
                        self::DelDir($fullPath, $recursive);
                    }
                }
            }
            closedir($dir);
            rmdir($path);
        }
    }

    /**
     * Does an mysql_real_escape_string to every element of an array.
     *
     * @param array $array
     *
     * @return array
     */
    public static function MysqlRealEscapeArray($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = MySqlLegacySupport::getInstance()->real_escape_string($array[$key]);
        }

        return $array;
    }

    /**
     * parse an url and extract it's arguments.
     *
     * @param string $url
     *
     * @return array
     */
    public static function GetURLArguments($url)
    {
        $args = [];
        $url = str_replace('&amp;', '&', $url);
        $processed_url = parse_url($url);
        if (isset($processed_url['query'])) {
            $query_string = $processed_url['query'];
            // split into arguments and values
            $query_string = explode('&', $query_string);
            $args = []; // return array

            foreach ($query_string as $chunk) {
                $chunk = explode('=', $chunk);
                // it's only really worth keeping if the parameter
                // has an argument.
                if (2 == count($chunk)) {
                    list($key, $val) = $chunk;
                    $args[$key] = urldecode($val);
                }
            }
        }

        return $args;
    }

    /**
     * Returns the lower case file extension of given filepath
     * handles also special cases like tar.gz.
     *
     * @param string $filename
     *
     * @return string|bool (returns false if no extension was found)
     */
    public static function GetFileExtension($filename)
    {
        $extension = false;
        $filename = strtolower($filename);
        $aPathInfo = pathinfo($filename);
        if (isset($aPathInfo) && is_array($aPathInfo) && array_key_exists('extension', $aPathInfo)) {
            $extension = $aPathInfo['extension'];
            if ('jpeg' === $extension) {
                $extension = 'jpg';
            } elseif ('jpe' === $extension) {
                $extension = 'jpg';
            } elseif ('tiff' === $extension) {
                $extension = 'tif';
            }

            // special cases
            if ('tar.gz' === substr($filename, -6)) {
                $extension = 'tar.gz';
            }
        }

        return $extension;
    }

    /**
     * get available CMS filetypes from database.
     *
     * @return array
     */
    public static function GetCMSFileTypes()
    {
        $file_types_array = [];
        $query = 'SELECT * FROM `cms_filetype`';
        $result = MySqlLegacySupport::getInstance()->query($query);

        while ($file_type = MySqlLegacySupport::getInstance()->fetch_assoc($result)) {
            $file_types_array[$file_type['id']] = $file_type['file_extension'];
        }

        return $file_types_array;
    }

    /**
     * validates an ISO/mysql date format e.g. 2010-12-31.
     *
     * @var string - date in ISO format e.g. 2010-12-31
     * @var bool - if true the date should be a birthday and cant`t be in the future
     *
     * @return bool
     */
    public static function ValidateISODate($sDate, $bCheckDateNotInFuture)
    {
        if (!preg_match('/^(\d\d\d\d)-(\d\d?)-(\d\d?)$/', $sDate, $aMatches)) {
            $bReturnVal = false;
        } else {
            $bReturnVal = checkdate($aMatches[2], $aMatches[3], $aMatches[1]);
        }
        if (true === $bReturnVal && true === $bCheckDateNotInFuture) { // check if date can`t be in the future
            $sTodaysDate = date('Y-m-d');
            $iTodayTimeStamp = strtotime($sTodaysDate);
            $iDateTimeStamp = strtotime($sDate);
            if ($iDateTimeStamp > $iTodayTimeStamp) {
                $bReturnVal = false;
            }
        }

        return $bReturnVal;
    }

    /**
     * returns an array of all known Zend related get/post variables to use it in a filter function.
     *
     * @return array
     */
    public static function GetZendDebugPostvarNames()
    {
        $excludeArray = ['FRQSTR', 'ZDEDebuggerPresent', 'debug_host', 'debug_fastfile', 'debug_port', 'start_debug', 'send_sess_end', 'debug_jit', 'original_url', 'debug_stop', 'send_debug_header'];

        return $excludeArray;
    }

    /**
     * returns the ISO code of the currently active language.
     *
     * @return string
     */
    public static function GetActiveLanguageIsoName()
    {
        $activeLanguage = self::getLanguageService()->getActiveLanguage();
        if (null === $activeLanguage) {
            return 'de'; // preserved BC; should be revised
        }

        return $activeLanguage->fieldIso6391;
    }

    /**
     * checks if a domain has valid syntax.
     *
     * @param string $sDomainName
     *
     * @return bool
     */
    public static function IsValidDomainName($sDomainName)
    {
        $m = '';

        return preg_match('/^(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i', $sDomainName, $m);
    }

    /**
     * converts an array (even multidimensional) to URL parameters
     * for javascript calls, please use TTools::GetArrayAsURLForJavascript().
     *
     * @param array $aData
     * @param string $prefix
     *
     * @return string
     *
     * @deprecated Use chameleon_system_core.util.url::getArrayAsUrl($aData, $prefix, '&amp;') instead
     */
    public static function GetArrayAsURL($aData, $prefix = '')
    {
        return self::getUrlUtil()->getArrayAsUrl($aData, $prefix, '&amp;');
    }

    /**
     * converts an array (even multidimensional) to URL parameters
     * for javascript calls, please use TTools::GetArrayAsURLForJavascript().
     *
     * @param array $aData
     *
     * @return string
     */
    public static function GetArrayAsFormInput($aData)
    {
        $sString = self::getUrlUtil()->getArrayAsUrl($aData);
        $aForm = [];
        $aParts = explode('&amp;', $sString);
        foreach ($aParts as $sLine) {
            $aLineParts = explode('=', $sLine);
            if (1 == count($aLineParts)) {
                $aLineParts[1] = '';
            }
            $aLineParts[0] = urldecode($aLineParts[0]);
            $aLineParts[1] = urldecode($aLineParts[1]);
            $sInputLine = '<input type="hidden" name="'.TGlobal::OutHTML($aLineParts[0]).'" value="'.TGlobal::OutHTML($aLineParts[1]).'" />';
            $aForm[] = $sInputLine;
        }

        return implode("\n", $aForm);
    }

    /**
     * return the array as URL - usable for javascript calls (the &amp; is converted to &).
     *
     * @static
     *
     * @param array $aData
     * @param string $prefix
     *
     * @return string
     *
     * @deprecated Use chameleon_system_core.util.url::getArrayAsUrl($aData, $prefix, '&') instead
     */
    public static function GetArrayAsURLForJavascript($aData, $prefix = '')
    {
        return self::getUrlUtil()->getArrayAsUrl($aData, $prefix, '&');
    }

    /**
     * calculates a faded hex colorcode.
     *
     * @param string $baseColor
     * @param int $opacity
     *
     * @return string hex colorcode
     */
    public static function getFadedColor($baseColor, $opacity)
    {
        $opacity = 100 - $opacity;
        $rgbValues = array_map('hexDec', str_split(ltrim($baseColor, '#'), 2));

        for ($i = 0, $len = count($rgbValues); $i < $len; ++$i) {
            $rgbValues[$i] = dechex(floor($rgbValues[$i] + (255 - $rgbValues[$i]) * ($opacity / 100)));
        }

        return '#'.implode('', $rgbValues);
    }

    /**
     * Generates a random password by $length.
     */
    public static function GenerateRandomPassword(int $length = 10, ?array $passwordChars = null): string
    {
        if (null === $passwordChars) {
            $passwordChars = array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z'), ['+', '-', '.']);
        }

        mt_srand((float) microtime() * 1000000);
        for ($i = 1; $i <= (count($passwordChars) * 2); ++$i) {
            $swap = mt_rand(0, count($passwordChars) - 1);
            $tmp = $passwordChars[$swap];
            $passwordChars[$swap] = $passwordChars[0];
            $passwordChars[0] = $tmp;
        }

        return substr(implode('', $passwordChars), 0, $length);
    }

    /**
     * generates a voucher code by $iLength.
     */
    public static function GenerateVoucherCode(int $length): string
    {
        $passwordChars = array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z'));

        return self::GenerateRandomPassword($length, $passwordChars);
    }

    /**
     * generates userfriendly temp passwords (should be changed by user) out of syllables.
     * the length of the password is at least $minLength. but the password can be longer!
     */
    public static function GenerateNicePassword(int $minLength = 10): string
    {
        $makepass = '';
        $syllables = 'er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se';
        $syllable_array = explode(',', $syllables);
        srand((float) microtime() * 1000000);
        while (strlen($makepass) <= $minLength) {
            if (1 === rand() % 10) {
                $makepass .= sprintf('%0.0f', (rand() % 50) + 1);
            } else {
                $makepass .= sprintf('%s', $syllable_array[rand() % 62]);
            }
        }

        return $makepass;
    }

    /**
     * parses an referrer URL for search engines and tries to find the keywords.
     *
     * @param string $referrer - must be a valid URL
     *
     * @return array|false - array('domain'=>'','keyword'=>'');
     */
    public static function referrer_analyzer($referrer)
    {
        $aReturnData = false;
        if (!empty($referrer)) {
            $aDomain = explode('/', $referrer);

            $aSearchEngines = [['google', 'q'], ['alltheweb', 'query'], ['altavista', 'q'], ['aol', 'query'], ['excite', 'search'], ['hotbot', 'query'], ['lycos', 'query'], ['yahoo', 'p'], ['t-online', 'q'], ['msn', 'q'], ['netscape', 'search']];

            $keyword = '';
            for ($i = 0; $i < count($aSearchEngines); ++$i) {
                if (preg_match('/'.$aSearchEngines[$i][0].'/', $referrer)) {
                    $parse = parse_url($referrer);
                    parse_str($parse['query'], $output);
                    $keyword = $output[$aSearchEngines[$i][1]];
                    break;
                }
            }

            $aReturnData = ['domain' => str_replace('www.', '', $aDomain[2]), 'keyword' => $keyword];
        }

        return $aReturnData;
    }

    /**
     * generates a Universally Unique Identifier (UUID) with optional prefix
     * e.g. atom feed uuid <id>urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6</id>.
     *
     * @param string $prefix
     *
     * @return string
     */
    public static function GetUUID($prefix = '')
    {
        $chars = bin2hex(openssl_random_pseudo_bytes(16));
        $uuid = substr($chars, 0, 8).'-';
        $uuid .= substr($chars, 8, 4).'-';
        $uuid .= substr($chars, 12, 4).'-';
        $uuid .= substr($chars, 16, 4).'-';
        $uuid .= substr($chars, 20, 12);

        return $prefix.$uuid;
    }

    /**
     * checks if an URL is available.
     *
     * @param string $url
     * @param int $timeout
     *
     * @return bool
     */
    public static function isOnline($url, $timeout = 30)
    {
        $isOnline = false;
        if (!$url_info = parse_url($url)) {
            return $isOnline;
        }

        if (false === array_key_exists('scheme', $url_info)) {
            return false;
        }
        if (false === in_array($url_info['scheme'], ['http', 'https'], true)) {
            return false;
        }

        $sendToHost = new TPkgCmsCoreSendToHost($url);
        $sendToHost->setMethod(TPkgCmsCoreSendToHost::METHOD_HEAD);
        $sendToHost->setTimeout($timeout);
        try {
            $sendToHost->executeRequest();

            return Response::HTTP_OK === $sendToHost->getLastResponseCode();
        } catch (TPkgCmsException_Log $e) {
            return false;
        }
    }

    /**
     * Fetches the id for a table name.
     *
     * @param bool $forceLoad - if true the local cache is ignored
     *
     * @throws InvalidArgumentException|Doctrine\DBAL\Exception if no table was found for $tableName
     */
    public static function GetCMSTableId(string $tableName, bool $forceLoad = false): string
    {
        static $tableIdCache = [];

        if (defined('CMSUpdateManagerRunning') && CMSUpdateManagerRunning === true) {
            $forceLoad = true;
        }

        if (false === $forceLoad && true === isset($tableIdCache[$tableName])) {
            return $tableIdCache[$tableName];
        }

        $query = 'SELECT `id` FROM `cms_tbl_conf` WHERE `name` = :tableName';
        $tableId = self::getDatabaseConnection()->fetchOne($query, [
            'tableName' => $tableName,
        ]);
        if (false === $tableId) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
            $traceInfo = array_map(static function ($trace) {
                return sprintf(
                    '%s%s%s:%d',
                    $trace['class'] ?? '',
                    $trace['type'] ?? '',
                    $trace['function'] ?? 'unknown',
                    $trace['line'] ?? 0
                );
            }, $backtrace);

            throw new InvalidArgumentException(sprintf(
                "Table '%s' not found. Call stack: %s",
                $tableName,
                implode(' -> ', $traceInfo)
            ));
        }
        $tableIdCache[$tableName] = $tableId;

        return $tableId;
    }

    /**
     * translates an upload error code ($_FILES['error']) into a text error code.
     *
     * @param int $iUploadErrorCode
     *
     * @return string
     */
    public static function GetUploadErrorText($iUploadErrorCode)
    {
        static $aCodeList;
        if (!$aCodeList) {
            $aCodeList = [
                UPLOAD_ERR_INI_SIZE => ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.upload_error_to_large'),
                UPLOAD_ERR_FORM_SIZE => ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.upload_error_to_large'),
                UPLOAD_ERR_PARTIAL => ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.upload_error_interrupted'),
                UPLOAD_ERR_NO_FILE => ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.upload_error_no_file'),
                UPLOAD_ERR_NO_TMP_DIR => ServiceLocator::get('translator')->trans(
                    'chameleon_system_core.field_document.upload_error_tmp_folder_not_writable'
                ),
                UPLOAD_ERR_CANT_WRITE => ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.upload_error_unable_to_save_to_disc'),
                UPLOAD_ERR_EXTENSION => ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.upload_error_invalid_file_extension'),
            ];
        }

        $sError = '';
        if (array_key_exists($iUploadErrorCode, $aCodeList)) {
            $sError = $aCodeList[$iUploadErrorCode];
        } else {
            $sError = ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.upload_error_unknown_error');
        }

        return $sError;
    }

    /**
     * return url required to call method sMethod on the currently executing module.
     *
     * @param string $sMethod - method name attached to module_fnc[spot]
     * @param array $aOtherParameters - any other parameters you want to add
     * @param bool $bUseFullURL - set to true if you want urls with domain
     *
     * @return string
     */
    public static function GetExecuteMethodOnCurrentModuleURL($sMethod, $aOtherParameters, $bUseFullURL = false)
    {
        $oGlobal = TGlobal::instance();

        $oModulePointer = $oGlobal->GetExecutingModulePointer();
        $sSpotName = $oModulePointer->sModuleSpotName;

        $aParamList = $aOtherParameters;
        if (!array_key_exists('module_fnc', $aParamList)) {
            $aParamList['module_fnc'] = [];
        }
        $aParamList['module_fnc'][$sSpotName] = $sMethod;
        if ($bUseFullURL) {
            return self::getActivePageService()->getLinkToActivePageAbsolute($aParamList, ['module_fnc']);
        } else {
            return self::getActivePageService()->getLinkToActivePageRelative($aParamList, ['module_fnc']);
        }
    }

    /**
     * pass the result of debug_backtrace().
     *
     * @param array $aDebugData
     *
     * @return string
     */
    public static function GetFormattedDebug($aDebugData = null)
    {
        $sResult = '';
        if (null === $aDebugData) {
            $aDebugData = debug_backtrace();
        }
        $sDocRoot = realpath(PATH_WEB);
        foreach ($aDebugData as $DebugDataItem) {
            $sFile = '';
            if (isset($DebugDataItem['file'])) {
                $sFile = './'.substr($DebugDataItem['file'], strlen($sDocRoot));
            }
            $sObjectName = '';
            $sLine = '';
            if (isset($DebugDataItem['line'])) {
                $sLine = $DebugDataItem['line'];
            }
            $sClass = '';
            if (array_key_exists('object', $DebugDataItem)) {
                $sObjectName = get_class($DebugDataItem['object']);
            }
            if (array_key_exists('class', $DebugDataItem)) {
                $sClass = false;
                if (is_object($DebugDataItem['class'])) {
                    $sClass = get_class($DebugDataItem['class']);
                }
                if (!$sClass) {
                    $sClass = $DebugDataItem['class'];
                }
            }
            $sResult .= "calling [{$DebugDataItem['function']}] in class [{$sClass}] (=object [{$sObjectName}]) from [{$sFile}] on line [{$sLine}]\n";
        }

        return $sResult;
    }

    /**
     * return a list of all public properties of an object (we need this in an
     * external method, since calling the method within the object will include
     * protected and private properties).
     *
     * @param object $oObject
     *
     * @return array
     */
    public static function GetPublicProperties($oObject)
    {
        return array_keys(get_object_vars($oObject));
    }

    /**
     * return the md5 insert ID based on the auto increment "cmsident" field last inserted.
     *
     * @param string $sTableName
     *
     * @return string
     */
    public static function GetMysqlInsertId($sTableName)
    {
        $sID = MySqlLegacySupport::getInstance()->insert_id();
        if (self::FieldExists($sTableName, 'cmsident')) {
            $query = 'SELECT `id` FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."` WHERE `cmsident`= '".MySqlLegacySupport::getInstance()->real_escape_string($sID)."'";
            if ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $sID = $aRow['id'];
            }
        }

        return $sID;
    }

    /**
     * returns the content of a field without any html and cutted to given length
     * without splitting a word
     * Optional custom variables can be placed into the wysiwyg editor - they will be replaced using the aCustomVariables passed.
     * These variables must have the following format: [{name:formatierung}]
     * "formatierung" ist either string, date, or number. It is possible to specify the number of decimals
     * used when formating a number: [{variable:number:decimalplaces}]
     * example [{costs:number:2}].
     *
     * @param int $length - max length of the text
     * @param string $sText
     * @param string $sAddToText - add to end of text if text is to long
     *
     * @return string - the cutted plain text
     */
    public static function StripTextWordSave($length, $sText, $sAddToText = '')
    {
        $content = strip_tags(trim($sText));
        $content = html_entity_decode($content, ENT_NOQUOTES, 'UTF-8');
        if (null !== $length && mb_strlen($content) > $length) {
            $content = mb_substr($content, 0, $length);
            $lastSpacePos = mb_strrpos($content, ' ');
            $content = mb_substr($content, 0, $lastSpacePos);
            $content .= $sAddToText;
        }

        return $content;
    }

    /**
     * returns true if the string passed matches our ID format.
     *
     * @param string $sStringToTest
     *
     * @return bool
     */
    public static function StringHasIDFormat($sStringToTest)
    {
        // d547350e-1d3e-9254-e3dd-f305a902eeef
        if (is_numeric($sStringToTest)) {
            return true;
        } elseif (36 === strlen($sStringToTest)) {
            if ('-' === substr($sStringToTest, 8, 1) && '-' === substr($sStringToTest, 13, 1) && '-' === substr($sStringToTest, 18, 1) && '-' === substr($sStringToTest, 23, 1)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Loads CSS file from URL and returns an array of classnames.
     * Tries to load the url from local path if the hostname is available in cms_portal_domains.
     *
     * @deprecated Use service chameleon_system_core.service.css_class_extractor method: extractCssClasses() instead.
     *
     * @param string $userCSSURL
     *
     * @return array
     */
    public static function GetClassNamesFromCSSFile($userCSSURL)
    {
        return ServiceLocator::get('chameleon_system_core.service.css_class_extractor')->extractCssClasses($userCSSURL);
    }

    /**
     * serializes data and encodes it with base64 for database storage.
     *
     * @return string
     */
    public static function mb_safe_serialize($data)
    {
        return base64_encode(serialize($data));
    }

    /**
     * deserializes data that is encoded with base64
     * includes fallback if data is not base64 encoded.
     *
     * @param string $data
     */
    public static function mb_safe_unserialize($data)
    {
        if (':' !== substr($data, 1, 1)) {
            $data = base64_decode($data);
        }
        if (!$sUnserializedData = unserialize($data)) {
            $data = preg_replace('!s:(\d+):"(.*?)";!se', '"s:".strlen("$2").":\"$2\";"', $data);
            $sUnserializedData = unserialize($data);
        }

        return $sUnserializedData;
    }

    /**
     * converts an ascii string to unicode.
     *
     * @param string $sText
     *
     * @return string
     */
    public static function unicode($sText)
    {
        $unicode = '';
        for ($i = 0; $i < $length = strlen($sText); ++$i) {
            $unicode .= '&#'.ord(substr($sText, $i, 1)).';';
        }

        return $unicode;
    }

    /**
     * Checks if record is currently locked by any other author than the currently
     * active user. Default lock timeout is 1 minute.
     *
     * @param string $sTableID
     * @param string $sRecordID
     *
     * @return TdbCmsLock|false - the lock record if found, else false
     */
    public static function IsRecordLocked($sTableID, $sRecordID)
    {
        $securityHelper = self::getSecurityHelperAccess();
        $userId = $securityHelper->getUser()?->getId();
        if (null === $userId) {
            $userId = '';
        }
        $lockActive = false;

        $query = "SELECT * FROM `cms_lock`
      WHERE `recordid` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRecordID)."'
      AND `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableID)."'
      AND `cms_user_id` != '".MySqlLegacySupport::getInstance()->real_escape_string($userId)."'
      AND TIMESTAMPDIFF(MINUTE,`time_stamp`,CURRENT_TIMESTAMP()) <= ".RECORD_LOCK_TIMEOUT.'
      ';

        $result = MySqlLegacySupport::getInstance()->query($query);
        if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
            $row = MySqlLegacySupport::getInstance()->fetch_assoc($result);
            $oCmsLock = TdbCmsLock::GetNewInstance();
            $oCmsLock->Load($row['id']);
            $lockActive = $oCmsLock;
        }

        return $lockActive;
    }

    /**
     * checks if record exists in table.
     *
     * @param string $sTableName
     * @param string $sFieldName
     * @param string $sFieldValue
     *
     * @return bool
     */
    public static function RecordExists($sTableName, $sFieldName, $sFieldValue)
    {
        $bRecordExists = false;
        $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'`
               WHERE  `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'`.`'.MySqlLegacySupport::getInstance()->real_escape_string($sFieldName)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sFieldValue)."'";
        $result = MySqlLegacySupport::getInstance()->query($query);
        if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
            $bRecordExists = true;
        }

        return $bRecordExists;
    }

    /**
     * if field based translation is active, then this will change the data in aField to
     * match the current language.
     *
     * @param array $aField - array of a list field (cms_tbl_display_list_fields -> name and db_alias are relevant)
     * @param TCMSTableConf $oTableConf - config object of the table
     *
     * @return array - array('name','direction')
     */
    public static function TransformFieldForTranslations($aField, $oTableConf = null)
    {
        if (null === $oTableConf) {
            return $aField;
        }
        $oCmsConfig = TdbCmsConfig::GetInstance();
        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $sActiveLanguage = $backendSession->getCurrentEditLanguageId();
        if ($sActiveLanguage != $oCmsConfig->sqlData['translation_base_language_id']) {
            $sActiveLanguagePrefix = TGlobal::GetLanguagePrefix($sActiveLanguage);
            $aTranslatableFields = $oCmsConfig->GetListOfTranslatableFields();

            $sTransTableName = $oTableConf->sqlData['name'];
            $sTransFieldName = $aField['db_alias'];
            // get table name
            $aTransParts = explode('.', $aField['name']);
            if (2 === count($aTransParts)) {
                $sTransTableName = str_replace('`', '', $aTransParts[0]);
                $sTransFieldName = str_replace('`', '', $aTransParts[1]);
            }
            if (array_key_exists($sTransTableName, $aTranslatableFields) && in_array($sTransFieldName, $aTranslatableFields[$sTransTableName])) {
                $databaseConnection = self::getDatabaseConnection();
                $fieldNameLang = $sTransFieldName.'__'.$sActiveLanguagePrefix;
                if (2 === count($aTransParts)) {
                    $quotedTransTableName = $databaseConnection->quoteIdentifier($sTransTableName);
                    $quotedFieldNameLang = $databaseConnection->quoteIdentifier($fieldNameLang);
                    $aField['name'] = "$quotedTransTableName.$quotedFieldNameLang";
                } else {
                    $aField['name'] = $databaseConnection->quoteIdentifier(str_replace('`', '', $aField['name']).'__'.$sActiveLanguagePrefix);
                }
            }
            if (self::titleNeedsTranslation($aField, $aTranslatableFields, $sActiveLanguagePrefix)) {
                $aField['title'] = $aField['title__'.$sActiveLanguagePrefix];
            }
        }

        return $aField;
    }

    /**
     * @param string[] $translatableFields
     * @param string $activeLanguagePrefix
     *
     * @return bool
     */
    private static function titleNeedsTranslation(array $field, array $translatableFields, $activeLanguagePrefix)
    {
        if (!isset($translatableFields['cms_tbl_display_list_fields'])) {
            return false;
        }
        if (!in_array('title', $translatableFields['cms_tbl_display_list_fields'])) {
            return false;
        }
        if (!isset($field['title'])) {
            return false;
        }
        if (false === CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE) {
            return true;
        }
        if (empty($field['title__'.$activeLanguagePrefix])) {
            return false;
        }

        return true;
    }

    /**
     * Get a table editor manager for given table name and record id.
     *
     * @param string $sTableName
     * @param string $sRecordId
     * @param string $sLanguageID - overwrites the user language and loads the record in this language instead
     *
     * @return TCMSTableEditorManager
     */
    public static function GetTableEditorManager($sTableName, $sRecordId = null, $sLanguageID = null)
    {
        $iTableID = self::GetCMSTableId($sTableName);
        $oTableEditor = new TCMSTableEditorManager();
        $oTableEditor->Init($iTableID, $sRecordId, $sLanguageID);

        return $oTableEditor;
    }

    /**
     * returns the chameleon temp directory
     * if missing, it tries to create it
     * if creation fails the server`s tmp directory will be returned
     * includes no trailing slash.
     *
     * @return string - should return something like: /var/www/yourProject/private/cmsdata/tmp
     */
    public static function GetTempDir()
    {
        $sWritableTempDir = '/tmp';
        // chameleon temp dir
        $sTempPath = realpath(CMS_TMP_DIR);
        if ($sTempPath && is_writable($sTempPath)) { // chameleon should have a writable temp dir in cmsdata/tmp
            $sWritableTempDir = $sTempPath;
        } else {
            if (!$sTempPath && mkdir($sTempPath)) { // try to create the missing temp dir
                $sWritableTempDir = $sTempPath;
            } else { // fetch the server wide temp dir (this is slow, but should never happen, if directory rights are set properly)
                $tmpfile = tempnam('dummy', '');
                $sWritableTempDir = dirname($tmpfile);
                unlink($tmpfile);
            }
        }

        return $sWritableTempDir;
    }

    /**
     * Converts a hex string to a decimal number using bcmath.
     *
     * @param string $sDecString
     *
     * @return string
     */
    public static function BcDec2Hex($sDecString)
    {
        $sHexResult = '';
        do {
            $sHexResult = sprintf('%02x', (int) bcmod($sDecString, 256)).$sHexResult;
            $sDecString = bcdiv($sDecString, 256);
        } while (bccomp($sDecString, 0));

        return ltrim($sHexResult, '0');
    }

    /**
     * Convert a hex value to long decimal value using bcmath.
     *
     * @param string $sHex
     *
     * @return string
     */
    public static function BcHexToLongDec($sHex)
    {
        $iHexLength = strlen($sHex);
        $sLongDec = '';
        for ($i = 1; $i <= $iHexLength; ++$i) {
            $sLongDec = bcadd($sLongDec, bcmul((string) hexdec($sHex[$i - 1]), bcpow('16', (string) ($iHexLength - $i))));
        }

        return $sLongDec;
    }

    /**
     * Removes UTF-8 BOM header from string.
     *
     * @param string $str
     *
     * @return string
     */
    public static function RemoveUTF8HeaderBomFromString($str = '')
    {
        if (substr($str, 0, 3) == pack('CCC', 0xEF, 0xBB, 0xBF)) {
            $str = substr($str, 3);
        }

        return $str;
    }

    /**
     * Function deletes a existing array key which was defined in given path.
     *
     * @param array $aDataArray
     * @param string $sDeleteArrayPath Path to delete in given array. (level1-level2-level3- for array("level1"=>array("level2"=>array("level3"=>test))))
     * @param string $sActiveArrayPath do not set this parameter manually on function call
     *
     * @return array
     */
    public static function DeleteArrayKeyByPath($aDataArray, $sDeleteArrayPath = '', $sActiveArrayPath = '')
    {
        foreach ($aDataArray as $sDataArrayKey => $sDataArrayValue) {
            if (is_array($sDataArrayValue)) {
                $sDataArrayValue = self::DeleteArrayKeyByPath($sDataArrayValue, $sDeleteArrayPath, $sActiveArrayPath.$sDataArrayKey.'-');
                $aDataArray[$sDataArrayKey] = $sDataArrayValue;
            } else {
                if ($sDeleteArrayPath === $sActiveArrayPath.$sDataArrayKey.'-') {
                    unset($aDataArray[$sDataArrayKey]);
                }
            }
        }

        return $aDataArray;
    }

    /**
     * removes whitespaces from all values of the array.
     *
     * @param array<array-key, string> $aData
     *
     * @return array<array-key, string>
     */
    public static function TrimArrayValues($aData)
    {
        if (is_array($aData) && count($aData) > 0) {
            foreach ($aData as $key => $val) {
                $aData[$key] = trim($val);
            }
            reset($aData);
        }

        return $aData;
    }

    /**
     * write a message to a log file. the file is created if it does not exist.
     *
     * @param string $sMessage
     * @param int $sLogLevel - 1=Error, 2=warning, 3=notice, 4=info
     * @param string $sCallFromFile - file the log request is called from
     * @param int $iLineNumber - line the log request is called from
     * @param string $sLogFileName - optional log file name (path is relative to PATH_CMS_CUSTOMER_DATA)
     *
     * @deprecated - use your own logger service (with appropriate channel) or 'logger' directly instead
     */
    public static function WriteLogEntry($sMessage, $sLogLevel, $sCallFromFile, $iLineNumber, $sLogFileName = null)
    {
        $logger = self::getLogger();

        if (null !== $sLogFileName) {
            $logger->warning(sprintf('Additional log file parameter %s to TTools::WriteLogEntry() ignored.', $sLogFileName));
        }

        switch ($sLogLevel) {
            case 1:
                $logger->error($sMessage);
                break;
            case 2:
                $logger->warning($sMessage);
                break;
            case 3:
                $logger->notice($sMessage);
                break;
            case 4:
                $logger->info($sMessage);
                break;
            case 5:
            default:
                $logger->debug($sMessage);
                break;
        }
    }

    /**
     * write a message to a log file. WITHOUT additional user/env information.
     *
     * @param string $sMessage
     * @param int $sLogLevel - 1=Error, 2=warning, 3=notice, 4=info
     * @param string $sCallFromFile - file the log request is called from
     * @param int $iLineNumber - line the log request is called from
     * @param string $sLogFileName - optional log file name (path is relative to PATH_CMS_CUSTOMER_DATA)
     *
     * @deprecated - use a logger service instead
     */
    public static function WriteLogEntrySimple($sMessage, $sLogLevel, $sCallFromFile, $iLineNumber, $sLogFileName = null)
    {
        self::WriteLogEntry($sMessage, $sLogLevel, $sCallFromFile, $iLineNumber, $sLogFileName);
    }

    /**
     * add variables to inject into the rendered page
     * IMPORTANT! the variables are inserted into the page AS-IS. So make sure you escape
     * them via TGlobal::OutHTML. use.
     *
     * @static
     *
     * @param array<string, string>|null $aVariables
     * @param bool $bEscapeViaOutHTML - set to true, if you want to pass each value through TGlobal::OutHTML
     *
     * @return array|null
     *
     * @deprecated since 6.3.0 - use ResponseVariableReplacerInterface::addVariable() instead (allows only string values,
     *             does not escape, does not return variables).
     */
    public static function AddStaticPageVariables($aVariables, $bEscapeViaOutHTML = false)
    {
        /** @var array<string, string> $aPageVars */
        static $aPageVars = [];

        if (is_array($aVariables)) {
            $responseVariableReplacer = self::getResponseVariableReplacer();
            foreach ($aVariables as $sKey => $value) {
                if ($bEscapeViaOutHTML) {
                    $aPageVars[$sKey] = TGlobal::OutHTML($aVariables[$sKey]);
                } else {
                    $aPageVars[$sKey] = $aVariables[$sKey];
                }
                $responseVariableReplacer->addVariable($sKey, (string) $aPageVars[$sKey]);
            }
        } elseif (null === $aVariables) {
            return $aPageVars;
        }
    }

    /**
     * checks if a record exists in table.
     *
     * @param string $sTableName
     * @param array<string, string> $aFieldsArray - array('fieldname'=>'fieldvalue')
     *
     * @return bool
     */
    public static function RecordExistsArray($sTableName, $aFieldsArray)
    {
        $bRecordExists = false;
        $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'` WHERE 1=1';
        foreach ($aFieldsArray as $sFieldName => $sFieldValue) {
            $query .= ' AND `'.MySqlLegacySupport::getInstance()->real_escape_string($sFieldName)."`='".MySqlLegacySupport::getInstance()->real_escape_string($sFieldValue)."'";
        }
        $result = MySqlLegacySupport::getInstance()->query($query);
        if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
            $bRecordExists = true;
        }

        return $bRecordExists;
    }

    /**
     * validates an IPv4 or IPv6 address
     * for IPv6 validation PHP 5.2 or newer is mandatory.
     *
     * @param string $ip
     *
     * @return bool
     */
    public static function IsValidIP($ip = '')
    {
        $bIsValid = false;
        if (!empty($ip)) {
            // if PHP is older than 5.2 we only support IPv4 checks
            if (!function_exists('filter_var')) {
                return ip2long($ip);
            }

            // check for IPv4 address
            if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                if (ip2long($ip)) {
                    $bIsValid = true;
                }
            } else {
                if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) { // check IPv6
                    if (false !== inet_pton($ip)) {
                        $bIsValid = true;
                    }
                }
            }
        }

        return $bIsValid;
    }

    /**
     * utf8 save replacement for str_word_count.
     *
     * @static
     *
     * @param string $string
     * @param int $format - 0: Returns the number of words, 1: Returns the words themselves, 2: Returns an array of words indexed by their position in the original string
     * @param string $charlist
     *
     * @psalm-param 0|1|2 $format
     *
     * @return array|int
     *
     * @psalm-return ($format is 0 ? int : ($format is 1 ? string[] : ($format is 2 ? array<int, string> : 0)))
     */
    public static function str_word_count_utf8($string, $format = 0, $charlist = '')
    {
        $aSplit = preg_split("/[^'\-A-Za-z".$charlist.']+/u', $string, -1, PREG_SPLIT_NO_EMPTY);
        if (2 == $format) {
            $aReturnArray = [];
            $iPos = 0;
            foreach ($aSplit as $sString) {
                $iPos = mb_strpos($string, $sString, $iPos, 'utf-8');
                $aReturnArray[$iPos] = $sString;
                $iPos += mb_strlen($sString, 'utf-8');
            }

            return $aReturnArray;
        } elseif (1 == $format) {
            return $aSplit;
        } elseif (0 == $format) {
            return count($aSplit);
        } else {
            return 0;
        }
    }

    /**
     * validates a vat id for different countries
     * if no iso country code ($sCountry) or country id ($sCountryId) is passed the country of the active billing address will be used.
     *
     * @param string $sVatId
     * @param string|null $sCountry iso code (2 characters) e.g. de
     * @param string|null $sCountryId
     *
     * @return false|int
     */
    public static function IsVatIdValid($sVatId, $sCountry = null, $sCountryId = null)
    {
        if (null === $sCountry && null === $sCountryId) {
            $oUser = TdbDataExtranetUser::GetInstance();
            if (null !== $oUser) {
                $oBillingAddress = $oUser->GetBillingAddress();
                if (null !== $oBillingAddress) {
                    $oCountry = $oBillingAddress->GetFieldDataCountry();
                    if (null !== $oCountry) {
                        $oTCountry = $oCountry->GetFieldTCountry();
                        if (null !== $oTCountry) {
                            $sCountry = $oTCountry->fieldIsoCode2;
                        }
                    }
                }
            }
        } elseif (null !== $sCountryId) {
            $oCountry = TdbDataCountry::GetNewInstance($sCountryId);
            $oTCountry = $oCountry->GetFieldTCountry();
            if (null !== $oTCountry) {
                $sCountry = $oTCountry->fieldIsoCode2;
            }
        }
        if ('' == $sCountry) {
            $sCountry = 'de';
        }

        // remove all non alphanumeric characters
        $sVatId = preg_replace('/[^a-zA-Z0-9]+/', '', $sVatId);
        switch (strtolower($sCountry)) {
            case 'be':
                $sRegex = '/^BE[0-9]{9,10}$/i'; // Belgien
                break;
            case 'dk':
                $sRegex = '/^DK[0-9]{8}$/i'; // Dänemark
                break;
            case 'de':
                $sRegex = '/^DE[0-9]{9}$/i'; // Deutschland
                break;
            case 'gb':
                $sRegex = '/^GB(GD[0-9]{3}|HA[0-9]{3}|[0-9]{9}|[0-9]{12})$/i'; // Vereinigtes Königreich
                break;
            case 'fi':
                $sRegex = '/^FI[0-9]{8}$/i'; // Finnland
                break;
            case 'fr':
                $sRegex = '/^FR[A-Z0-9]{2}[0-9]{9}$/i'; // Frankreich
                break;
            case 'it':
                $sRegex = '/^IT[0-9]{11}$/i'; // Italien
                break;
            case 'lu':
                $sRegex = '/^LU[0-9]{8}$/i'; // Luxemburg
                break;
            case 'nl':
                $sRegex = '/^NL[0-9]{9}B[0-9]{2}$/i'; // Niederlande
                break;
            case 'at':
                $sRegex = '/^ATU[0-9]{8}$/i'; // Österreich
                break;
            case 'pt':
                $sRegex = '/^PT[0-9]{9}$/i'; // Portugal
                break;
            case 'se':
                $sRegex = '/^SE[0-9]{10}01$/i'; // Schweden
                break;
            case 'es':
                $sRegex = '/^ES[A-Z0-9][0-9]{7}[A-Z0-9]$/i'; // Spanien
                break;
            case 'ie':
                $sRegex = '/^IE[0-9][A-Z0-9][0-9]{5}[A-Z]$/i'; // Irland
                break;
            case 'gr':
                $sRegex = '/^EL[0-9]{9}$/i'; // Griechenland
                break;
            case 'pl':
                $sRegex = '/^PL[0-9]{10}$/i'; // Polen
                break;
            case 'cz':
                $sRegex = '/^CZ[0-9]{8,10}$/i'; // Tschechien
                break;
            case 'sk':
                $sRegex = '/^SK[0-9]{10}$/i'; // Slowakei
                break;
            case 'hu':
                $sRegex = '/^HU[0-9]{8}$/i'; // Ungarn
                break;
            case 'si':
                $sRegex = '/^SI[0-9]{8}$/i'; // Slowenien
                break;
            case 'bg':
                $sRegex = '/^BG[0-9]{9,10}$/i'; // Bulgarien
                break;
            case 'ro':
                $sRegex = '/^RO[0-9]{2,10}$/i'; // Rumänien
                break;
            case 'lt':
                $sRegex = '/^LT([0-9]{9}|[0-9]{12})$/i'; // Litauen
                break;
            case 'lv':
                $sRegex = '/^LV[0-9]{11}$/i'; // Lettland
                break;
            case 'ee':
                $sRegex = '/^EE[0-9]{9}$/i'; // Estland
                break;
            case 'mt':
                $sRegex = '/^MT[0-9]{8}$/i'; // Malta
                break;
            case 'cy':
                $sRegex = '/^CY[0-9]{8}[A-Z]$/i'; // Zypern
                break;
            default:
                return false;
        }

        return preg_match($sRegex, $sVatId);
    }

    /**
     * return a per request unique ID.
     *
     * @static
     *
     * @return string
     */
    public static function GetProcessId()
    {
        static $sId = null;
        if (null === $sId) {
            $sId = self::GetUUID();
        }

        return $sId;
    }

    /**
     * Takes an array in the form array(array('weight'=>weight,'value'=>$value),array('weight'=>weight,'value'=>$anotherValue), ...) and
     * returns the value of a randomly selected value respecting weights from the input array.
     *
     * @static
     *
     * @template T
     *
     * @param array{weight: numeric, value: T}[] $aArray
     *
     * @return T
     */
    public static function GetWeightedRandomArrayValue($aArray)
    {
        shuffle($aArray);
        usort($aArray, ['TTools', '_CompareByWeight']);

        // $aArray = array_reverse($aArray);
        return $aArray[0]['value'];
    }

    /**
     * compares values by weight-random.
     *
     * @static
     *
     * @param array{weight: numeric} $a
     * @param array{weight: numeric} $b
     *
     * @return int
     */
    public static function _CompareByWeight($a, $b)
    {
        mt_srand((float) microtime() * 1000000);
        $iWeightA = $a['weight'];
        $iWeightB = $b['weight'];

        return mt_rand(0, $iWeightA + $iWeightB) > $iWeightA ? 1 : -1;
    }

    /**
     * checks string for GUID pattern like "A98C5A1E-A742-4808-96FA-6F409E799937".
     *
     * @param string $sID
     *
     * @return bool
     */
    public static function isValidUUID($sID)
    {
        $bIsValid = false;
        if (preg_match('/^[A-Z0-9]{8}-(?:[A-Z0-9]{4}-){3}[A-Z0-9]{12}/', strtoupper($sID))) {
            $bIsValid = true;
        }

        return $bIsValid;
    }

    private static function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }

    private static function getResponseVariableReplacer(): ResponseVariableReplacerInterface
    {
        return ServiceLocator::get('chameleon_system_core.response.response_variable_replacer');
    }

    private static function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }

    private static function getValidator(): ValidatorInterface
    {
        return Validation::createValidator();
    }

    private static function getActivePageService(): ActivePageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    private static function getLanguageService(): LanguageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }

    private static function getSubModuleLoader(): TUserModuleLoader
    {
        return ServiceLocator::get('chameleon_system_core.subusermoduleloader');
    }

    private static function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }

    private static function getSecurityHelperAccess(): SecurityHelperAccess
    {
        return ServiceLocator::get(SecurityHelperAccess::class);
    }
}
