<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IMapperRequirementsRestricted
{
    /**
     * Set requirement for source object
     * should be passed right to the view - without being available to the Accept method in the mapper.
     *
     * @param string $key
     * @param string $sType
     * @param bool $bOptional
     *
     * @return void
     */
    public function NeedsSourceObject($key, $sType = null, $sDefault = null, $bOptional = false);
}
