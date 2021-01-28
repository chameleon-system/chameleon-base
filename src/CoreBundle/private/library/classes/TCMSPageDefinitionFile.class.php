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
 * read and write page definition files.
 *
 * @deprecated - but used in the backend in the moment
/**/
class TCMSPageDefinitionFile
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
     * the language code of the layout (iso6391 - see table cms_language).
     *
     * @var string
     */
    public $templateLanguage;

    /**
     * if the page makes use of a master pagedef file, then this var holds the master pagedef name
     * with no extension. example: layout (not layout.pagedef.php).
     *
     * @var string
     */
    public $sMasterPageDefinitionName;

    /**
     * name of the layout to use (if a masterpagedef is given, then the layout from the master
     * is used).
     * example: layout (not layout.layout.php).
     *
     * @var string
     */
    public $layoutTemplate;

    /**
     * array of the modules. form:
     * 'spotname'=>array(''=>'',...),'spot2'=>array(''=>'',...),...
     *
     * @var array
     */
    public $moduleList;

    /**
     * list of the static modules (when a master pagedef is present, then these will always be selected
     * from the master)
     * 'spotname'=>array(''=>'',...),'spot2'=>array(''=>'',...),...
     *
     * @var array
     */
    public $staticModuleList;

    /**
     * @var \TdbCmsRight[]
     */
    public $allowedRights = [];

    /**
     * name of the pagedef (without the .pagedef.php).
     *
     * @var string
     */
    protected $sPageDef;
    /**
     * name of the master pagedef if present.
     *
     * @var string
     */
    protected $sMasterPageDef;

    /**
     * the path to the pagedef. if not set
     * it will point to the customer pagedefs.
     *
     * @var string
     */
    protected $sPageDefPath = PATH_CUSTOMER_PAGEDEFINITIONS;

    /**
     * create a new CUSTOMER pagedef (save is also executed).
     *
     * @param string $masterPagedefName - name of the master
     * @param string $targetPagdef      - name of the pagedef that we want to write
     * @param array  $aModuleList       - the module list
     */
    public function Create($masterPagedefName, $targetPagdef, $aModuleList = null)
    {
        $this->LoadMasterPageDefVars($masterPagedefName);
        $this->sPageDef = $targetPagdef;
        if (is_array($aModuleList)) {
            if (!is_array($this->moduleList)) {
                $this->moduleList = array();
            }
            foreach ($aModuleList as $key => $value) {
                $this->moduleList[$key] = $value;
            }
        }
        $this->Save();
    }

    /**
     * Load the page def given by sPagedef. This function assumes that we are
     * dealing with a customer page!
     *
     * @param string $sPagedef
     * @param string|null sPageDefPath - optionally you can overwrite where the class should look for the pagedef
     *
     * @return bool
     */
    public function Load($sPagedef, $sPageDefPath = null)
    {
        if (null !== $sPageDefPath) {
            $this->sPageDefPath = $sPageDefPath;
        }
        $this->sPageDef = $sPagedef;

        return $this->LoadPageDefVars($sPagedef);
    }

    /**
     * updates a module within the pagedef. Make sure to call save after calling
     * this function if you want to commit the changes to the file.
     *
     * @param string $spotName   - name of the spot in the module list
     * @param string $model      - the class to be used as the model
     * @param string $view       - name of the view to use
     * @param int    $instanceID - module instance id (optional)
     */
    public function UpdateModule($spotName, $model, $view, $instanceID = null)
    {
        if (null === $this->moduleList) {
            $this->moduleList = array();
        }
        if (!array_key_exists($spotName, $this->moduleList)) {
            $this->moduleList[$spotName] = array(
                'model' => 'MTEmpty',
                'view' => 'standard',
                'instanceID' => null,
            );
        }
        $this->moduleList[$spotName]['model'] = $model;
        $this->moduleList[$spotName]['view'] = $view;
        $this->moduleList[$spotName]['instanceID'] = $instanceID;
    }

    /**
     * commit the current pagedef state to the pagedef file.
     *
     * @deprecated
     */
    public function Save()
    {
        $pageFile = $this->sPageDefPath.'/'.$this->sPageDef.'.pagedef.php';
        $_moduleListString = '';
        $_isFirst = true;
        foreach ($this->moduleList as $_spotname => $_config) {
            if ($_isFirst) {
                $_isFirst = false;
            } else {
                $_moduleListString .= ",\n";
            }
            $_moduleListString .= "    '{$_spotname}' => ";
            // write parameters...
            $sModuleConfigData = TTools::ArrayToString($_config);

            $_moduleListString .= $sModuleConfigData;
        }
        $_newPageDef = "<?php\n"."  \$sMasterPageDefinitionName = '{$this->sMasterPageDefinitionName}';\n"."\n"."  // modules...\n"."  \$moduleList = array(\n".$_moduleListString."\n"."  );\n"."\n"."  // this line needs to be included... do not touch\n"."  if (!is_array(\$moduleList)) \$layoutTemplate = '';\n";
        $fp = fopen($pageFile, 'wb');
        fwrite($fp, $_newPageDef, mb_strlen($_newPageDef));
        fclose($fp);
    }

    /**
     * returns static and dynamic modules merged as one array.
     *
     * @return array
     */
    public function GetModuleList()
    {
        $moduleList = $this->moduleList;
        if (is_array($this->staticModuleList)) {
            $moduleList = array_merge($moduleList, $this->staticModuleList);
        }

        return $moduleList;
    }

    /**
     * Load the pagedef variables from the page for the page.
     *
     * @param string $sPagedef
     *
     * @return bool
     */
    protected function LoadPageDefVars($sPagedef)
    {
        $pageLoaded = false;
        $file = $this->sPageDefPath.'/'.$sPagedef.'.pagedef.php';
        if (file_exists($file)) {
            include_once __DIR__.'/pagedefFunctions.inc.php';
            include $file;
            if (isset($templateName)) {
                $this->templateName = $templateName;
            }
            if (isset($templateDescription)) {
                $this->templateDescription = $templateDescription;
            }
            if (isset($templateLanguage)) {
                $this->templateLanguage = $templateLanguage;
            }
            if (isset($sMasterPageDefinitionName)) {
                $this->sMasterPageDefinitionName = $sMasterPageDefinitionName;
            }
            if (isset($layoutTemplate)) {
                $this->layoutTemplate = $layoutTemplate;
            }
            if (isset($moduleList)) {
                $this->moduleList = $moduleList;
            }
            if (isset($staticModuleList)) {
                $this->staticModuleList = $staticModuleList;
            }
            if (isset($cmsRightAllowList) && true === \is_array($cmsRightAllowList) && \count($cmsRightAllowList) > 0) {
                foreach ($cmsRightAllowList as $rightName) {
                    $right = TdbCmsRight::GetNewInstance();
                    if (true === $right->LoadFromField('name', $rightName)) {
                        $this->allowedRights[] = $right;
                    }
                }
            }

            if (!is_null($this->sMasterPageDefinitionName)) {
                $this->sMasterPageDefinitionName = str_replace('.pagedef.php', '', $this->sMasterPageDefinitionName);
                $this->LoadMasterPageDefVars($this->sMasterPageDefinitionName);
            }
            $pageLoaded = true;
        }

        return $pageLoaded;
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
     * load the pagedef data from the master pagedef.
     *
     * @param string $sPagedef - name of the masterpagedef
     */
    public function LoadMasterPageDefVars($sPagedef)
    {
        $file = PATH_CUSTOMER_PAGEMASTERDEFINITIONS.'/'.$sPagedef.'.pagedef.php';
        if (file_exists($file)) {
            require TGlobal::ProtectedPath($file, '.pagedef.php');
            if (isset($templateName)) {
                $this->templateName = $templateName;
            }
            if (isset($templateDescription)) {
                $this->templateDescription = $templateDescription;
            }
            if (isset($templateLanguage)) {
                $this->templateLanguage = $templateLanguage;
            }
            $this->sMasterPageDefinitionName = $sPagedef;
            if (isset($layoutTemplate)) {
                $this->layoutTemplate = $layoutTemplate;
            }
            if (isset($staticModuleList)) {
                $this->staticModuleList = $staticModuleList;
            }

            // overwrite moduleList ONLY if not set
            if (isset($moduleList) && is_null($this->moduleList)) {
                $this->moduleList = $moduleList;
            }
        }
    }
}
