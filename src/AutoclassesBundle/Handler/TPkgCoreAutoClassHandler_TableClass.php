<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\Handler;

use ChameleonSystem\AutoclassesBundle\DataAccess\AutoclassesDataAccessInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Filesystem\Filesystem;

class TPkgCoreAutoClassHandler_TableClass extends TPkgCoreAutoClassHandler_AbstractBase
{
    private AutoclassesDataAccessInterface $autoClassesDataAccess;

    public function __construct(Connection $databaseConnection, Filesystem $filemanager, AutoclassesDataAccessInterface $autoClassesDataAccess)
    {
        parent::__construct($databaseConnection, $filemanager);
        $this->autoClassesDataAccess = $autoClassesDataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function create($sClassName, $targetDir)
    {
        $tableConfId = $this->getTableConfIdForClassName($sClassName);
        if (null === $tableConfId) {
            return;
        }
        $oClassWriter = new \TCMSTableToClass($this->filemanager, $targetDir, $this->autoClassesDataAccess, $this->getDatabaseConnection());
        if ($oClassWriter->Load($tableConfId)) {
            $oClassWriter->Update($sClassName);
        }
    }

    /**
     * @param string $className
     *
     * @return int|string|null
     */
    private function getTableConfIdForClassName($className)
    {
        if (\TCMSTableToClass::PREFIX_CLASS === substr($className, 0, strlen(\TCMSTableToClass::PREFIX_CLASS))) {
            $tableName = substr($className, strlen(\TCMSTableToClass::PREFIX_CLASS));
        } elseif (\TCMSTableToClass::PREFIX_CLASS_AUTO === substr($className, 0, strlen(\TCMSTableToClass::PREFIX_CLASS_AUTO))) {
            $tableName = substr($className, strlen(\TCMSTableToClass::PREFIX_CLASS_AUTO));
        }

        $tableName = preg_replace('/([[:upper:]])/', '_$1', $tableName);
        if ('_' === substr($tableName, 0, 1)) {
            $tableName = substr($tableName, 1);
        }
        $tableName = strtolower($tableName);

        $tableConfId = $this->getTableConfIdForTableName($tableName);
        if (null === $tableConfId) {
            // The class name may end in "List" because it is a list version of the class. Try again with the base name.
            if ('_list' === substr($tableName, -5)) {
                return $this->getTableConfIdForTableName(substr($tableName, 0, -5));
            } else {
                return null;
            }
        } else {
            return $tableConfId;
        }
    }

    /**
     * @param string $tableName
     *
     * @return string|null
     */
    private function getTableConfIdForTableName($tableName)
    {
        $data = $this->autoClassesDataAccess->getTableConfigData();
        foreach ($data as $id => $tableConf) {
            if ($tableConf['name'] === $tableName) {
                return $id;
            }
        }

        return null;
    }

    /**
     * converts the key under which the auto class definition is stored into the class name which the key stands for.
     *
     * @param string $sKey
     *
     * @return string
     */
    public function getClassNameFromKey($sKey)
    {
        return \TCMSTableToClass::GetClassName(\TCMSTableToClass::PREFIX_CLASS, $sKey);
    }

    /**
     * returns true if the auto class handler knows how to handle the class name passed.
     *
     * @param string $sClassName
     *
     * @return bool
     */
    public function canHandleClass($sClassName)
    {
        $bIsTdbObject = (\TCMSTableToClass::PREFIX_CLASS == substr(
            $sClassName,
            0,
            strlen(\TCMSTableToClass::PREFIX_CLASS)
        ));
        if (true === $bIsTdbObject) {
            return true;
        }

        $bIsTAdbObject = (\TCMSTableToClass::PREFIX_CLASS_AUTO == substr(
            $sClassName,
            0,
            strlen(\TCMSTableToClass::PREFIX_CLASS_AUTO)
        ));
        if (true === $bIsTAdbObject) {
            return true;
        }

        return false;
    }

    /**
     * return an array holding classes the handler is responsible for.
     *
     * @return array
     */
    public function getClassNameList()
    {
        if (null === $this->aClassNameList) {
            $this->aClassNameList = [];
            $query = 'SELECT `name` FROM `cms_tbl_conf` ORDER BY `cmsident`';
            $tRes = $this->getDatabaseConnection()->query($query);
            while ($aRow = $tRes->fetch(\PDO::FETCH_NUM)) {
                $this->aClassNameList[] = $this->getClassNameFromKey($aRow[0]);
            }
        }

        return $this->aClassNameList;
    }
}
