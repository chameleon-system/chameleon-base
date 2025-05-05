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
 * extend AbstractViewMapper instead of implementing IViewMapper.
 */
interface IViewMapper extends IVisitable
{
    /**
     * A mapper has to specify its requirements by providing the passed MapperRequirements instance with the
     * needed information and returning it.
     *
     * Example:
     *
     * $oRequirements->NeedsSourceObject("foo",'stdClass','default-value');
     * $oRequirements->NeedsSourceObject("bar");
     * $oRequirements->NeedsMappedValue("baz");
     *
     * @return void
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements);
}
