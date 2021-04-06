<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\DataAccess\AutoclassesDataAccessInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Doctrine\DBAL\Connection;

/**
 * used to create a class based on a table definition.
/**/
class TCMSTableToClass
{
    /**
     * table conf to convert.
     *
     * @var array
     */
    protected $aTableConf;

    const PREFIX_CLASS = 'Tdb';
    const PREFIX_CLASS_AUTO = 'TAdb';
    const PREFIX_CLASS_AUTO_CUSTOM = 'TACdb';
    const PREFIX_PROPERTY = 'field';
    /**
     * @var IPkgCmsFileManager
     */
    private $filemanager;
    /**
     * @var string
     */
    private $cachedir;

    /**
     * @param string $tableConfId
     *
     * @return bool
     */
    public function Load($tableConfId)
    {
        $bLoadSuccess = false;
        $this->aTableConf = $this->loadRecord($tableConfId);
        // we do not allow the use of Custom-Core for now...
        if (is_array($this->aTableConf) && count($this->aTableConf) > 0) {
            if ('Custom-Core' === $this->aTableConf['dbobject_type']) {
                $this->aTableConf['dbobject_type'] = 'Customer';
            }

            $bLoadSuccess = true;
        }

        return $bLoadSuccess;
    }

    /**
     * @param string $sOldName
     * @param array  $aOldData
     */
    public function Update($sOldName = '', $aOldData = array())
    {
        // overwrite the auto class
        $this->WriteAutoClass();
        $aData = $this->GetClassData();
        $aCurrentExtensionClass = array(
            'sExtendsClassName' => $aData['sAutoClassName'],
            'sExtendsClassSubType' => $aData['sAutoClassSubtype'],
            'sExtendsClassType' => $aData['sAutoClassType'],

            'sExtendsClassNameList' => $aData['sAutoClassName'].'List',
            'sExtendsClassSubTypeList' => $aData['sAutoClassSubtype'],
            'sExtendsClassTypeList' => $aData['sAutoClassType'],
        );
        // write inbetween classes

        $extensionData = $this->getTableExtensionData($this->aTableConf['id']);
        $nameExtension = null;
        $nameListExtension = null;
        foreach ($extensionData as $data) {
            if (null === $nameExtension && '' !== $data['name']) {
                $nameExtension = $data;
            }
            if (null === $nameListExtension && '' !== $data['name_list']) {
                $nameListExtension = $data;
            }
            if (null !== $nameExtension && null !== $nameListExtension) {
                break;
            }
        }

        if (null !== $nameExtension) {
            $aCurrentExtensionClass['sExtendsClassName'] = $nameExtension['name'];
            $aCurrentExtensionClass['sExtendsClassSubType'] = $nameExtension['subtype'];
            $aCurrentExtensionClass['sExtendsClassType'] = $nameExtension['type'];
        }

        if (null !== $nameListExtension) {
            $aCurrentExtensionClass['sExtendsClassNameList'] = $nameListExtension['name_list'];
            $aCurrentExtensionClass['sExtendsClassSubTypeList'] = $nameListExtension['subtype'];
            $aCurrentExtensionClass['sExtendsClassTypeList'] = $nameListExtension['type'];
        }

        // now create stub
        $aStubData = array(
            'sTableName' => $aData['sTableDBName'],
            'sClassName' => $aData['sClassName'],
            'sClassSubType' => $aData['sClassSubtype'],
            'sClassType' => $aData['sClassType'],
            'sExtendsClassName' => $aCurrentExtensionClass['sExtendsClassName'],
            'sExtendsClassSubType' => $aCurrentExtensionClass['sExtendsClassSubType'],
            'sExtendsClassType' => $aCurrentExtensionClass['sExtendsClassType'], );

        $aStubOldData = array(
            'sClassName' => $aStubData['sClassName'],
            'sClassSubType' => $aStubData['sClassSubType'],
            'sClassType' => $aStubData['sClassType'],
        );
        if (is_array($aOldData) && count($aOldData) > 0) {
            $aStubOldData = array(
                'sClassName' => self::GetClassName(self::PREFIX_CLASS, $aOldData['name']),
                'sClassSubType' => 'CMSDataObjects',
                'sClassType' => $aOldData['dbobject_type'],
            );
        }

        $this->WriteStubClass($aStubData, $aStubOldData);

        $aStubData['sClassName'] = $aStubData['sClassName'].'List';
        $aStubOldData['sClassName'] = $aStubOldData['sClassName'].'List';

        $aStubData['sExtendsClassName'] = $aCurrentExtensionClass['sExtendsClassNameList'];
        $aStubData['sExtendsClassSubType'] = $aCurrentExtensionClass['sExtendsClassSubTypeList'];
        $aStubData['sExtendsClassType'] = $aCurrentExtensionClass['sExtendsClassTypeList'];

        $this->WriteStubClass($aStubData, $aStubOldData);

        $sType = $this->aTableConf['dbobject_type'];
        if (array_key_exists('dbobject_type', $aOldData)) {
            $sType = $aOldData['dbobject_type'];
        }

        if ($sOldName !== $this->aTableConf['name'] || $sType !== $this->aTableConf['dbobject_type']) {
            // need to delete the old class
            $sFile = realpath($this->GetClassRootPath($sType).'/CMSAutoDataObjects').'/'.self::GetClassName(self::PREFIX_CLASS_AUTO, $sOldName).'.class.php';
            if (file_exists($sFile)) {
                $this->filemanager->unlink($sFile);
            }
            $sFile = realpath($this->GetClassRootPath($sType).'/CMSAutoDataObjects').'/'.self::GetClassName(self::PREFIX_CLASS_AUTO, $sOldName).'List.class.php';
            if (file_exists($sFile)) {
                $this->filemanager->unlink($sFile);
            }
        }
    }

