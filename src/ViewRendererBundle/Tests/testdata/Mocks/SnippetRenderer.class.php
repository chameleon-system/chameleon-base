<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SnippetRenderer implements IPkgSnippetRenderer
{
    private $aVars = [];
    private $sViewName = '';

    public function __construct($viewName)
    {
        $this->sViewName = $viewName;
    }

    public function SetVar($key, $value)
    {
        $this->aVars[$key] = $value;
    }

    public function Render()
    {
        return [$this->sViewName, $this->aVars];
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

    /**
     * Set the snippet source.
     * It is possible to override the initially given source this way afterwards.
     *
     * @param string $sSource - the snippet source
     */
    public function setSource($sSource)
    {
        // TODO: Implement setSource() method.
    }

    /**
     * Set the path to the snippet code.
     * It is possible to override the initially given source this way afterwards.
     *
     * @param string $sPath - the path to the snippet code
     */
    public function setFilename($sPath)
    {
        // TODO: Implement setFilename() method.
    }

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
     * @throws BadMethodCallException
     */
    public function setCapturedVarStart($sName)
    {
        // TODO: Implement setCapturedVarStart() method.
    }

    /**
     * Stops the active captured variable call and writes the buffer to the variable.
     *
     * The method will throw a <code>badMethodCallException</code> if it is called while no capturing session is active.
     *
     * @throws BadMethodCallException
     */
    public function setCapturedVarStop()
    {
        // TODO: Implement setCapturedVarStop() method.
    }

    public function InitializeSource($sSource, $iSourceType = IPkgSnippetRenderer::SOURCE_TYPE_STRING)
    {
        // TODO: Implement InitializeSource() method.
    }

    /**
     * clears the objects state.
     */
    public function clear()
    {
        // TODO: Implement clear() method.
    }
}
