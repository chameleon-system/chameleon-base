<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * NullMapper does not do any work, it is purely intended to allow other mappers to be disabled. As mappers can be
 * referenced both in code and database configuration, it is not easy to remove mappers temporarily or for a single
 * project. So this class can be set as implementation class for mappers defined as services in the dependency injection
 * container.
 * Note that views need to cope with unmapped values.
 */
class NullMapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
    }
}
