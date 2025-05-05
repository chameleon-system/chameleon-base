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

use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceResponseFactoryInterface;

class DataMappingServiceResponseFactory implements DataMappingServiceResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createResponse(\IMapperVisitor $visitor, \IMapperCacheTrigger $cacheTrigger)
    {
        $response = new DataMappingServiceResponse();
        $trigger = $cacheTrigger->getTrigger();
        $triggersAddedByUsedMapperChains = $visitor->getCacheTriggerCollectedForMapperChainsExecuted();
        if (count($triggersAddedByUsedMapperChains) > 0) {
            if (null === $trigger) {
                $trigger = $triggersAddedByUsedMapperChains;
            } else {
                $trigger = array_merge($trigger, $triggersAddedByUsedMapperChains);
            }
        }
        if (null !== $trigger) {
            $response->setCacheTrigger($trigger);
        }
        $mappedData = $visitor->GetMappedValues();
        if (null !== $mappedData) {
            $response->setMappedData($mappedData);
        }

        return $response;
    }
}
