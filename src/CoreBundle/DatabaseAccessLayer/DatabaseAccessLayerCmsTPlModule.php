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
    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * @param string $classOrId
     *
     * @return \TdbCmsTplModule|null
     */
    public function loadFromClassOrServiceId($classOrId)
    {
        $this->loadAllModules();
        $keyMappingData = ['classname' => $classOrId];
        $mappedKey = $this->getMapLookupKey($keyMappingData);
        $data = $this->getFromCacheViaMappedKey($mappedKey);

        return $data ?? null;
    }

    /**
     * @param string $id
     *
     * @return \TdbCmsTplModule|null
     */
    public function loadFromId($id)
    {
        $this->loadAllModules();

        return $this->getFromCache($id);
    }

    /**
     * @param string $field
     * @param string $value
     *
     * @return mixed|null
     */
    public function loadFromField($field, $value)
    {
        $this->loadAllModules();
        $matches = $this->findDbObjectFromFieldInCache($field, $value);
        if (0 === count($matches)) {
            return null;
        }

        return $matches[0];
    }

    private function loadAllModules(): void
    {
        if (true === $this->isLoaded) {
            return;
        }
        $this->isLoaded = true;

        $query = 'select * from `cms_tpl_module`';
        $modules = $this->getDatabaseConnection()->fetchAllAssociative($query);
        foreach ($modules as $module) {
            $className = $module['classname'];
            $id = $module['id'];
            $object = \TdbCmsTplModule::GetNewInstance($module);
            $this->setCache($id, $object);
            $keyMappingData = ['classname' => $className];
            $mappedKey = $this->getMapLookupKey($keyMappingData);

            $this->setCacheKeyMapping($mappedKey, $id);
        }
    }
}