    /**
     * @param string $targetTableConfId
     *
     * @return array
     */
    private function getTableExtensionData($targetTableConfId)
    {
        $data = $this->getAutoclassesDataAccess()->getTableExtensionData();
        if (isset($data[$targetTableConfId])) {
            return $data[$targetTableConfId];
        } else {
            return array();
        }
    }

    public function Create()
    {
        $this->Update();
    }

    public function Delete()
    {
        // delete stubs and auto class...
        $aData = $this->GetClassData();
        $sBaseClassFile = realpath($this->GetClassRootPath().$aData['sAutoClassSubtype']).'/'.$aData['sAutoClassName'].'.class.php';
        if (file_exists($sBaseClassFile)) {
            $this->filemanager->unlink($sBaseClassFile);
        }

        $sBaseClassFile = realpath($this->GetClassRootPath().$aData['sAutoClassSubtype']).'/'.$aData['sAutoClassName'].'List.class.php';
        if (file_exists($sBaseClassFile)) {
            $this->filemanager->unlink($sBaseClassFile);
        }

        $sFile = realpath($this->GetClassRootPath($aData['sClassType']).$aData['sClassSubtype']).'/'.$aData['sClassName'].'.class.php';
        if (file_exists($sFile)) {
            $this->filemanager->unlink($sFile);
        }
        $sFile = realpath($this->GetClassRootPath($aData['sClassType']).$aData['sClassSubtype']).'/'.$aData['sClassName'].'List.class.php';
        if (file_exists($sFile)) {
            $this->filemanager->unlink($sFile);
        }
    }

    /**
     * returns true if the class name is a database object class.
     *
     * @param string $sClassName
     *
     * @return bool
     */
    public static function IsDatabaseObjectClass($sClassName)
    {
        $prefixLength = strlen(self::PREFIX_CLASS);

        return self::PREFIX_CLASS == substr($sClassName, 0, $prefixLength) || self::PREFIX_CLASS_AUTO == substr($sClassName, 0, strlen(self::PREFIX_CLASS_AUTO));
    }

    protected function WriteAutoClass()
    {
        $oViewParser = new TViewParser();
        /** @var $oViewParser TViewParser */
        $aData = $this->GetClassData();

        $oViewParser->AddVarArray($aData);
        $oViewParser->AddVarArray($this->getGlobalData());
        $oViewParser->bShowTemplatePathAsHTMLHint = false;

        $sAutoClassString = $oViewParser->RenderObjectView('record', 'TCMSTableToClass');

        // save result...
        $sBaseClassFile = realpath($this->GetClassRootPath().$aData['sAutoClassSubtype']).'/'.$aData['sAutoClassName'].'.class.php';
        if ($fp = fopen($sBaseClassFile, 'wb')) {
            if (fwrite($fp, $sAutoClassString)) {
                fclose($fp);

                $this->filemanager->put($sBaseClassFile, $sBaseClassFile, 0777, true);
            }
        }

        $sAutoClassString = $oViewParser->RenderObjectView('recordList', 'TCMSTableToClass');

        // save result...
        $sBaseListClassFile = realpath($this->GetClassRootPath().$aData['sAutoClassSubtype']).'/'.$aData['sAutoClassName'].'List.class.php';

        if ($fp = fopen($sBaseListClassFile, 'wb')) {
            if (fwrite($fp, $sAutoClassString)) {
                fclose($fp);

                $this->filemanager->put($sBaseListClassFile, $sBaseListClassFile, 0777, true);
            }
        }
    }

