<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    public $sNavigationIconId = null;
    /**
     * @var int
     */
    public $iLevel = 1;
    /**
     * @var string
     */
    public $sCssClass = '';
    /**
     * @var array|null
     */
    protected $aChildren = null;
    /**
     * @var bool|null
     */
    protected $bIsActive = null;
    /**
     * @var bool|null
     */
    protected $bIsExpanded = null;
    /**
     * @var TdbCmsTree|null
     */
    private $oNodeCopy = null;
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
     * @param TdbCmsTree $oNode
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
     * @return TdbCmsTree
     */
    protected function getNodeCopy()
    {
        return $this->oNodeCopy;
    }

    /**
     * @param TdbCmsTree $oNodeCopy
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
