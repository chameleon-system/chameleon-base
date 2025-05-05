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
 * @property TdbPkgCmsClassManager $oTable
 * @property TdbPkgCmsClassManager $oTablePreChangeData
 */
class TPkgCmsClassManager_TableEditor extends TCMSTableEditor
{
    /**
     * gets called after save if all posted data was valid.
     *
     * @param TIterator $oFields holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     *
     * @return void
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);
        $classManager = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_cms_class_manager.manager');
        $classManager->load($this->oTable->fieldNameOfEntryPoint);
        if ($this->oTablePreChangeData && $this->oTablePreChangeData->fieldNameOfEntryPoint != $this->oTable->fieldNameOfEntryPoint) {
            $classManager->recreateAutoclasses();
        }
        $classManager->UpdateVirtualClasses();
    }

    /**
     * is called only from Delete method and calls all delete relevant methods
     * executes the final SQL Delete Query.
     *
     * @return void
     */
    protected function DeleteExecute()
    {
        /** @var ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesCacheWarmer $classManager */
        $classManager = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_autoclasses.cache_warmer');
        $classManager->updateAllTables();
        parent::DeleteExecute();
    }
}
