<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * used to render module views. makes data as $data and under the key name available in the view.
/**/
class TViewParser
{
    /**
     * the template data.
     *
     * @var array
     */
    protected $aTemplateData = array();

    /**
     * defines key/value pairs of parameters that describe the unique cache element.
     *
     * @var array
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    protected $aCacheParameters = array();

    /**
     * @var array
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    protected $aCacheClearTriggers = array();

    /**
     * rendered output is loaded from cache if available.
     *
     * @var bool - default false
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    protected $bUseCaching = false;

    /**
     * shows a comment hint block with the template path.
     *
     * @var bool - default true
     */
    public $bShowTemplatePathAsHTMLHint = true;

    /**
     * add a variable to the system. the variable will be accessible in the view
     * under $data[$key], and the key name.
     *
     * @param string $key
     * @param mixed  $var
     */
    public function AddVar($key, $var)
    {
        $this->aTemplateData[$key] = $var;
    }

    /**
     * adds an array of variables to the template system
     * the variable will be accessible in the view under $data[$key],
     * and the key name.
     *
     * @param array $aData
     */
    public function AddVarArray($aData)
    {
        $this->aTemplateData = array_merge($this->aTemplateData, $aData);
    }

    /**
     * allow caching the result, and using that cached result.
     *
     * @param bool $bUse
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    public function UseCaching($bUse = true)
    {
    }

    /**
     * parameters identifying a cached instance.
     *
     * @param array $aParameters - assoc array: key=>value,...
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    public function SetCacheParameters($aParameters)
    {
    }

    /**
     * Changes to which tables will trigger the cached instance to be deleted.
     *
     * @param array $aClearTriggers - of the form array(array('table'=>'','id'=>''),....)
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    public function SetCacheClearTriggers($aClearTriggers)
    {
    }

    /**
     * render content.
     *
     * @param string $sModuleName - module name
     * @param string $sViewName   - view name
     *
     * @return string -rendert view
     */
    public function Render($sModuleName, $sViewName)
    {
        $sTemplatePath = $this->getViewPathManager()->getModuleViewPath($sModuleName, $sViewName);

        return $this->GetContent($sTemplatePath);
    }

    /**
     * clear cache for instance.
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    public function ClearCache()
    {
    }

    /**
     * renders from cache. returns falls if the object is not in cache
     * throws an exception if the object is not in cache.
     *
     * @return string
     *
     * @throws Exception
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    public function RenderFromCache()
    {
        throw new Exception('object not in cache');
    }

    /**
     * Render current data using sViewName in sSubType for either Core, Custom, or Customer.
     *
     * @param string $sViewName - name of the view (do not include .view.php)
     * @param string $sSubType  - path relative to objectviews
     * @param string $sType     - Core, Custom, Customer
     *
     * @example RenderObjectView('metadata','dbobjects/TCMSActivePage','Core')
     *
     * @return string
     */
    public function RenderObjectView($sViewName, $sSubType = '', $sType = 'Core')
    {
        $sTemplatePath = $this->getViewPathManager()->getObjectViewPath($sViewName, $sSubType, $sType);

        return $this->GetContent($sTemplatePath);
    }

    /**
     * Render current data using sViewName in sSubType for either Core, Custom, or Customer.
     *
     * @param string $sViewName   - name of the view (do not include .view.php)
     * @param string $sModuleName - name of the CMS backend module
     * @param string $sType       - Core, Custom, Customer
     *
     * @return string
     */
    public function RenderBackendModuleView($sViewName, $sModuleName = '', $sType = 'Core')
    {
        $sTemplatePath = $this->getViewPathManager()->getBackendModuleViewPath($sViewName, $sModuleName, $sType);

        return $this->GetContent($sTemplatePath);
    }

    /**
     * render current data using view.
     *
     * @param string $sPath - full path to view
     *
     * @return string
     */
    public function RenderBackendModuleViewByFullPath($sPath)
    {
        $sTemplatePath = $this->getViewPathManager()->getBackendModuleViewFromFullPath($sPath);

        return $this->GetContent($sTemplatePath);
    }

