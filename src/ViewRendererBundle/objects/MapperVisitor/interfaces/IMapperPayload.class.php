<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IMapperPayload
{
    /**
     * @param string $key
     *
     * @return mixed|MapperVirtualSourceObject
     *
     * @throws MapperException
     */
    public function GetSourceObject($key);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isVirtualSourceObject($key);

    /**
     * @return string|null
     */
    public function getSnippetName();
}
