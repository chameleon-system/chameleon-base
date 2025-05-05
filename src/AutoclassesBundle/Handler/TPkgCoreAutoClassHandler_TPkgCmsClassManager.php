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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Filesystem\Filesystem;

class TPkgCoreAutoClassHandler_TPkgCmsClassManager extends TPkgCoreAutoClassHandler_AbstractBase
{
    private \TPkgCmsVirtualClassManager $virtualClassManager;

    public function __construct(Connection $databaseConnection, Filesystem $filemanager, \TPkgCmsVirtualClassManager $virtualClassManager)
    {
        parent::__construct($databaseConnection, $filemanager);
        $this->virtualClassManager = $virtualClassManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create($sClassName, $targetDir)
    {
        $virtualClassManager = clone $this->virtualClassManager;

        if (true === $virtualClassManager->load($sClassName)) {
            $virtualClassManager->UpdateVirtualClasses($targetDir);

            return;
        }

        if ('AutoParent' !== substr($sClassName, -10)) {
            trigger_error(
                "invalid class name {$sClassName} for TPkgCoreAutoClassHandler_TPkgCmsClassManager",
                E_USER_ERROR
            );
        }

        // my be a class extension auto parent glue
        $sClean = substr($sClassName, 0, -10);
        $sQuery = 'SELECT `pkg_cms_class_manager`.*
                 FROM `pkg_cms_class_manager`
           INNER JOIN `pkg_cms_class_manager_extension` ON `pkg_cms_class_manager`.`id` = `pkg_cms_class_manager_extension`.`pkg_cms_class_manager_id`
                WHERE `pkg_cms_class_manager_extension`.`class` = :cleanClassName
              ';
        $aClassManager = $this->getDatabaseConnection()->fetchAssociative($sQuery, ['cleanClassName' => $sClean]);
        if (false === $aClassManager) {
            trigger_error(
                "invalid class name {$sClassName} for TPkgCoreAutoClassHandler_TPkgCmsClassManager",
                E_USER_ERROR
            );
        }

        if (false === $virtualClassManager->load($aClassManager['name_of_entry_point'])) {
            trigger_error(
                "invalid class name {$sClassName} for TPkgCoreAutoClassHandler_TPkgCmsClassManager",
                E_USER_ERROR
            );
        }

        $virtualClassManager->UpdateVirtualClasses($targetDir);
    }

    /**
     * converts the key under which the auto class definition is stored into the class name which the key stands for.
     *
     * @param string $sKey
     *
     * @return string|false
     */
    public function getClassNameFromKey($sKey)
    {
        $sClassName = false;
        $query = 'SELECT `name_of_entry_point` FROM `pkg_cms_class_manager` WHERE `id` = :key';
        // @TODO This might not work? fetchAssociative returns an associative array
        if ($aClass = $this->getDatabaseConnection()->fetchAssociative($query, ['key' => $sKey])) {
            /** @psalm-suppress InvalidArrayOffset */
            $sClassName = $aClass[0];
        }

        return $sClassName;
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
        $aClassList = $this->getClassNameList();
        $bClassExists = in_array($sClassName, $aClassList);
        if ($bClassExists) {
            return true;
        }

        // it may also be an auto class
        if ('AutoParent' == substr($sClassName, -10)) {
            $sClean = substr($sClassName, 0, -10);
            $aExtensions = $this->getExtensionList();
            if (true === in_array($sClean, $aExtensions)) {
                return true;
            }
        }

        return false;
    }

    private function getExtensionList(): array
    {
        if (null === $this->aClassExtensionList) {
            $this->aClassExtensionList = [];
            $query = 'SELECT `class` FROM `pkg_cms_class_manager_extension` ORDER BY `cmsident`';
            $tRes = $this->getDatabaseConnection()->executeQuery($query);
            while ($aRow = $tRes->fetchNumeric()) {
                $this->aClassExtensionList[] = $aRow[0];
            }
        }

        return $this->aClassExtensionList;
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
            $query = 'SELECT `name_of_entry_point` FROM `pkg_cms_class_manager` ORDER BY `cmsident`';
            try {
                $tRes = $this->getDatabaseConnection()->executeQuery($query);

                while ($aRow = $tRes->fetchNumeric()) {
                    $this->aClassNameList[] = $aRow[0];
                }
            } catch (DBALException $e) {
                return [];
            }
        }

        return $this->aClassNameList;
    }
}
