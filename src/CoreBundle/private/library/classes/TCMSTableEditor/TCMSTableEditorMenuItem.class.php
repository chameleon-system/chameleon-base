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
 * holds one menu item for the editor page and possible sub menu items
 * items are rendered from TWIG templates.
/**/
class TCMSTableEditorMenuItem
{
    /**
     * items should have a unique name so they can be addressed within the item list.
     *
     * @var string
     */
    public $sItemKey = null;

    /**
     * the button title
     * it will be translated, so set the base language string.
     *
     * @deprecated use setTitle() instead
     *
     * @var string
     */
    public $sDisplayName = null;

    /**
     * the button title
     * set via setTitle().
     *
     * the title expects an already translated string
     *
     * @var string|null
     */
    protected $sTitle = null;

    /**
     * button icon (mandatory).
     *
     * @var string
     */
    public $sIcon = null;

    /**
     * onClick event (mandatory).
     *
     * @var string
     */
    public $sOnClick = null;

    /**
     * one or more additional CSS style classes
     * (if you want to set the bootstrap button style, please set this via setButtonType().
     *
     * @var string
     */
    public $sCSSClass = null;

    /**
     * bootstrap button style (default: btn-primary)
     * possible values in bootstrap 4.1: btn-primary, btn-secondary, btn-success, btn-info, btn-warning, btn-danger, btn-link.
     *
     * @see http://getbootstrap.com/css/#buttons
     *
     * @var string
     */
    protected $sButtonStyleClass = 'btn-secondary';

    /**
     * holds array of TCMSTableEditorMenuItem which will be rendered as submenu items.
     *
     * @var array
     */
    protected $aSubMenuItems = array();

    /**
     * expects an already translated string.
     *
     * @param $sTitle
     */
    public function setTitle($sTitle)
    {
        $this->sTitle = $sTitle;
    }

    /**
     * the button title.
     *
     * @return string
     */
    protected function getTitle()
    {
        $sTitle = 'no title';
        if (null != $this->sTitle) {
            $sTitle = $this->sTitle;
        } else {
            $sTitle = $this->sDisplayName;
        }

        return $sTitle;
    }

    /**
     * @return string
     */
    public function GetRightClickMenuItemHTML()
    {
        $sClass = '';
        if (count($this->aSubMenuItems) > 0) {
            $sClass = ' class="haschildren"';
        }

        $html = '<li'.$sClass.'><a'.$sClass." href=\"javascript:$('#jqContextMenu').hide();void(0);\"";

        if (!is_null($this->sOnClick)) {
            $sOnClick = str_replace('return false;', '$(\'#jqContextMenu\').hide();return false;', $this->sOnClick);
            $html .= ' onclick="'.$sOnClick.'"';
        }

        if (!is_null($this->sCSSClass)) {
            $html .= ' class="'.$this->sCSSClass.'"';
        }

        $html .= '>';

        if ($this->isIconUrl($this->sIcon)) {
            $html .= '<img src="'.$this->sIcon.'" border="0" style="float: left; padding-right: 5px; padding-top: 3px;" alt="" />';
        } else {
            $html .= '<i class="'.$this->sIcon.' pr-2"></i>';
        }

        $html .= $this->getTitle().'</a>';

        if (count($this->aSubMenuItems) > 0) {
            $html .= '<ul>';

            /**
             * @var $oSubMenuItem TCMSTableEditorMenuItem
             */
            foreach ($this->aSubMenuItems as $oSubMenuItem) {
                $html .= $oSubMenuItem->GetRightClickMenuItemHTML();
            }
            $html .= '</ul>';
        }

        $html .= '</li>';

        return $html;
    }

    /**
     * generates a tab button (e.g. save, delete...).
     *
     * @return string
     */
    public function GetMenuItemHTML()
    {
        if (count($this->aSubMenuItems) > 0) {
            $html = $this->renderItemWithSubMenu();
        } else {
            $html = $this->renderSingleItem();
        }

        return $html;
    }

