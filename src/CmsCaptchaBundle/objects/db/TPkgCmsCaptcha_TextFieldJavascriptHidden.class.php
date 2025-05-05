<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

class TPkgCmsCaptcha_TextFieldJavascriptHidden extends TdbPkgCmsCaptcha
{
    /**
     * return true if the code in session was submitted in user data AND is empty
     * note: the code will be removed from session.
     *
     * @param string $sIdentifier
     * @param string $sCode will be ignored by this type of captcha and shall be passed as empty string
     *
     * @return bool
     */
    public function CodeIsValid($sIdentifier, $sCode)
    {
        $sCodeFromSession = TdbPkgCmsCaptcha::GetCodeFromSession($sIdentifier);
        if (false === $sCodeFromSession) {
            return false;
        }
        $request = ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
        $bValid = (null !== $request->get($sCodeFromSession, null));
        $sValue = $this->getInputFilterUtil()->getFilteredInput($sCodeFromSession, '');
        $bValid = ($bValid && '' === $sValue);

        return $bValid;
    }

    /**
     * generates a code for an identifier only once within one session call.
     *
     * @param string $sIdentifier
     * @param int $iCharacters will be ignored by this type of captcha
     *
     * @return string
     */
    protected function GenerateCode($sIdentifier, $iCharacters)
    {
        /** @var array<string, string> $aCodeCache */
        static $aCodeCache = []; // generate a code for an identifier only once within one session call

        if (!array_key_exists($sIdentifier, $aCodeCache)) {
            $sCode = TTools::GenerateNicePassword();
            $aCodeCache[$sIdentifier] = $sCode;
            TdbPkgCmsCaptcha::SaveInSession($sIdentifier, $sCode);
        }

        return $aCodeCache[$sIdentifier];
    }

    /**
     * return input field type text by default
     * adds javascript to hide the text field.
     *
     * @param string $sIdentifier used as name and id for the input field
     *
     * @return string
     */
    public function getHTMLSnippet($sIdentifier)
    {
        $sIdentifier = $this->GenerateCode($sIdentifier, 10);
        $sHTML = '
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function(event) {
                var captchaFields = document.querySelectorAll(\'input[name="'.$sIdentifier.'"]\');
                   Array.prototype.forEach.call(captchaFields, function(captchaField) {
                        captchaField.style.display = "none";
                   });
                });
            </script>
        ';
        $sHTML .= parent::getHTMLSnippet($sIdentifier);

        return $sHTML;
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
