<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

abstract class PkgAbstractSnippetRenderer implements IPkgSnippetRenderer
{
    private $aSubstitutes = array();
    private $bCapturing = false;
    private $sCapturingVar = null;
    /**
     * @var IResourceHandler
     */
    private $oResourceHandler;
    private $sSource = null;
    private $sFile = null;
    private $oSourceModule = null;
    private $sourceType = IPkgSnippetRenderer::SOURCE_TYPE_STRING;

    public function InitializeSource($sSource, $iSourceType = IPkgSnippetRenderer::SOURCE_TYPE_STRING)
    {
        $this->sourceType = $iSourceType;

        switch ($iSourceType) {
            case IPkgSnippetRenderer::SOURCE_TYPE_STRING:
                $this->setSource($sSource);
                break;
            case IPkgSnippetRenderer::SOURCE_TYPE_FILE:
                $this->setFilename($sSource);
                break;
            case IPkgSnippetRenderer::SOURCE_TYPE_CMSMODULE:
                $this->setSourceModule($sSource);
                break;
            default:
                throw new ErrorException('invalid source type ', 0, E_USER_ERROR);
                break;
        }
    }

    protected function getSourceType(): int
    {
        return $this->sourceType;
    }

    /**
     * Set the snippet source.
     * It is possible to override the initially given source this way afterwards.
     *
     * @param $sSource - the snippet source
     */
    public function setSource($sSource)
    {
        $this->sSource = $sSource;
    }

    protected function getSource()
    {
        return $this->sSource;
    }

    /**
     * Set the path to the snippet code.
     * It is possible to override the initially given source this way afterwards.
     *
     * @param $sPath - the path to the snippet code
     */
    public function setFilename($sPath)
    {
        $this->sFile = $sPath;
    }

    protected function getFilename()
    {
        return $this->sFile;
    }

    public function setSourceModule($oModuleInstance)
    {
        $this->oSourceModule = $oModuleInstance;
    }

    public function getSourceModule()
    {
        return $this->oSourceModule;
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
     * @param $sName - the variable/block name
     *
     * @throws BadMethodCallException
     */
    public function setCapturedVarStart($sName)
    {
        if ($this->bCapturing) {
            throw new BadMethodCallException("You can't capture two vars at the same time");
        }
        $this->bCapturing = true;
        $this->sCapturingVar = $sName;
        ob_start();
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
        if (!$this->bCapturing) {
            throw new BadMethodCallException('You were not capturing anyting at the moment');
        }
        $sResult = ob_get_clean();
        $this->bCapturing = false;
        $this->aSubstitutes[$this->sCapturingVar] = $sResult;
        $this->sCapturingVar = null;
    }

    /**
     * Set a variable/block to be substituted in the snippet.
     *
     * @param $sName - variable/block name
     * @param $sValue - the string to use in place of the variable/block
     */
    public function setVar($sName, $sValue)
    {
        $this->aSubstitutes[$sName] = $sValue;
    }

    protected function getVars()
    {
        return $this->aSubstitutes;
    }

    /**
     * @param IResourceHandler $oResourceHandler
     */
    public function setResourceHandler(IResourceHandler $oResourceHandler)
    {
        $this->oResourceHandler = $oResourceHandler;
    }

    /**
     * clears the objects state.
     */
    public function clear()
    {
        $this->aSubstitutes = array();
    }
}
