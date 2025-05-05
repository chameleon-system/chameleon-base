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
 * @template T
 */
abstract class AbstractPkgCmsNavigationNode
{
    /**
     * @var string
     */
    public $sLink = '';
    /**
     * @var string
     */
    public $sTitle = '';
    /**
     * @var string|bool
     */
    public $sSeoTitle = false;
    /**
     * @var string|bool
     */
    public $sRel = false;
    /**
     * @var string|bool
     */
    public $sAccessKey = false;
    /**
     * @var string|bool
     */
    public $sTarget = false;
    /**
     * @var string|bool
     */
    public $sNavigationIconClass = false;
    /**
     * @var string|bool
     */
    public $sNavigationIconURL = false;
    /**
     * @var string|null
     */
    public $sNavigationIconId;
    /**
     * @var int
     */
    public $iLevel = 1;
    /**
     * @var string
     */
    public $sCssClass = '';
    /**
     * @var AbstractPkgCmsNavigationNode[]|null
     */
    protected $aChildren;
    /**
     * @var bool|null
     */
    protected $bIsActive;
    /**
     * @var bool|null
     */
    protected $bIsExpanded;
    /**
     * @var T|null
     */
    private $oNodeCopy;
    /**
     * @var bool
     */
    protected $bDisableSubmenu = false;

    /**
     * @param string $sId
     *
     * @return bool
     */
    abstract public function load($sId);

    /**
     * @param T $oNode
     *
     * @return bool
     */
    abstract public function loadFromNode($oNode);

    /**
     * returns the url to an icon for the node - if set.
     *
     * @return string|null
     */
    abstract public function getNodeIconURL();

    /**
     * @return array|null
     */
    abstract public function getAChildren();

    /**
     * @return bool
     */
    abstract public function getBIsActive();

    /**
     * @return bool
     */
    abstract public function getBIsExpanded();

    /**
     * @param bool $bDisableSubmenu
     *
     * @return void
     */
    public function setDisableSubmenu($bDisableSubmenu)
    {
        $this->bDisableSubmenu = $bDisableSubmenu;
    }

    /**
     * @param array $aChildren
     *
     * @return void
     */
    public function setChildren($aChildren)
    {
        $this->aChildren = $aChildren;
    }

    /**
     * @param bool $bIsActive
     *
     * @return void
     */
    public function setIsActive($bIsActive)
    {
        $this->bIsActive = $bIsActive;
    }

    /**
     * @return T|null
     */
    protected function getNodeCopy()
    {
        return $this->oNodeCopy;
    }

    /**
     * @param T $oNodeCopy
     *
     * @return void
     */
    protected function setNodeCopy($oNodeCopy)
    {
        $this->oNodeCopy = $oNodeCopy;
    }

    /**
     * rendering of dummy images in navigation.
     *
     * @return bool
     */
    protected function dummyImagesAllowed()
    {
        return 2 == $this->iLevel;
    }
}
