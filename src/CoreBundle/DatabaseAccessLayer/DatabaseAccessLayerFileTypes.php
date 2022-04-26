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

class DatabaseAccessLayerFileTypes extends AbstractDatabaseAccessLayer
{
    /**
     * @var \TdbCmsFiletype[]
     */
    private $cache;

    /**
     * @param string $id
     *
     * @return \TdbCmsFiletype
     */
    public function getFileType($id)
    {
        if (isset($this->cache[$id])) {
            return $this->cache[$id];
        }
        $this->cache[$id] = \TdbCmsFiletype::GetNewInstance($id);

        return $this->cache[$id];
    }
}
