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
 * holds a record from the "cms_tpl_module" table.
/**/
class TCMSTPLModule extends TCMSRecord
{
    /**
     * an iterator of all the views for the module.
     *
     * @var TIterator
     */
    private $_oViews = null;

    /**
     * if set, it will be used to restricted the views returned by GetViews.
     *
     * @var array
     */
    public $aPermittedViews = null;

    public function __construct($id = null)
    {
        $table = 'cms_tpl_module';
        parent::__construct($table, $id);
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSTPLModule()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }


    public function isLegacy()
    {
        return 0 === $this->getViewMapperConfig()->getConfigCount();
    }

    public function GetViews()
    {
        if (true === $this->isLegacy()) {
            return $this->GetViewsLegacy();
        }

        if (null !== $this->_oViews) {
            return $this->_oViews;
        }

        $this->_oViews = new TIterator();

        $aViewMapperConfig = $this->getViewMapperConfig();
        $views = $aViewMapperConfig->getConfigs();
        foreach ($views as $sViewName) {
            $this->_oViews->AddItem($sViewName);
        }

        return $this->_oViews;
    }

    /**
     * returns an iterator holding all the views of the module.
     *
     * @return TIterator
     */
    public function &GetViewsLegacy()
    {
        if (null !== $this->_oViews) {
            return $this->_oViews;
        }

        $this->_oViews = new TIterator();

        if ('' === $this->sqlData['classname']) {
            return $this->_oViews;
        }

        // try to get from the view/mapper configuration first
        $viewMapperConfig = $this->getViewMapperConfig();
        $viewConfigurations = $viewMapperConfig->getConfigs();
        if (count($viewConfigurations) > 0) {
            foreach (array_keys($viewConfigurations) as $viewName) {
                $this->_oViews->AddItem($viewName);
            }

            return $this->_oViews;
        }

        // get default view location (view folder in the module directory - or theme if defined)
        $defaultViewLocation = PATH_CUSTOMER_FRAMEWORK_MODULES.'/'.$this->sqlData['classname'].'/views/';
        /** @var \ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface $portalDomainService */
        $portalDomainService = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
        $activePortal = $portalDomainService->getActivePortal();
        if (!is_null($activePortal)) {
            $sThemePath = $activePortal->GetThemeBaseModuleViewsPath();
            if (!empty($sThemePath)) {
                $defaultViewLocation = $sThemePath.'/'.$this->sqlData['classname'].'/';
            }
        }

        $modulePathList = array();

        if (true === is_dir($defaultViewLocation)) {
            $modulePathList[] = $defaultViewLocation;
        }

        if (!is_null($activePortal)) {
            $tmpList = $this->getViewRendererSnippetDirectory()->getBasePaths($activePortal, TPkgViewRendererSnippetDirectory::PATH_MODULES);
            if (count($tmpList) > 0) {
                foreach ($tmpList as $dir) {
                    $newDir = $dir.'/'.$this->sqlData['classname'];
                    if (true === is_dir($newDir)) {
                        $modulePathList[] = $newDir;
                    }
                }
            }
        }

        if (0 === count($modulePathList)) {
            return $this->_oViews;
        }

        $finder = new \Symfony\Component\Finder\Finder();
        $finder->files()->in($modulePathList)->depth('== 0')->name('*.view.php')->sortByName();
        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        $viewsFound = array();
        foreach ($finder as $file) {
            $viewName = $file->getBasename('.view.php');
            // include every view only once
            if (in_array($viewName, $viewsFound)) {
                continue;
            }
            $viewsFound[] = $viewName;
            $this->_oViews->AddItem($viewName);
        }

        return $this->_oViews;
    }

    /**
     * returns an array holding translations for views of the module.
     *
     * @return array
     */
    public function GetViewMapping()
    {
        $aViewMapping = array();
        $views = $this->GetViews();
        $views->GoToStart();
        while ($view = $views->next()) {
            $aViewMapping[$view] = $view;
        }
        $views->GoToStart();

        $aViewsToMap = explode("\n", $this->sqlData['view_mapping']);
        foreach ($aViewsToMap as $sViewToMap) {
            $aSplittedParts = explode('=', $sViewToMap);
            $sViewOriginalname = trim(array_shift($aSplittedParts));
            if ('' === $sViewOriginalname) {
                continue;
            }
            $sViewTranslatedName = trim(implode('=', $aSplittedParts));
            $aViewMapping[$sViewOriginalname] = $sViewTranslatedName;
        }
        \asort($aViewMapping, SORT_NATURAL | SORT_FLAG_CASE);

        return $aViewMapping;
    }

    /**
     * loads the module by looking for a cms_tbl_conf connection
     * needed if we want to get the module, instance or page for a data table.
     *
     * @param int $id
     *
     * @return bool
     */
    public function LoadByTblConfId($id)
    {
        $returnVal = false;
        $query = "SELECT * FROM `cms_tpl_module_cms_tbl_conf_mlt` WHERE `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($id)."'";
        $result = MySqlLegacySupport::getInstance()->query($query);
        if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
            $row = MySqlLegacySupport::getInstance()->fetch_assoc($result);
            $this->Load($row['source_id']);
            $returnVal = true;
        }

        return $returnVal;
    }

    /**
     * @return ViewMapperConfig
     */
    public function getViewMapperConfig()
    {
        $viewMapperConfig = $this->GetFromInternalCache('viewMapperConfig');
        if (null !== $viewMapperConfig) {
            return $viewMapperConfig;
        }

        try {
            $viewMapperConfig = $this->sqlData['view_mapper_config'];
        } catch (\Exception $e) {
            throw new \Exception(sprintf('module with ID: %s not found',$this->id));
        }
        $viewMapperConfig = new ViewMapperConfig($viewMapperConfig);
        $this->SetInternalCache('viewMapperConfig', $viewMapperConfig);

        return $viewMapperConfig;
    }

    /**
     * @return array
     */
    public function getMapperChains()
    {
        $config = $this->getMapperChainConfig();

        return $config->getMapperChains();
    }

    /**
     * @return ModuleMapperChainConfigInterface
     */
    public function getMapperChainConfig()
    {
        $config = new \ModuleMapperChainConfig();
        $config->loadFromString($this->sqlData['mapper_chain']);

        return $config;
    }

    /**
     * @return TPkgViewRendererSnippetDirectoryInterface
     */
    private function getViewRendererSnippetDirectory()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.snippet_directory');
    }
}
