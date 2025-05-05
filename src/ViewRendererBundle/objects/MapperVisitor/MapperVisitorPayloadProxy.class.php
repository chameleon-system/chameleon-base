<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MapperVisitorPayloadProxy implements IMapperPayload
{
    /**
     * @var IMapperVisitor
     */
    protected $oComponent;

    /**
     * notice that we pass by reference. we WANT the modification cause by this class to affect the object outside of this class.
     */
    public function __construct(IMapperVisitor $oComponent)
    {
        $this->oComponent = $oComponent;
    }

    /**
     * {@inheritdoc}
     */
    public function GetSourceObject($key)
    {
        if (true === ($this->oComponent->hasSourceObject($key) || $this->oComponent->hasSetRequirementWithDefaultValue($key))) {
            return $this->oComponent->GetSourceObject($key, false);
        }

        return new MapperVirtualSourceObject();
    }

    /**
     * {@inheritdoc}
     */
    public function isVirtualSourceObject($key)
    {
        return false === $this->oComponent->hasSourceObject($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getSnippetName()
    {
        return $this->oComponent->getSnippetName();
    }
}