    /**
     * renders MTTableEditor/singleMenuButton.html.twig.
     *
     * @return string
     */
    protected function renderSingleItem()
    {
        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->AddSourceObject('sTitle', $this->getTitle());
        $oViewRenderer->AddSourceObject('sItemKey', $this->sItemKey);
        $oViewRenderer->AddSourceObject('sCSSClass', $this->sCSSClass);
        $oViewRenderer->AddSourceObject('sOnClick', $this->sOnClick);
        $oViewRenderer->AddSourceObject('sButtonStyle', $this->getButtonStyle());

        if ($this->isIconUrl($this->sIcon)) {
            $oViewRenderer->AddSourceObject('sIconURL', $this->sIcon);
        } else {
            $oViewRenderer->AddSourceObject('sIcon', $this->sIcon);
        }

        return $oViewRenderer->Render('MTTableEditor/singleMenuButton.html.twig', null, false);
    }

    /**
     * renders MTTableEditor/singleMenuButton.html.twig.
     *
     * @return string
     */
    protected function renderItemWithSubMenu()
    {
        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->AddSourceObject('sTitle', $this->getTitle());
        $oViewRenderer->AddSourceObject('sItemKey', $this->sItemKey);
        $oViewRenderer->AddSourceObject('sCSSClass', $this->sCSSClass);
        $oViewRenderer->AddSourceObject('sOnClick', $this->sOnClick);
        if ($this->isIconUrl($this->sIcon)) {
            $oViewRenderer->AddSourceObject('sIconURL', $this->sIcon);
        } else {
            $oViewRenderer->AddSourceObject('sIcon', $this->sIcon);
        }
        $oViewRenderer->AddSourceObject('sButtonStyle', $this->getButtonStyle());

        $aSubItems = array();

        /**
         * @var $oSubItem TCMSTableEditorMenuItem
         */
        foreach ($this->aSubMenuItems as $oSubItem) {
            $aSubItemData = array();
            $aSubItemData['sTitle'] = $oSubItem->getTitle();
            $aSubItemData['sItemKey'] = $oSubItem->sItemKey;
            $aSubItemData['sCSSClass'] = $oSubItem->sCSSClass;
            $aSubItemData['sOnClick'] = $oSubItem->sOnClick;
            if ($this->isIconUrl($oSubItem->sIcon)) {
                $aSubItemData['sIconURL'] = $oSubItem->sIcon;
            } else {
                $aSubItemData['sIcon'] = $oSubItem->sIcon;
            }
            $aSubItems[] = $aSubItemData;
        }
        $oViewRenderer->AddSourceObject('aSubItems', $aSubItems);

        return $oViewRenderer->Render('MTTableEditor/menuButtonWithDropdown.html.twig', null, false);
    }

    private function isIconUrl($icon): bool
    {
        return false !== \strpos($icon, '/') || false !== \strpos($icon, '.');
    }

    /**
     * bootstrap button style (default: btn-primary)
     * possible values in bootstrap 4.1: btn-primary, btn-secondary, btn-success, btn-info, btn-warning, btn-danger, btn-link.
     *
     * @see http://getbootstrap.com/css/#buttons
     *
     * @param $sButtonStyle
     */
    public function setButtonStyle($sButtonStyle)
    {
        $this->sButtonStyleClass = $sButtonStyle;
    }

    /**
     * bootstrap button style.
     *
     * @see http://getbootstrap.com/css/#buttons
     *
     * @return string
     */
    protected function getButtonStyle()
    {
        return $this->sButtonStyleClass;
    }

    /**
     * use this to add a TCMSTableEditorMenuItem as sub menu item
     * if no position is set it will be added to the last position.
     *
     * @param TCMSTableEditorMenuItem $oItem
     * @param int|null                $iPos  - position to place the item in the array (starting with index 0)
     */
    public function addSubMenuItem($oItem, $iPos = null)
    {
        if (is_int($iPos) && count($this->aSubMenuItems) <= $iPos) {
            $aNewSubMenuItems = array();
            foreach ($this->aSubMenuItems as $key => $oStoredItem) {
                if ($key == $iPos) {
                    $aNewSubMenuItems[] = $oItem;
                }
                $aNewSubMenuItems[] = $oStoredItem;
            }
            $this->aSubMenuItems = $aNewSubMenuItems;
        } else {
            $this->aSubMenuItems[] = $oItem;
        }
    }
}
