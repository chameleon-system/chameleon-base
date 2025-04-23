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
    /**
     * @var array<string, string>
     */
    private $aSubstitutes = [];

    /**
     * @var bool
     */
    private $bCapturing = false;

    /**
     * @var string|null
     */
    private $sCapturingVar;

    /**
     * @var IResourceHandler
     */
    private $oResourceHandler;

    /**
     * @var string|null
     */
    private $sSource;

    /**
     * @var string|null
     */
    private $sFile;

    /**
     * @var TModelBase|null
     */
    private $oSourceModule;

    /**
     * @var int
     */
    private $sourceType = IPkgSnippetRenderer::SOURCE_TYPE_STRING;

    /**
     * @param string|TModelBase $sSource
     * @param int $iSourceType
     *
     * @psalm-param IPkgSnippetRenderer::SOURCE_TYPE_* $iSourceType
     *
     * @return void
     */
    public function InitializeSource($sSource, $iSourceType = IPkgSnippetRenderer::SOURCE_TYPE_STRING)
    {
        $this->sourceType = $iSourceType;

        switch ($iSourceType) {
            case IPkgSnippetRenderer::SOURCE_TYPE_STRING:
                /* @var string $sSource */
                $this->setSource($sSource);
                break;
            case IPkgSnippetRenderer::SOURCE_TYPE_FILE:
                /* @var string $sSource */
                $this->setFilename($sSource);
                break;
            case IPkgSnippetRenderer::SOURCE_TYPE_CMSMODULE:
                /* @var TModelBase $sSource */
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
     * @param string $sSource - the snippet source
     *
     * @return void
     */
    public function setSource($sSource)
    {
        $this->sSource = $sSource;
    }

    /**
     * @return string|null
     */
    protected function getSource()
    {
        return $this->sSource;
    }

    /**
     * Set the path to the snippet code.
     * It is possible to override the initially given source this way afterwards.
     *
     * @param string $sPath - the path to the snippet code
     *
     * @return void
     */
    public function setFilename($sPath)
    {
        $this->sFile = $sPath;
    }

    /**
     * @return string|null
     */
    protected function getFilename()
    {
        return $this->sFile;
    }

    /**
     * @param TModelBase $oModuleInstance
     *
     * @return void
     */
    public function setSourceModule($oModuleInstance)
    {
        $this->oSourceModule = $oModuleInstance;
    }

    /**
     * @return TModelBase|null
     */
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
     * @param string $sName - the variable/block name
     *
     * @return void
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
     * @return void
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
        /*
         * @psalm-suppress InvalidPropertyAssignmentValue
         */
        $this->aSubstitutes[$this->sCapturingVar] = $sResult;
        $this->sCapturingVar = null;
    }

    /**
     * Set a variable/block to be substituted in the snippet.
     *
     * @param string $sValue - the string to use in place of the variable/block
     * @param string $sName - variable/block name
     *
     * @return void
     */
    public function setVar($sName, $sValue)
    {
        $this->aSubstitutes[$sName] = $sValue;
    }

    /**
     * @return string[]
     *
     * @psalm-return array<string, string>
     */
    protected function getVars()
    {
        return $this->aSubstitutes;
    }

    /**
     * @return void
     */
    public function setResourceHandler(IResourceHandler $oResourceHandler)
    {
        $this->oResourceHandler = $oResourceHandler;
    }

    /**
     * clears the objects state.
     *
     * @return void
     */
    public function clear()
    {
        $this->aSubstitutes = [];
    }
}
