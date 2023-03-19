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
use ChameleonSystem\CoreBundle\ServiceLocator;
use Doctrine\DBAL\Exception;
use TCMSTableToClass;

class TPkgCoreAutoClassHandler_TableClass extends TPkgCoreAutoClassHandler_AbstractBase
{

    public function create(string $sClassName, string $targetDir): void
    {
        $tableConfId = $this->getTableConfIdForClassName($sClassName);
        if (null === $tableConfId) {
            return;
        }
        $oClassWriter = new TCMSTableToClass($this->fileManager, $targetDir);
        if ($oClassWriter->Load($tableConfId)) {
            $oClassWriter->Update($sClassName);
        }
    }

    /**
     * @return int|string|null
     */
    private function getTableConfIdForClassName(string $className)
    {
        if (TCMSTableToClass::PREFIX_CLASS === substr($className, 0, strlen(TCMSTableToClass::PREFIX_CLASS))) {
            $tableName = substr($className, strlen(TCMSTableToClass::PREFIX_CLASS));
        } elseif (TCMSTableToClass::PREFIX_CLASS_AUTO === substr($className, 0, strlen(TCMSTableToClass::PREFIX_CLASS_AUTO))) {
            $tableName = substr($className, strlen(TCMSTableToClass::PREFIX_CLASS_AUTO));
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

    private function getTableConfIdForTableName(string $tableName): ?string
    {
        $data = $this->getAutoClassesDataAccess()->getTableConfigData();
        foreach ($data as $id => $tableConf) {
            if ($tableConf['name'] === $tableName) {
                return $id;
            }
        }

        return null;
    }


    public function getClassNameFromKey(string $sKey): string
    {
        return TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sKey);
    }


    public function canHandleClass(string $sClassName): bool
    {
        $bIsTdbObject = (TCMSTableToClass::PREFIX_CLASS == substr(
            $sClassName,
            0,
            strlen(TCMSTableToClass::PREFIX_CLASS)
        ));
        if (true === $bIsTdbObject) {
            return true;
        }

        $bIsTAdbObject = (TCMSTableToClass::PREFIX_CLASS_AUTO == substr(
            $sClassName,
            0,
            strlen(TCMSTableToClass::PREFIX_CLASS_AUTO)
        ));
        if (true === $bIsTAdbObject) {
            return true;
        }

        return false;
    }

    /**
     * return an array holding classes the handler is responsible for.
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getClassNameList(): array
    {
        if (null === $this->aClassNameList) {
            $this->aClassNameList = array();
            $query = 'SELECT `name` FROM `cms_tbl_conf` ORDER BY `cmsident`';
            $tRes = $this->getDatabaseConnection()->executeQuery($query);
            while ($aRow = $tRes->fetchNumeric()) {
                $this->aClassNameList[] = $this->getClassNameFromKey($aRow[0]);
            }
        }

        return $this->aClassNameList;
    }

}
