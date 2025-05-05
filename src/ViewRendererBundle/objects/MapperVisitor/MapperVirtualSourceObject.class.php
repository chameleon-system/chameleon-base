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
    /**
     * @param string $name
     * @param mixed[] $arguments
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        return $this;
    }

    /**
     * @param string $name
     * @param mixed[] $arguments
     *
     * @return MapperVirtualSourceObject
     */
    public static function __callStatic($name, $arguments)
    {
        return new self();
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function __set($name, $value)
    {
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function __get($name)
    {
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '[null]';
    }
}
