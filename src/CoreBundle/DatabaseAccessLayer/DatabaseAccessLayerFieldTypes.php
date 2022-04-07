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

class DatabaseAccessLayerFieldTypes extends AbstractDatabaseAccessLayer
{
    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * @param string $id
     *
     * @return \TdbCmsFieldType
     */
    public function getFieldType($id)
    {
        $this->loadAll();

        return $this->getFromCache($id);
    }

    /**
     * @return void
     */
    private function loadAll()
    {
        if (true === $this->isLoaded) {
            return;
        }
        $this->isLoaded = true;

        $query = 'select * from `cms_field_type`';

        $fieldTypes = $this->getDatabaseConnection()->fetchAll($query);
        foreach ($fieldTypes as $fieldType) {
            $treeObject = \TdbCmsFieldType::GetNewInstance($fieldType);
            $this->setCache($fieldType['id'], $treeObject);
        }
    }
}
