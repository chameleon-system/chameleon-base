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
 * MapperRequirements.
 *
 * An instance of this class is passed by the ViewRenderer to the method GetRequirements in an implementation of IViewMapper
 *
 * The mapper has to declare its requirements and return the instance to the ViewRenderer
 */
class MapperRequirements implements IMapperRequirements
{
    /**
     * @var array
     */
    protected $aRequiredSourceObjects = [];

    /**
     * {@inheritdoc}
     */
    public function NeedsSourceObject($key, $sType = null, $sDefault = null, $bOptional = false)
    {
        $this->aRequiredSourceObjects[$key] = [
            'aTypes' => null === $sType ? null : explode('|', $sType),
            'sDefault' => $sDefault,
            'bOptional' => $bOptional, ];
    }

    /**
     * {@inheritdoc}
     */
    public function CanHaveSourceObject($key)
    {
        return isset($this->aRequiredSourceObjects[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceObjectType($key)
    {
        return isset($this->aRequiredSourceObjects[$key]) ? $this->aRequiredSourceObjects[$key]['aTypes'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceObjectDefault($key)
    {
        return isset($this->aRequiredSourceObjects[$key]) ? $this->aRequiredSourceObjects[$key]['sDefault'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceObjectOptional($key)
    {
        return isset($this->aRequiredSourceObjects[$key]) ? $this->aRequiredSourceObjects[$key]['bOptional'] : false;
    }
}
