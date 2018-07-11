<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\objects\interfaces;

use IMapperCacheTrigger;
use IMapperRequirements;
use IMapperVisitor;

interface DataMappingServiceHelperFactoryInterface
{
    /**
     * @param array $sourceObjects
     *
     * @return IMapperVisitor
     */
    public function createMapperVisitor(array $sourceObjects);

    /**
     * @return IMapperCacheTrigger
     */
    public function createCacheTriggerCollector();

    /**
     * @return IMapperRequirements
     */
    public function createRequirementsVisitor();
}
