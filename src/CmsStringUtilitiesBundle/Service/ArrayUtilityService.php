<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsStringUtilitiesBundle\Service;

use ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\ArrayUtilityServiceInterface;

class ArrayUtilityService implements ArrayUtilityServiceInterface
{
    /**
     * return true if two arrays are equal.
     *
     * @return bool
     */
    public function equal(array $array1, array $array2)
    {
        if (count($array1) !== count($array2)) {
            return false;
        }

        ksort($array1);
        ksort($array2);
        $copyArray2 = $array2;
        foreach ($array1 as $key => $value) {
            if (!isset($array2[$key])) {
                return false;
            }
            if (is_array($value) && is_array($array2[$key])) {
                if (false === $this->equal($value, $array2[$key])) {
                    return false;
                }
            } else {
                if ($value !== $array2[$key]) {
                    return false;
                }
            }
            unset($copyArray2[$key]);
        }

        if (0 !== count($copyArray2)) {
            return false;
        }

        return true;
    }
}
