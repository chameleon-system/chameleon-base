<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

abstract class TCMSUserInputFilter_BaseClass
{
    /**
     * run the filter.
     *
     * @template T of string|array<array-key, string>
     *
     * @param T $sValue
     *
     * @return T
     */
    public function Filter($sValue)
    {
        if (is_array($sValue)) {
            reset($sValue);
            foreach ($sValue as $sKey => $sTmpVal) {
                $sValue[$sKey] = $this->Filter($sTmpVal);
            }
            reset($sValue);
        } else {
            $sValue = $this->FilterItem($sValue);
        }

        return $sValue;
    }

    /**
     * filter a single item.
     *
     * @param string $sValue
     *
     * @return string
     */
    abstract protected function FilterItem($sValue);
}
