<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MapperRequirementsRestrictedProxy implements IMapperRequirementsRestricted
{
    /**
     * @var IMapperRequirements
     */
    private $oRequirements;

    /**
     * notice that we pass by reference. we WANT the modification cause by this class to affect the object outside of this class.
     */
    public function __construct(IMapperRequirements $oRequirements)
    {
        $this->oRequirements = $oRequirements;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnStatement - void functions should not return
     */
    public function NeedsSourceObject($key, $sType = null, $sDefault = null, $bOptional = false)
    {
        return $this->oRequirements->NeedsSourceObject($key, $sType, $sDefault, $bOptional);
    }
}
