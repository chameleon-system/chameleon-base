<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsStringUtilitiesBundle\Interfaces;

interface ArrayUtilityServiceInterface
{
    /**
     * return true if two arrays are equal.
     *
     * @return bool
     */
    public function equal(array $array1, array $array2);
}
