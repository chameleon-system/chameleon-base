<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSMasterPagedef extends TCMSMasterPagedefAutoParent
{
    /**
     * name of the Template (shown to the user).
     *
     * @var string
     */
    public $templateName;

    /**
     * a short text describing the layout to the user.
     *
     * @var string
     */
    public $templateDescription;

    /**
     * name of the layout to use (comes from the master pagedef).
     * example: layout (not layout.layout.php).
     *
     * @var string
     */
    public $layoutTemplate;

    /**
     * holds all spots that belong to the pagedef.
     *
     * @var TCMSMasterPagedefSpot[]
     */
    protected $aSpots;
    /**
     * the layout for that pagedef.
     *
     * @var string
     */
    public $sLayout = '';

    /**
     * @var null string
     */
    protected $sCMSTplPageId = '';

    /**
     * {@inheritdoc}
     */
    public function __construct($id = null, $iLanguageId = null)
    {
        $this->table = 'cms_master_pagedef';
        parent::__construct($id, $iLanguageId);
    }

    /**
     * set layout.
     */
    protected function PostLoadHook()
    {
        parent::PostLoadHook();
        $this->sLayout = $this->sqlData['layout'];
        $this->LoadMasterPageDefVars(); // load all pagevars (including master pagedata)
    }

    /**
     * load the pagedef data from the master pagedef.
     */
    public function LoadMasterPageDefVars()
    {
        $this->templateName = $this->sqlData['name'];
        $this->templateDescription = $this->sqlData['description'];
        $this->layoutTemplate = $this->sLayout;
    }

    /**
     * returns static and dynamic modules merged as one array.
     *
     * @return array
     */
    public function GetModuleList()
    {
        $moduleList = $this->GetSpots();

        // now convert list to use the old spotname=>array('param'=>'val',...) format
        $aModuleData = [];
        foreach ($moduleList as $sSpotName => $spot) {
            $aModuleData[$sSpotName] = $spot->GetParameters();
        }

        return $aModuleData;
    }

    /**
     * return list of spots.
     *
     * @return TCMSMasterPagedefSpot[]
     */
    public function GetSpots()
    {
        if (null !== $this->aSpots) {
            return $this->aSpots;
        }

        $aSpots = [];
        $oSpots = $this->GetProperties('cms_master_pagedef_spot', 'TCMSMasterPagedefSpot');
        /**
         * @var TCMSMasterPagedefSpot $oSpot
         */
        while ($oSpot = $oSpots->Next()) {
            $aSpots[$oSpot->sName] = $oSpot;
        }

        $aSpots = $this->AddAdditionalSpots($aSpots);

        // some methods access the content of $this->aSpots directly and modify it... to prevent a second call to a new pagedef from returning the modified content, we clone the cache
        // data and return it instead
        $this->aSpots = [];
        reset($aSpots);
        foreach ($aSpots as $sSpotIndex => $spot) {
            $this->aSpots[$sSpotIndex] = clone $spot;
        }

        return $this->aSpots;
    }

    /**
     * Add spots that should be accessible via module loader in layout.
     *
     * @param array $aSpots
     *
     * @return array
     */
    protected function AddAdditionalSpots($aSpots)
    {
        return $aSpots;
    }

    /**
     * return an assoc array (spotname=>oSpot) of all dynamic spots.
     *
     * @return array
     */
    public function GetDynamicSpots()
    {
        $aDynamicSpots = [];
        $this->GetSpots();
        reset($this->aSpots);
        foreach ($this->aSpots as $sSpotName => $spot) {
            if (false === $spot->bIsStatic) {
                $aDynamicSpots[$sSpotName] = $spot;
            }
        }
        reset($this->aSpots);

        return $aDynamicSpots;
    }

    /**
     * return an assoc array (spotname=>oSpot) of all static spots.
     *
     * @return array
     */
    public function GetStaticSpots()
    {
        $aStaticSpots = [];
        $this->GetSpots();
        reset($this->aSpots);
        foreach ($this->aSpots as $sSpotName => $spot) {
            if (true === $spot->bIsStatic) {
                $aStaticSpots[$sSpotName] = $spot;
            }
        }
        reset($this->aSpots);

        return $aStaticSpots;
    }

    /**
     * return how many modules in the pagedef are dynamic.
     *
     * @return int
     */
    public function NumberOfDynamicModules()
    {
        $iNumberOfDynModules = $this->GetFromInternalCache('_numberOfDynmaicSpots');
        if (null !== $iNumberOfDynModules) {
            return (int) $iNumberOfDynModules;
        }
        $iNumberOfDynModules = 0;
        $this->GetSpots();
        reset($this->aSpots);
        foreach ($this->aSpots as $sSpotName => $spot) {
            if (false === $spot->bIsStatic) {
                ++$iNumberOfDynModules;
            }
        }
        $this->SetInternalCache('_numberOfDynmaicSpots', $iNumberOfDynModules);

        return $iNumberOfDynModules;
    }

    /**
     * return a specific spot.
     *
     * @param string $sSpotName
     *
     * @return TCMSMasterPagedefSpot
     */
    public function GetSpot($sSpotName)
    {
        $this->GetSpots();
        $oSpot = null;
        if (array_key_exists($sSpotName, $this->aSpots)) {
            $oSpot = $this->aSpots[$sSpotName];
        }

        return $oSpot;
    }

    /**
     * returns the layout filename without extension
     * example: "mylayout" (not "mylayout.layout.php").
     *
     * @return string
     */
    public function GetLayoutFile()
    {
        return $this->layoutTemplate;
    }

    /**
     * @param string $sCMSTplPageId
     */
    public function SetPageId($sCMSTplPageId)
    {
        $this->sCMSTplPageId = $sCMSTplPageId;
    }
}
