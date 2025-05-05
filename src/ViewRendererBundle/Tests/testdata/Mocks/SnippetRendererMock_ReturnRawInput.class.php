<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SnippetRendererMock_ReturnRawInput extends PkgAbstractSnippetRenderer
{
    private $aVars = [];
    private $sTestContent = '';

    public function __construct($sTestContent)
    {
        $this->sTestContent = $sTestContent;
    }

    public function SetVar($key, $value)
    {
        $this->aVars[$key] = $value;
    }

    public function Render()
    {
        return $this->sTestContent;
    }

    /**
     * Returns a new instance. The instance uses the given string as snippet source.
     * It is possible to optionally provide it with a path to a file containing the snippet code.
     *
     * @static
     *
     * @param string $sSource - the snippet source (or path to a file containing it)
     * @param int $iSourceType - set to one of self::SOURCE_TYPE_*
     *
     * @return TPkgSnippetRenderer
     */
    public static function GetNewInstance($sSource, $iSourceType = IPkgSnippetRenderer::SOURCE_TYPE_STRING, ?IResourceHandler $oResourceHandler = null)
    {
        // TODO: Implement GetNewInstance() method.
    }
}
