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

use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceResponseInterface;

class DataMappingServiceResponse implements DataMappingServiceResponseInterface
{
    /**
     * @var array
     */
    private $mappedData = [];
    /**
     * @var array
     */
    private $cacheTrigger = [];

    /**
     * {@inheritdoc}
     */
    public function setMappedData(array $data)
    {
        $this->mappedData = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheTrigger(array $trigger)
    {
        $this->cacheTrigger = $trigger;
    }

    /**
     * {@inheritdoc}
     */
    public function getMappedData()
    {
        return $this->mappedData;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTrigger()
    {
        return $this->cacheTrigger;
    }
}
