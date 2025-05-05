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

class DatabaseAccessLayerCmsMasterPagedefSpotAccess extends AbstractDatabaseAccessLayer
{
    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * @param string $spotId
     *
     * @return array|null
     */
    public function getAccessForSpot($spotId)
    {
        $this->loadAll();

        return $this->getFromCache($spotId);
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

        $query = 'SELECT * FROM `cms_master_pagedef_spot_access`';
        $accessList = $this->getDatabaseConnection()->fetchAllAssociative($query);
        $data = [];
        foreach ($accessList as $access) {
            $cmsMasterPagedefSpotId = $access['cms_master_pagedef_spot_id'];
            if (false === isset($data[$cmsMasterPagedefSpotId])) {
                $data[$cmsMasterPagedefSpotId] = [];
            }
            $data[$cmsMasterPagedefSpotId][] = $access;
        }

        foreach ($data as $cmsMasterPagedefSpotId => $content) {
            $this->setCache($cmsMasterPagedefSpotId, $content);
        }
    }
}
