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

class DatabaseAccessLayerCmsMasterPagedefSpotParameter extends AbstractDatabaseAccessLayer
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
    public function getParameterForSpot($spotId)
    {
        $this->loadAllParameters();

        return $this->getFromCache($spotId);
    }

    /**
     * @return void
     */
    private function loadAllParameters()
    {
        if (true === $this->isLoaded) {
            return;
        }
        $this->isLoaded = true;

        $query = 'SELECT * FROM `cms_master_pagedef_spot_parameter`';
        $parameters = $this->getDatabaseConnection()->fetchAllAssociative($query);
        $data = [];
        foreach ($parameters as $parameter) {
            $cmsMasterPagedefSpotId = $parameter['cms_master_pagedef_spot_id'];
            if (false === isset($data[$cmsMasterPagedefSpotId])) {
                $data[$cmsMasterPagedefSpotId] = [];
            }
            $data[$cmsMasterPagedefSpotId][] = $parameter;
        }

        foreach ($data as $cmsMasterPagedefSpotId => $content) {
            $this->setCache($cmsMasterPagedefSpotId, $content);
        }
    }
}