    /**
     * @param string $sViewName   - name of the view (do not include .view.php, include subdirs if neccessary)
     * @param string $sModuleName - name of the web module
     * @param string $sType       - Core, Custom, Customer
     *
     * @example RenderWebModuleView('includes/atom','MTBlogPostList','Customer')
     *
     * @return string
     */
    public function RenderWebModuleView($sViewName, $sModuleName, $sType = 'Customer')
    {
        $sTemplatePath = $this->getViewPathManager()->getWebModuleViewPath($sViewName, $sModuleName, $sType);

        return $this->GetContent($sTemplatePath);
    }

    /**
     * Render current data using sViewName in sSubType for either Core, Custom, or Customer
     * but assumes that the path is in ./classes.
     *
     *
     * @param string $sViewName - name of the view (do not include .view.php)
     * @param string $sSubType  - path relative to objectviews
     * @param string $sType     - Core, Custom, Customer
     *
     * @example RenderObjectPackageView('metadata','pkgShop/dbobjects/TCMSActivePage','Core')
     *
     * @return string
     */
    public function RenderObjectPackageView($sViewName, $sSubType = '', $sType = 'Core')
    {
        $sTemplatePath = $this->getViewPathManager()->getObjectPackageViewPath($sViewName, $sSubType, $sType);

        return $this->GetContent($sTemplatePath);
    }

    /**
     * renders the module/object view
     * outputs an error in html if view was not found.
     *
     * @param string $sTemplatePath - path to view
     *
     * @return string
     */
    protected function GetContent($sTemplatePath)
    {
        $content = '';
        $orgPath = $sTemplatePath;
        $sTemplatePath = realpath($sTemplatePath);
        if (false === $sTemplatePath) {
            if (_DEVELOPMENT_MODE) {
                $content = '<div style="background-color: #ffcccc; color: #900; border: 2px solid #c00; padding-left: 10px; margin-bottom: 8px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; font-weight: bold; font-size: 11px; min-height: 40px; display: block;">Error! view is missing: '.TGlobal::OutHTML($orgPath).'</div>';
            } else {
                $content = '<!-- MISSING VIEW - see log for details -->';
                TTools::WriteLogEntry('Error! view is missing: '.$orgPath, 1, __FILE__, __LINE__);
            }
        } else {
            if (file_exists($sTemplatePath) && is_file($sTemplatePath)) {
                unset($orgPath);
                if (!array_key_exists('data', $this->aTemplateData)) {
                    $data = &$this->aTemplateData;
                }
                extract($this->aTemplateData, EXTR_REFS || EXTR_SKIP);
                ob_start();
                require TGlobal::ProtectedPath($sTemplatePath);
                $content = ob_get_contents();
                ob_end_clean();
            } else {
                if (_DEVELOPMENT_MODE) {
                    $content = '<div style="background-color: #ffcccc; color: #900; border: 2px solid #c00; padding-left: 10px; margin-bottom: 8px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; font-weight: bold; font-size: 11px; min-height: 40px; display: block;">Error! view is missing: '.TGlobal::OutHTML($orgPath).'</div>';
                } else {
                    $content = '<!-- MISSING VIEW - see log for details -->';
                    TTools::WriteLogEntry("Error! view is missing: {$sTemplatePath} [org: {$orgPath}]", 1, __FILE__, __LINE__);
                }
            }
        }
        $bAllowHint = false;
        $showViewSourceHtmlHints = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.debug.show_view_source_html_hints');
        if ($showViewSourceHtmlHints && !empty($content) && _DEVELOPMENT_MODE) {
            $bAllowHint = true;
        }
        if ($bAllowHint && $this->bShowTemplatePathAsHTMLHint && !TGlobal::IsCMSMode()) {
            $sTemplatePath = $this->TransformHTMLHintPath($sTemplatePath);
            $content = "<!--\nSTART VIEW: {$sTemplatePath}\n-->{$content}<!--\nEND VIEW: {$sTemplatePath}\n-->";
        }

        return $content;
    }

    /**
     * overwrite this method to transform the view path hint to a more usable format
     * use this for example to convert the path to your local dev environment and IDE
     * (samba shares, FTP paths or whatever), this reduces your effort finding the right files.
     *
     * @param string $sTemplatePath
     *
     * @return string
     */
    protected function TransformHTMLHintPath($sTemplatePath)
    {
        return $sTemplatePath;
    }

    /**
     * @return IViewPathManager
     */
    private function getViewPathManager()
    {
        return ServiceLocator::get('chameleon_system_core.viewpathmanager');
    }
}
