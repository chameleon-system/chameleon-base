<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\objects;

use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceHelperFactoryInterface;

class DataMappingServiceHelperFactory implements DataMappingServiceHelperFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createMapperVisitor(array $sourceObjects)
    {
        return new \MapperVisitor($sourceObjects);
    }

    /**
     * {@inheritdoc}
     */
    public function createCacheTriggerCollector()
    {
        return new \MapperCacheTrigger();
    }

    /**
     * {@inheritdoc}
     */
    public function createRequirementsVisitor()
    {
        return new \MapperRequirements();
    }
}
