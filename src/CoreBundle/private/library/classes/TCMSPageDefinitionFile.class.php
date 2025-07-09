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
 * used in the backend
 */
class TCMSPageDefinitionFile
{
    /**
     * name of the Template (shown to the user).
     */
    public string $templateName;

    /**
     * a short text describing the layout to the user.
     */
    public string $templateDescription;
    /**
     * the language code of the layout (iso6391 - see table cms_language).
     */
    public string $templateLanguage;

    /**
     * if the page makes use of a master pagedef file, then this var holds the master pagedef name
     * with no extension. example: layout (not layout.pagedef.php).
     */
    public string $sMasterPageDefinitionName = '';

    /**
     * name of the layout to use (if a masterpagedef is given, then the layout from the master
     * is used).
     * example: layout (not layout.layout.php).
     */
    public string $layoutTemplate = '';

    /**
     * array of the modules. form:
     * 'spotname'=>array(''=>'',...),'spot2'=>array(''=>'',...),...
     */
    public array $moduleList = [];

    /**
     * list of the static modules (when a master pagedef is present, then these will always be selected
     * from the master)
     * 'spotname'=>array(''=>'',...),'spot2'=>array(''=>'',...),...
     */
    public array $staticModuleList = [];

    /**
     * name of the pagedef (without the .pagedef.php).
     */
    protected string $sPageDef;
    /**
     * name of the master pagedef if present.
     */
    protected string $sMasterPageDef;

    /**
     * the path to the pagedef. if not set
     * it will point to the customer pagedefs.
     *
     * @var string
     */
    protected $sPageDefPath = PATH_CUSTOMER_PAGEDEFINITIONS;

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
     * @param string $spotName - name of the spot in the module list
     * @param string $model - the class to be used as the model
     * @param string $view - name of the view to use
     * @param int $instanceID - module instance id (optional)
     */
    public function UpdateModule($spotName, $model, $view, $instanceID = null)
    {
        if (!array_key_exists($spotName, $this->moduleList)) {
            $this->moduleList[$spotName] = [
                'model' => 'MTEmpty',
                'view' => 'standard',
                'instanceID' => null,
            ];
        }
        $this->moduleList[$spotName]['model'] = $model;
        $this->moduleList[$spotName]['view'] = $view;
        $this->moduleList[$spotName]['instanceID'] = $instanceID;
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
