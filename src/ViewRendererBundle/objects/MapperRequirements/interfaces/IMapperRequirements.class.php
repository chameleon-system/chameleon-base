<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IMapperRequirements extends IMapperRequirementsRestricted
{
    /**
     * This method is used by the active MapperVisitor to determine the requirements.
     *
     * @param string $key
     *
     * @return bool
     */
    public function CanHaveSourceObject($key);

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function getSourceObjectType($key);

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getSourceObjectDefault($key);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function getSourceObjectOptional($key);
}
