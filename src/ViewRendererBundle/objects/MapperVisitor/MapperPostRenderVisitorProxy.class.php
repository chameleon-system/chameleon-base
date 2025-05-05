<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MapperPostRenderVisitorProxy extends MapperVisitorPayloadProxy implements IMapperPostRenderVisitor
{
    /**
     * {@inheritdoc}
     */
    public function SetMappedValue($key, $value)
    {
        $this->oComponent->SetMappedValue($key, $value);
    }

    /**
     * @param string $key
     *
     * @throws MapperException
     */
    public function GetSourceObject($key)
    {
        if (true === $this->oComponent->hasSourceObject($key)) {
            return $this->oComponent->GetSourceObject($key, true);
        }

        return new MapperVirtualSourceObject();
    }
}
