<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MapperVirtualSourceObject
{
    public function __call($name, $arguments)
    {
        return $this;
    }

    public static function __callStatic($name, $arguments)
    {
        return new self();
    }

    public function __set($name, $value)
    {
    }

    public function __get($name)
    {
        return $this;
    }

    public function __toString()
    {
        return '[null]';
    }
}
