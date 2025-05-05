<?php

namespace ChameleonSystem\CoreBundle\Util;

interface HashInterface
{
    /**
     * Create a 32-bit hash of any data type including an array. Note that two instances or two arrays with the same content
     * will create the same hash independent of of the order of the data within the data structure.
     *
     * @return string (32 char)
     */
    public function hash32($data);
}
