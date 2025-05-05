<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use Psr\Log\LoggerInterface;

class TCMSSpellcheck
{
    /**
     * language object to us.
     *
     * @var TdbCmsLanguage
     */
    protected $oLanguage;

    /**
     * return instance of spellchecker.
     *
     * @return TCMSSpellcheck
     */
    public static function GetInstance()
    {
        static $oInstance;
        if (!isset($oInstance)) {
            $oInstance = new self();
            $oInstance->Init(self::getLanguageService()->getActiveLanguageId());
        }

        return $oInstance;
    }

    /**
     * init the object for the given language.
     *
     * @param int $iLanguageId
     */
    public function Init($iLanguageId)
    {
        $this->oLanguage = self::getLanguageService()->getLanguage($iLanguageId);
    }

    /**
     * suggest a correction. return false if there are now corrections.
     *
     * @param string $sString
     * @param callable(string[]): string|null $sCorrectionSelectCallback - callback to improve suggestion (must take a word as input). you can also use: array($this,'somemethod')
     *
     * @return array{ string: string, corrections: array<string, string> } - format: [ 'string' => '', 'corrections' => [ 'word' => 'new word' ] ]
     */
    public function SuggestCorrection($sString, $sCorrectionSelectCallback = null)
    {
        $aResult = ['string' => '', 'corrections' => []];

        $sString = strip_tags($sString);
        $aErrors = $this->GetMisspelled($sString);
        if (count($aErrors) > 0) {
            $aResult['corrections'] = $this->Correct($aErrors, $sCorrectionSelectCallback);
            $aResult['string'] = str_replace(array_keys($aResult['corrections']), array_values($aResult['corrections']), $sString);
        } else {
            $aResult = false;
        }

        return $aResult;
    }

    /**
     * @param array $aWords
     * @param callable(string[]): string $sCorrectionSelectCallback - callback to improve suggestion (must take a word as input). you can also use: array($this,'somemethod')
     *
     * @return array
     */
    protected function Correct($aWords, $sCorrectionSelectCallback = '')
    {
        $aCorrections = [];
        $sList = implode(' ', $aWords);
        $aTmp = $this->RunASpell($sList, '-a');
        $iWordPointer = 0;
        foreach ($aTmp as $sLine) {
            if ('*' == substr($sLine, 0, 1)) {
                $aCorrections[$aWords[$iWordPointer]] = $aWords[$iWordPointer];
                ++$iWordPointer;
            } elseif ('&' == substr($sLine, 0, 1)) {
                $sClean = substr($sLine, strpos($sLine, ':') + 2);
                $aTmpStringParts = explode(', ', $sClean);
                $sBestSuggestion = $aTmpStringParts[0];
                if (!empty($sCorrectionSelectCallback)) {
                    if (is_array($sCorrectionSelectCallback)) {
                        $oObject = $sCorrectionSelectCallback[0];
                        $sMethod = $sCorrectionSelectCallback[1];
                        $sBestSuggestion = $oObject->$sMethod($aTmpStringParts);
                    } else {
                        $sBestSuggestion = $sCorrectionSelectCallback($aTmpStringParts);
                    }
                }

                $aCorrections[$aWords[$iWordPointer]] = $sBestSuggestion;
                ++$iWordPointer;
            }
        }

        return $aCorrections;
    }

    /**
     * return array of misspelled words.
     *
     * @param string $sString
     *
     * @return array
     */
    protected function GetMisspelled($sString)
    {
        return $this->RunASpell($sString, '--list');
    }

    protected function RunASpell($sInput, $sArgs)
    {
        $aStreams = [0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];
        $out = '';
        $err = '';

        $pipes = null;
        $process = proc_open('aspell -l '.TTools::GetActiveLanguageIsoName().' --ignore-case --encoding=utf-8 --sug-mode=bad-spellers '.escapeshellarg($sArgs), $aStreams, $pipes);
        if (is_resource($process)) {
            // write to stdin
            fwrite($pipes[0], $sInput);
            fclose($pipes[0]);

            // read stdout
            while (!feof($pipes[1])) {
                $out .= fread($pipes[1], 8192);
            }
            fclose($pipes[1]);

            // read stderr
            while (!feof($pipes[2])) {
                $err .= fread($pipes[2], 8192);
            }
            fclose($pipes[2]);

            proc_close($process);
        }

        if (!empty($err)) {
            $this->getLogger()->warning(sprintf('aspell returned an error: %s', $err));

            return [];
        }

        $aLines = explode("\n", $out);
        $aFinal = [];
        foreach ($aLines as $sLine) {
            $sLine = trim($sLine);
            if (!empty($sLine)) {
                $aFinal[] = $sLine;
            }
        }

        return $aFinal;
    }

    /**
     * @return LanguageServiceInterface
     */
    private static function getLanguageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }

    private function getLogger(): LoggerInterface
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('logger');
    }
}
