<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use Symfony\Contracts\Translation\TranslatorInterface;

class antiSpam
{
    /**
     * default strings to replace the at symbol and dot symbol.
     *
     * @var array
     */
    protected $aDefaultReplacementStrings = array('sDotSymbol' => 'chameleon_system_core.anti_spam.dot', 'sAtSymbol' => 'chameleon_system_core.anti_spam.at');

    /**
     * generated uuid strings to replace the at symbol and dot symbol.
     *
     * @var array
     */
    public static $aGeneratedReplacementStrings = array();

    /**
     * @param string|null $sEmail
     * @param string|null $sName
     * @param array       $aLinkAttributes
     *
     * @return string|null
     */
    public function ShowLink($sEmail = null, $sName = null, $aLinkAttributes = array())
    {
        if (!is_null($sEmail)) {
            $aEncodedEmail = explode('?', $sEmail);
            $sEncodedEmail = $this->EncodeEmail($aEncodedEmail[0]);
            if (count($aEncodedEmail) > 1) {
                $sEncodedEmail .= '?'.$aEncodedEmail[1];
            }
            // check if linked text is an email address so we may need to encode it, too
            // yipp, regex would be nicer here, but it works; Sven
            if (null !== $sName && count(explode('@', $sName)) > 1) {
                $sName = $this->EncodeEmail($sName);
            }
            $sEncodedMailTo = '<a href="mailto:'.$sEncodedEmail.'" '.$this->mergeLinkAttributes($aLinkAttributes, true).'>'.$sName.'</a>';

            return $sEncodedMailTo;
        } else {
            return null;
        }
    }

    /**
     * creates a string of attributes given in parameter $aLinkAttributes except href attribute.
     *
     * @param array $aLinkAttributes
     * @param bool  $bAddAntiSpamClass add antispam css class to the class attribute if true
     *
     * @return string
     */
    protected function mergeLinkAttributes($aLinkAttributes, $bAddAntiSpamClass)
    {
        $sConcatenatedLinkAttributes = '';
        $bClassAttributePresent = false;
        foreach ($aLinkAttributes as $sLinkAttribute) {
            if (!strstr($sLinkAttribute, 'href')) {
                if (false !== strstr($sLinkAttribute, 'class') && $bAddAntiSpamClass) {
                    $bClassAttributePresent = true;
                    $sConcatenatedLinkAttributes .= substr($sLinkAttribute, 0, -1).' antispam"';
                } else {
                    $sConcatenatedLinkAttributes .= $sLinkAttribute;
                }
            }
        }

        if (!$bClassAttributePresent) {
            $sConcatenatedLinkAttributes .= ' class="antispam"';
        }

        return $sConcatenatedLinkAttributes;
    }

    public function GetReplacementStrings()
    {
        $aReplace = array();
        switch (CHAMELEON_EMAIL_PRINT_SECURITY_LEVEL) {
            case 0:
                $aReplace['sAtSymbol'] = '';
                $aReplace['sDotSymbol'] = '';
                break;
            case 1:
                if (isset($this->aDefaultReplacementStrings['sAtSymbol']) && !empty($this->aDefaultReplacementStrings['sAtSymbol'])) {
                    $aReplace['sAtSymbol'] = $this->aDefaultReplacementStrings['sAtSymbol'];
                }
                if (isset($this->aDefaultReplacementStrings['sDotSymbol']) && !empty($this->aDefaultReplacementStrings['sDotSymbol'])) {
                    $aReplace['sDotSymbol'] = $this->aDefaultReplacementStrings['sDotSymbol'];
                }
                break;
            case 2:
                if (count(self::$aGeneratedReplacementStrings) < 2) {
                    $sAtSymbol = TTools::GetUUID();
                    $sAtSymbol = str_replace('-', '', $sAtSymbol);
                    self::$aGeneratedReplacementStrings['sAtSymbol'] = $sAtSymbol;
                    $aReplace['sAtSymbol'] = $sAtSymbol;
                    $sDotSymbol = TTools::GetUUID();
                    $sDotSymbol = str_replace('-', '', $sDotSymbol);
                    $aReplace['sDotSymbol'] = $sDotSymbol;
                    self::$aGeneratedReplacementStrings['sDotSymbol'] = $sDotSymbol;
                } else {
                    if (isset(self::$aGeneratedReplacementStrings['sAtSymbol']) && !empty(self::$aGeneratedReplacementStrings['sAtSymbol']) && isset(self::$aGeneratedReplacementStrings['sDotSymbol']) && !empty(self::$aGeneratedReplacementStrings['sDotSymbol'])) {
                        $aReplace['sAtSymbol'] = self::$aGeneratedReplacementStrings['sAtSymbol'];
                        $aReplace['sDotSymbol'] = self::$aGeneratedReplacementStrings['sDotSymbol'];
                    }
                }

                break;
            default:
                $aReplace['sAtSymbol'] = '';
                $aReplace['sDotSymbol'] = '';
                echo 'Warning, unknown email security level!';
        }

        return $aReplace;
    }

    /**
     * replaces dots and @ in email and address.
     *
     * @param string $sEmail
     *
     * @return string
     */
    public function EncodeEmail($sEmail)
    {
        $aReplacement = $this->GetReplacementStrings();
        $sDotSymbol = $aReplacement['sDotSymbol'];
        $sAtSymbol = $aReplacement['sAtSymbol'];
        if (!empty($sDotSymbol) && !empty($sAtSymbol)) {
            $translator = $this->getTranslator();
            $sEmail = str_replace(array('.', '@'), array('['.TGlobal::OutHTML($translator->trans($sDotSymbol, array(), TranslationConstants::DOMAIN_FRONTEND)).']', '['.TGlobal::OutHTML($translator->trans($sAtSymbol, array(), TranslationConstants::DOMAIN_FRONTEND)).']'), $sEmail);
        }

        return $sEmail;
    }

    public function PrintJSCode()
    {
        $jsCode = '';
        $sAtSymbol = '';
        $sDotSymbol = '';
        $aReplaceStrings = $this->GetReplacementStrings();
        if (isset($aReplaceStrings['sAtSymbol']) && !empty($aReplaceStrings['sAtSymbol'])) {
            $sAtSymbol = $aReplaceStrings['sAtSymbol'];
        }
        if (isset($aReplaceStrings['sDotSymbol']) && !empty($aReplaceStrings['sDotSymbol'])) {
            $sDotSymbol = $aReplaceStrings['sDotSymbol'];
        }
        if (!empty($sAtSymbol) && !empty($sDotSymbol)) {
            $translate = $this->getTranslator();
            $sAtSymbol = TGlobal::OutHTML($translate->trans($sAtSymbol, array(), TranslationConstants::DOMAIN_FRONTEND));
            $sDotSymbol = TGlobal::OutHTML($translate->trans($sDotSymbol, array(), TranslationConstants::DOMAIN_FRONTEND));

            $jsCode = "
        <script type=\"text/javascript\">
        //<![CDATA[
        var atSymbol = '".$sAtSymbol."';
        var dotSymbol = '".$sDotSymbol."';
        $(document).ready(function(){
          $(\".antispam\").ready(function(){
            DoAntiSpam('".$sAtSymbol."','".$sDotSymbol."');
          });
        });
        //]]>
        </script>
        ";
        }

        return $jsCode;
    }

    public function SetDefaultReplacementStrings($aReplacementStrings)
    {
        $this->aDefaultReplacementStrings = $aReplacementStrings;
    }

    public function GetDefaultReplacementStrings()
    {
        return $this->aDefaultReplacementStrings;
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
