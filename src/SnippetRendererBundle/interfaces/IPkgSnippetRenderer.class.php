<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IPkgSnippetRenderer
{
    public const SOURCE_TYPE_STRING = 0;
    public const SOURCE_TYPE_FILE = 1;
    public const SOURCE_TYPE_CMSMODULE = 2;

    /**
     * Returns a new instance. The instance uses the given string as snippet source.
     * It is possible to optionally provide it with a path to a file containing the snippet code.
     *
     * @static
     *
     * @param string $sSource - the snippet source (or path to a file containing it)
     * @param int $iSourceType - set to one of self::SOURCE_TYPE_*
     *
     * @psalm-param self::SOURCE_TYPE_* $iSourceType
     *
     * @return IPkgSnippetRenderer
     */
    public static function GetNewInstance($sSource, $iSourceType = self::SOURCE_TYPE_STRING);

    /**
     * Set the snippet source.
     * It is possible to override the initially given source this way afterwards.
     *
     * @param string $sSource - the snippet source
     *
     * @return void
     */
    public function setSource($sSource);

    /**
     * Set the path to the snippet code.
     * It is possible to override the initially given source this way afterwards.
     *
     * @param string $sPath - the path to the snippet code
     *
     * @return void
     */
    public function setFilename($sPath);

    /**
     * Set a variable/block to be substituted in the snippet.
     *
     * @param string $sName - variable/block name
     * @param mixed $sValue - the string to use in place of the variable/block
     *
     * @return void
     */
    public function setVar($sName, $sValue);

    /**
     * Set a variable/block content using a buffer.
     * e.g.:
     * <code>.
     *
     *   $oSnippetRenderer->setCapturedVarStart("foo");
     *   echo "bar";
     *   $oSnippetRenderer->setCapturedVarStop();
     *
     * </code>
     * After this, the variable "foo" will be set to "bar".
     *
     * The method will throw a <code>badMethodCallException</code> if it is called while another
     * similar call is already active,
     *
     * @param string $sName - the variable/block name
     *
     * @return void
     *
     * @throws BadMethodCallException
     */
    public function setCapturedVarStart($sName);

    /**
     * Stops the active captured variable call and writes the buffer to the variable.
     *
     * The method will throw a <code>badMethodCallException</code> if it is called while no capturing session is active.
     *
     * @return void
     *
     * @throws BadMethodCallException
     */
    public function setCapturedVarStop();

    /**
     * Renders the snippet and returns the rendered content.
     *
     * @return string - the rendered content
     *
     * @throws TPkgSnippetRenderer_SnippetRenderingException|Exception
     */
    public function render();

    /**
     * @param string|TModelBase $sSource
     * @param int $iSourceType
     *
     * @psalm-param IPkgSnippetRenderer::SOURCE_TYPE_* $iSourceType
     *
     * @return void
     */
    public function InitializeSource($sSource, $iSourceType = self::SOURCE_TYPE_STRING);

    /**
     * clears the objects state.
     *
     * @return void
     */
    public function clear();
}
