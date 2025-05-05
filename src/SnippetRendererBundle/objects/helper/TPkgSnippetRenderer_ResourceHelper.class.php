<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgSnippetRenderer_ResourceHelper
{
    /**
     * @param string $sSource
     * @param bool $bExcludeJS
     *
     * @return array<string, string[]>
     */
    public function getResourcesFromSource($sSource, $bExcludeJS = false)
    {
        $sSource = trim($sSource);
        $aResource = ['css' => [], 'less' => []];
        if (false === $bExcludeJS) {
            $aResource['js'] = [];
        }

        $aLines = explode("\n", $sSource);
        foreach ($aLines as $sLine) {
            $sLine = trim($sLine);
            if (empty($sLine)) {
                continue;
            }
            $ext = substr($sLine, strrpos($sLine, '.') + 1);
            if (isset($aResource[$ext])) {
                $aResource[$ext][] = $sLine;
            }
        }

        return $aResource;
    }
}