    /**
     * update a new inbetween class...
     *
     * @param array  $aData    - array('sTableName'=>'','sClassName'=>'', 'sClassSubType'=>'', 'sClassType'=>'', 'sExtendsClassName'=>'', 'sExtendsClassSubType'=>'', 'sExtendsClassType'=>'')
     * @param array  $aOldData - array('sClassName'=>'', 'sClassSubType'=>'', 'sClassType'=>'')
     * @param string $sPostFix
     */
    protected function WriteStubClass($aData, $aOldData, $sPostFix = '')
    {
        $sFile = realpath($this->GetClassRootPath($aData['sClassType']).$aData['sClassSubType']).'/'.$aData['sClassName'].$sPostFix.'.class.php';
        $sOldFile = realpath($this->GetClassRootPath($aOldData['sClassType']).$aOldData['sClassSubType']).'/'.$aOldData['sClassName'].$sPostFix.'.class.php';

        $oViewParser = new TViewParser();
        $oViewParser->AddVarArray($aData);
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $sClassString = $oViewParser->RenderObjectView('recordCustom'.$sPostFix, 'TCMSTableToClass');

        if (file_exists($sOldFile)) {
            $this->filemanager->unlink($sOldFile);
        }

        if ($fp = fopen($sFile, 'wb')) {
            if (fwrite($fp, $sClassString)) {
                fclose($fp);

                $this->filemanager->put($sFile, $sFile, 0777, true);
            }
        }
    }

    /**
     * @param string $sTableId
     *
     * @return TIterator
     */
    private function getFields($sTableId)
    {
        $data = $this->getAutoclassesDataAccess()->getFieldData();

        if (isset($data[$sTableId])) {
            return $data[$sTableId];
        } else {
            return new TIterator();
        }
    }

