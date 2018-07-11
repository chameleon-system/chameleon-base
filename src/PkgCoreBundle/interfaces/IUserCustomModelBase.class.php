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
 * @deprecated since 6.2.0 - no longer used.
 */
interface IUserCustomModelBase
{
    /**
     * the constructor - sets the pointer to the controller and fetches a
     * pointer to the global config singleton.
     *
     * @param TController $controller
     */
    public function __construct(TController &$controller);

    /**
     * called before any external functions get called, but after the constructor.
     */
    public function Init();

    /**
     * this function should fill the data array and return a pointer to it
     * (pointer because it may contain objects).
     *
     * @deprecated - use a mapper instead (see getMapper)
     *
     * @return array
     */
    public function &Execute();

    /**
     * return true if the method is white-listed for access without Authenticity token. Note: you will still need
     * to define the permitted methods via DefineInterface.
     *
     * @param string $sMethodName
     */
    public function AllowAccessWithoutAuthenticityToken($sMethodName);

    /**
     * Run a method within the module, but as an ajax call (no module will be used
     * and the function will output jason encoded data). The method assumes that
     * the name of the function that you want to execute is in the parameter _fnc.
     * Also note that the function being called needs to be included in $this->methodCallAllowed
     * You can control how the data will be encoded using the sOutputMode.
     */
    public function ExecuteAjaxCall();

    /**
     * executes the web module and shows a different view
     * use this method for exports like RSS feeds.
     *
     * @example http://yourblog.com/?module_fnc%5Bspota%5D=ExecuteExport&view=includes/rss2
     *
     * @param bool $bExit - set this to false if you want to grab the output and write it to a file
     */
    public function ExecuteExport($bExit = true);

    /**
     * returns an array holding the required style, js, and other info for the
     * module that needs to be loaded in the document head. each include should
     * be one element of the array, and should be formated exactly as it would
     * by another module that requires the same data (so it is not loaded twice).
     * the function will be called for every module on the page AUTOMATICALLY by
     * the controller (the controller will replace the tag "<!--#CMSHEADERCODE#-->" with
     * the results).
     *
     * @return array()
     */
    public function GetHtmlHeadIncludes();

    /**
     * returns an array holding the required js, html snippets, and other info for the
     * module that needs to be loaded in the document footer (before the ending </body> Tag).
     * Each include should be one element of the array, and should be formated exactly as it
     * would by another module that requires the same data (so it is not loaded twice).
     * the function will be called for every module on the page AUTOMATICALLY by
     * the controller (the controller will replace the tag "<!--#CMSFOOTERCODE#-->" with
     * the results).
     *
     * @return array()
     */
    public function GetHtmlFooterIncludes();

    /**
     * call a method of this module. validates request.
     *
     * @param array  $aMethodParameter - parameters to pass to the method
     * @param string $sMethodName      - name of the function
     *
     * @return var
     */
    public function &_CallMethod($sMethodName, $aMethodParameter = array());

    /**
     * returns an array of variables that should be replaced in the rendered module. Use this method to inject not-cachable data into
     * the complete module html.
     *
     * The Variables can be used in the HTML of the Module via [{NameOfVariable}]. This also works for view of other objects used by the module.
     *
     * @deprecated - use a mapper instead (see getMapper)
     *
     * @return array
     */
    public function GetPostRenderVariables();

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @deprecated - use a mapper instead (see getMapper)
     *
     * @return bool
     */
    public function _AllowCache();

    /**
     * if this function returns true and all modules within the page are cacheable
     * then the entire page will be cached. Set this to false if your module uses internal
     * parameters for caching. Example: using date('Ym'); in _GetCacheParameters.
     *
     * @return bool
     */
    public function AllowPageCache();

    /**
     * clear cache of current version - needed if the output is changed dynamically.
     */
    public function ClearCache();

    /**
     * return an assoc array of parameters that describe the state of the module.
     *
     * @deprecated - use a mapper instead (see getMapper)
     *
     * @return array
     */
    public function _GetCacheParameters();

    /**
     * if the content that is to be cached comes from the database (as ist most often the case)
     * then this function should return an array of assoc arrays that point to the
     * tables and records that are associated with the content. one table entry has
     * two fields:
     *   - table - the name of the table
     *   - id    - the record in question. if this is empty, then any record change in that
     *             table will result in a cache clear.
     *
     * @deprecated - use a mapper instead (see getMapper)
     *
     * @return array
     */
    public function _GetCacheTableInfos();

    /**
     * returns the state of the module for wrapping automatically a div around by the module loader.
     *
     * @return bool
     */
    public function IsHTMLDivWrappingAllowed();

    /**
     * injects virtual module spots with modules in $oModuleLoader->modules.
     *
     * @see MTPkgMultiModuleCoreEndPoint::InjectVirtualModuleSpots
     *
     * @param TUserModuleLoader
     */
    public function InjectVirtualModuleSpots(&$oModuleLoader);

    /**
     * cms calls this function to create an instance of the module. overwrite it
     * to do additional processing (like creating a record in a related table)
     * NOTICE: this function is currently not called for the user module!
     *
     * @param string $sName    name of the instance
     * @param int    $moduleID id of the module
     * @param string $view     name of the view
     */
    public function OnCreateInstance($sName, $moduleID, $view);

    /**
     * removes the module instance from the database. overwrite the function
     * to remove related tables. note that it will by default delete all records
     * in all tables directly linked to the module. HOWEVER it will not delete
     * records in related tables! these will have to be removed by hand in
     * the specific implementations of this class.
     */
    public function OnDeleteInstance();

    /**
     * use this method to set your module based custom navigation
     * you need to handle the navigation li classes to identify firstNode,
     * lastNode and activeNode in your method.
     *
     * @return string - html ul,li
     */
    public function GenerateModuleNavigation();
}
