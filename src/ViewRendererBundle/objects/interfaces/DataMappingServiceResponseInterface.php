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

interface DataMappingServiceResponseInterface
{
    /**
     * @param array $data
     *
     * @return void
     */
    public function setMappedData(array $data);

    /**
     * @param array $trigger
     *
     * @return void
     */
    public function setCacheTrigger(array $trigger);

    /**
     * @return array
     */
    public function getMappedData();

    /**
     * @return array
     */
    public function getCacheTrigger();
}
