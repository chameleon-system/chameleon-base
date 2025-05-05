<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MapperVisitorRestrictedProxy implements IMapperVisitorRestricted
{
    /** @var IMapperVisitor */
    protected $oComponent;

    /**
     * notice that we pass by reference. we WANT the modification cause by this class to affect the object outside of this class.
     */
    public function __construct(IMapperVisitor $oComponent)
    {
        $this->oComponent = $oComponent;
    }

    /**
     * {@inheritDoc}
     */
    public function SetMappedValue($key, $value)
    {
        $this->oComponent->SetMappedValue($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function GetSourceObject($key)
    {
        return $this->oComponent->GetSourceObject($key);
    }

    public function getSnippetName()
    {
        return $this->oComponent->getSnippetName();
    }

    /**
     * {@inheritdoc}
     */
    public function SetMappedValueFromArray($aData)
    {
        $this->oComponent->SetMappedValueFromArray($aData);
    }

    /**
     * {@inheritdoc}
     */
    public function runMapperChainOn($mapperChainName, array $mapperInputData)
    {
        return $this->oComponent->runMapperChainOn($mapperChainName, $mapperInputData);
    }
}
