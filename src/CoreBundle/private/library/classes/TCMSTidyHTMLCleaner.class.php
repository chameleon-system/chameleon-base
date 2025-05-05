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
 * manages a wysiwyxg textfield.
 * /**/
class TCMSTidyHTMLCleaner
{
    protected $sTidyPath = '';

    /**
     * Sanitize text for HTML using the PHP tidy extension.
     *
     * @param string $sUncleanedText
     * @param array $aOverwiteOptions
     *
     * @return string
     *
     * @throws LogicException
     */
    public function CleanText($sUncleanedText, $aOverwiteOptions = [])
    {
        if (!extension_loaded('tidy')) {
            throw new LogicException('tidy PHP extension is not loaded but was called in TCMSTidyHTMLCleaner.');
        }

        return $this->TidyClean($sUncleanedText, $this->GetOptions($aOverwiteOptions));
    }

    /**
     * Get default options and overwirte with given options.
     *
     * @param array $aOverwiteOptions
     *
     * @return array
     */
    protected function GetOptions($aOverwiteOptions)
    {
        $aDefaulOptions = [
            'clean' => false,
            'drop-proprietary-attributes' => false,
            'drop-empty-paras' => false,
            'enclose-text' => true,
            'fix-backslash' => false,
            'force-output' => true,
            'hide-comments' => true,
            'indent' => true,
            'indent-spaces' => 2,
            'join-classes' => true,
            'join-styles' => true,
            'logical-emphasis' => true,
            'output-xhtml' => true,
            'merge-divs' => true,
            'show-body-only' => true,
            'word-2000' => true,
            'wrap' => 0,
            'tidy-mark' => false,
            'char-encoding' => 'utf8',
            'quote-marks' => false,
            'bare' => false,
            'doctype' => 'omit',
            'break-before-br' => true,
            'enclose-block-text' => true,
        ];
        if (count($aOverwiteOptions) > 0) {
            foreach ($aOverwiteOptions as $sOption => $sValue) {
                if (array_key_exists($sOption, $aDefaulOptions) && $aDefaulOptions[$sOption] != $aOverwiteOptions[$sOption]) {
                    $aDefaulOptions[$sOption] = $aOverwiteOptions[$sOption];
                }
            }
        }

        return $aDefaulOptions;
    }

    /**
     * Clean text to a html save text with php extension tidy.
     *
     * @param string $sUncleanedText
     * @param array $aOptions
     *
     * @return string
     */
    protected function TidyClean($sUncleanedText, $aOptions)
    {
        $oTidy = new tidy();
        $oTidy->parseString($sUncleanedText, $aOptions, 'utf8');
        $oTidy->cleanRepair();
        $sCleanedText = tidy_get_output($oTidy);
        $sCleanedText = $this->PostClean($sCleanedText);

        return $sCleanedText;
    }

    /**
     * Here you can add additional filter to clean text.
     *
     * @param string $sCleanedText
     *
     * @return string
     */
    protected function PostClean($sCleanedText)
    {
        $sPatter = '/<o:p><\/o:p>|<o:p>.+?<\/o:p>/ism';
        $sCleanedText = preg_replace($sPatter, '', $sCleanedText);

        return $sCleanedText;
    }
}
