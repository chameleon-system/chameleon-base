<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsCoreParameterContainerParameterDefinition
{
    private $propertyName = null;
    private $type = null;
    private $required = false;

    public function __construct($propertyName, $required, $type = null)
    {
        $this->propertyName = $propertyName;
        $this->type = $type;
        $this->required = $required;
    }

    public function validate($val)
    {
        if (null !== $this->type) {
            $sType = $this->type;
            if (false === ($val instanceof $sType)) {
                throw new TPkgCmsException_Log(
                    "property {$this->propertyName} is not of type ".$sType,
                    array(
                         'property' => $this,
                         'value' => $val,
                    )
                );
            }
        }

        if (true === $this->required && null === $val) {
            throw new TPkgCmsException_Log(
                "property {$this->propertyName} is required ",
                array(
                     'property' => $this,
                     'value' => $val,
                )
            );
        }
    }

    public function getPropertyName()
    {
        return $this->propertyName;
    }
}