    /**
     * @return array
     */
    protected function GetClassData()
    {
        $aTableNotes = trim($this->aTableConf['notes']);
        if (!empty($aTableNotes)) {
            $aTableNotes = wordwrap($aTableNotes, 80);
            $aTableNotes = explode("\n", $aTableNotes);
        }
        /** @var $oFields TIterator */
        $oFields = $this->getFields($this->aTableConf['id']);

        $aData = array(
            'sClassName' => self::GetClassName(self::PREFIX_CLASS, $this->aTableConf['name']),
            'sClassType' => $this->aTableConf['dbobject_type'],
            'sClassSubtype' => 'CMSDataObjects',

            'sAutoClassName' => self::GetClassName(self::PREFIX_CLASS_AUTO, $this->aTableConf['name']),
            'sAutoClassType' => $this->aTableConf['dbobject_type'], // fixed to customer for now

            // @todo this should be renamed because it's actually the folder name, not the subtype
            'sAutoClassSubtype' => 'CMSAutoDataObjects',

            'sAutoCustomClassName' => self::GetClassName(self::PREFIX_CLASS_AUTO, $this->aTableConf['name']),
            'sAutoCustomClassType' => $this->aTableConf['dbobject_type'], // fixed to customer for now

            'sAutoCustomClassSubtype' => 'CMSAutoDataObjects',

            'year' => date('Y'), 'timestamp' => date('Y-m-d H:i:s'),
            'sTableDBName' => $this->aTableConf['name'],
            'sTableName' => $this->aTableConf['translation'],
            'aTableNotes' => $aTableNotes,
            'oFields' => $oFields,

            'sParentClass' => $this->aTableConf['dbobject_extend_class'],
            'sParentClassSubType' => $this->aTableConf['dbobject_extend_subtype'],
            'sParentClassType' => $this->aTableConf['dbobject_extend_type'],
            'aTableConf' => $this->aTableConf,
            'databaseConnection' => $this->getDatabaseConnection(),
        );

        $aData['isTableWithActiveWorkflow'] = false;
        $aData['sDisplayColumnName'] = $this->getDisplayColumnName();
        $aData['sDisplayColumnCallbackFunctionName'] = $this->getDisplayCallbackFunctionName();

        if ('Customer' !== $this->aTableConf['dbobject_type']) {
            $aData['sAutoCustomClassName'] = self::GetClassName(self::PREFIX_CLASS_AUTO_CUSTOM, $this->aTableConf['name']);
            $aData['sAutoCustomClassType'] = $this->aTableConf['dbobject_type'];

            // @todo this should be renamed because it's actually the folder name, not the subtype
            $aData['sAutoCustomClassSubtype'] = 'CMSDataObjects';
        }

        // add default query, and sorting...
        $aData['sCMSListQuery'] = "SELECT `{$aData['sTableDBName']}`.*\n"."                          FROM `{$aData['sTableDBName']}`\n".'                         WHERE [{sFilterConditions}]';

        $tableOrderBy = $this->getTableOrderBy($this->aTableConf['id']);

        $aOrderData = array();
        foreach ($tableOrderBy as $orderBy) {
            if ('`' === substr($orderBy['name'], 0, 1)) {
                $aOrderData[] = sprintf('%s %s', $orderBy['name'], $orderBy['sort_order_direction']);
            } else {
                $aOrderData[] = sprintf('`%s` %s', $orderBy['name'], $orderBy['sort_order_direction']);
            }
        }
        if (count($aOrderData) > 0) {
            $aData['sCMSListQuery'] .= "\n                      ORDER BY ".implode(",\n                               ", $aOrderData);
        }

        if (is_array($this->aTableConf) && array_key_exists('auto_limit_results', $this->aTableConf) && $this->aTableConf['auto_limit_results'] > -1) {
            $aData['sCMSListQuery'] .= ' LIMIT 0,'.$this->aTableConf['auto_limit_results'];
        }

        $extensionData = $this->getTableExtensionData($this->aTableConf['id']);
        // create stubs to join the end of the extensions to each other or to the TAdb class if needed
        $sAutoChainingStub = '';
        $sAutoChainingListStub = '';
        if (count($extensionData) > 0) {
            $aExtensionChain = array();
            $aExtensionChainList = array();

            foreach ($extensionData as $extension) {
                if ('' !== $extension['name']) {
                    $aExtensionChain[$extension['name']] = $extension;
                }
                if ('' !== $extension['name_list']) {
                    $aExtensionChainList[$extension['name_list']] = $extension;
                }
            }

            $sAutoChainingStub = $this->GetAutoChainingStub($aExtensionChain, $aData['sAutoClassName'], false);
            $sAutoChainingListStub = $this->GetAutoChainingStub($aExtensionChainList, $aData['sAutoClassName'].'List', true);
        }
        $aData['sAutoChainingStub'] = $sAutoChainingStub;
        $aData['sAutoChainingStubList'] = $sAutoChainingListStub;

        return $aData;
    }

    /**
     * @return string
     */
    private function getDisplayColumnName()
    {
        if (!empty($this->aTableConf['display_column'])) {
            return $this->aTableConf['display_column'];
        }

        if (!empty($this->aTableConf['name_column'])) {
            return $this->aTableConf['name_column'];
        }

        return 'name';
    }

    /**
     * @return string|null
     */
    private function getDisplayCallbackFunctionName()
    {
        if (isset($this->aTableConf['display_column_callback']) && !empty($this->aTableConf['display_column_callback'])) {
            return $this->aTableConf['display_column_callback'];
        }

        if (isset($this->aTableConf['name_column_callback']) && !empty($this->aTableConf['name_column_callback'])) {
            return $this->aTableConf['name_column_callback'];
        }

        return null;
    }

    /**
     * @return array
     */
    private function getGlobalData()
    {
        $data = array();

        $dataAccess = $this->getAutoclassesDataAccess();
        $data['cmsConfig'] = $dataAccess->getConfig();

        return $data;
    }

    /**
     * @param string $targetTableConfId
     *
     * @return array
     */
    private function getTableOrderBy($targetTableConfId)
    {
        $data = $this->getAutoclassesDataAccess()->getTableOrderByData();

        if (isset($data[$targetTableConfId])) {
            return $data[$targetTableConfId];
        } else {
            return array();
        }
    }

