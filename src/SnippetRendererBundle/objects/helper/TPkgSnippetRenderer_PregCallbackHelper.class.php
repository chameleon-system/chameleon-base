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
 * used to abstract a preg replace callback (they need to be public and we don't want them being part of our
 * public API in TPkgSnippetRenderer...
/**/
class TPkgSnippetRenderer_PregCallbackHelper
{
    /**
     * @var null|string
     */
    public $aResult = null;

    /**
     * @param array $aMatches
     * @return string
     */
    public function PregReplaceCallback($aMatches)
    {
        $this->aResult[] = $aMatches[1];

        return '';
    }
}
