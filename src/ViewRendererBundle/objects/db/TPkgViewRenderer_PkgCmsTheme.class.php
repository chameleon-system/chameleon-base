<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgViewRenderer_PkgCmsTheme extends TPkgViewRenderer_PkgCmsThemeAutoParent
{
    /**
     * get the theme's snippet chain as array.
     *
     * @return array
     */
    public function getSnippetChainAsArray()
    {
        $snippetChain = str_replace(["\r\n", "\n\r", "\r"], ["\n", "\n", "\n"], $this->fieldSnippetChain);

        return explode("\n", $snippetChain);
    }
}