    /**
     * @param string $cachedir
     */
    public function setCachedir($cachedir)
    {
        $this->cachedir = $cachedir;
    }

    protected function GetTableProperties()
    {
        $aProperties = array();
        $oFields = $this->getFields($this->aTableConf['id']);
        $oFields->GoToStart();
        while ($oField = $oFields->Next()) {
            /** @var $oField TCMSField */
            $aProperties[] = $oField->RenderFieldPropertyString();
        }

        return $aProperties;
    }

    /**
     * convert a string into a camel case string.
     *
     * @param string $sqlName
     *
     * @return string
     */
    public static function ConvertToClassString($sqlName)
    {
        // replace any non alpha chars
        $sName = preg_replace('/[^A-Za-z0-9_]/', '', $sqlName);
        if (false === strpos($sName, '_')) {
            return ucfirst($sName);
        }

        $aParts = explode('_', $sName);
        foreach ($aParts as $key => $value) {
            if ('' === $value) {
                continue;
            }
            $aParts[$key] = ucfirst($value);
        }
        $sName = implode('', $aParts);

        return $sName;
    }

    /**
     * @param string $sPrefix
     * @param string $sName
     *
     * @return string
     */
    public static function GetClassName($sPrefix, $sName)
    {
        $sFullName = '';
        if (!empty($sName)) {
            $sFullName = $sPrefix.self::ConvertToClassString($sName);
        }

        return $sFullName;
    }

    /**
     * returns the path to ./classes. for now, we fix it to the extensions path. later
     * we will change it so that classes can be placed into the core as well.
     *
     * @todo check if this is needed anymore (classes get all placed in /customer/priv... ?)
     */
    public function GetClassRootPath($sType = null)
    {
        $path = $this->cachedir;
        foreach (array('CMSDataObjects', 'CMSAutoDataObjects') as $subdir) {
            if (!file_exists($path.$subdir)) {
                mkdir($path.$subdir, 0777, true);
            }
        }

        return $path;
    }

    /**
     * @param array  $aChain
     * @param string $sAutoClass
     * @param bool   $bIsListClass
     *
     * @return string
     */
    protected function GetAutoChainingStub($aChain, $sAutoClass, $bIsListClass)
    {
        $aClass = array();
        $sVirtualNamField = 'virtual_item_class_name';
        if ($bIsListClass) {
            $sVirtualNamField = 'virtual_item_class_list_name';
        }
        $aClassList = array_keys($aChain);
        $aClassList[] = $sAutoClass;
        $iCountList = count($aChain);
        for ($i = 0; $i < $iCountList; ++$i) {
            $sCurrentClass = $aClassList[$i];
            $sNextClass = $aClassList[$i + 1];
            if (!empty($aChain[$sCurrentClass][$sVirtualNamField])) {
                $aClass[$aChain[$sCurrentClass][$sVirtualNamField]] = 'class '.$aChain[$sCurrentClass][$sVirtualNamField].' extends '.$sNextClass.' {}';
            }
        }

        foreach ($aClass as $className => $classString) {
            $path = $this->GetClassRootPath().'/CMSAutoDataObjects/';
            $file = $path.$className.'.class.php';
            $classString = "<?php\n".$classString;
            file_put_contents($file, $classString);
        }
        $aClass = array_reverse($aClass);

        return implode("\n", $aClass);
    }

    /**
     * @param string $tableConfId
     *
     * @return array
     */
    private function loadRecord($tableConfId)
    {
        $data = $this->getAutoclassesDataAccess()->getTableConfigData();

        if (true === isset($data[$tableConfId])) {
            return $data[$tableConfId];
        } else {
            return array();
        }
    }

    /**
     * @param IPkgCmsFileManager $filemanager
     * @param string             $cachedir
     */
    public function __construct(IPkgCmsFileManager $filemanager, $cachedir)
    {
        $this->filemanager = $filemanager;
        $this->cachedir = $cachedir;
    }

    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }

    /**
     * @return AutoclassesDataAccessInterface
     */
    private function getAutoclassesDataAccess()
    {
        return ServiceLocator::get('chameleon_system_autoclasses.data_access.autoclasses');
    }
}
