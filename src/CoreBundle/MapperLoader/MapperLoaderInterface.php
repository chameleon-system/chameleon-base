<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\MapperLoader;

/**
 * MapperLoaderInterface defines a services that loads mapper instances.
 */
interface MapperLoaderInterface
{
    /**
     * Returns a mapper instance identified by $identifier. $identifier is either a service ID of a service defined in
     * the Symfony dependency container or a fully qualified class name. Implementations may also define additional
     * types of identifiers.
     *
     * @param string $identifier
     *
     * @return \IViewMapper
     *
     * @throws \LogicException if no mapper could be found for the passed identifier
     */
    public function getMapper($identifier);
}
