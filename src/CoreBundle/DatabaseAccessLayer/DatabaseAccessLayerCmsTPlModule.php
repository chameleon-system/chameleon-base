<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DatabaseAccessLayer;

class DatabaseAccessLayerCmsTPlModule extends AbstractDatabaseAccessLayer
{
    private $isLoaded = false;

    /**
     * @param $classOrId
     *
     * @return null|\TdbCmsTplModule
     */
    public function loadFromClassOrServiceId($classOrId)
    {
        $this->loadAllModules();
        $keyMappingData = array('classname' => $classOrId);
        $mappedKey = $this->getMapLookupKey($keyMappingData);
        $data = $this->getFromCacheViaMappedKey($mappedKey);
        if (null !== $data) {
            return $data;
        }

        return null;
    }

    /**
     * @param $id
     *
     * @return null|\TdbCmsTplModule
     */
    public function loadFromId($id)
    {
        $this->loadAllModules();

        return $this->getFromCache($id);
    }

    public function loadFromField($field, $value)
    {
        $this->loadAllModules();
        $matches = $this->findDbObjectFromFieldInCache($field, $value);
        if (0 === count($matches)) {
            return null;
        }

        return $matches[0];
    }

    private function loadAllModules()
    {
        if (true === $this->isLoaded) {
            return;
        }
        $this->isLoaded = true;

        $query = 'select * from `cms_tpl_module`';
        $modules = $this->getDatabaseConnection()->fetchAll($query);
        foreach ($modules as $module) {
            $className = $module['classname'];
            $id = $module['id'];
            $object = \TdbCmsTplModule::GetNewInstance($module);
            $this->setCache($id, $object);
            $keyMappingData = array('classname' => $className);
            $mappedKey = $this->getMapLookupKey($keyMappingData);

            $this->setCacheKeyMapping($mappedKey, $id);
        }
    }
}
