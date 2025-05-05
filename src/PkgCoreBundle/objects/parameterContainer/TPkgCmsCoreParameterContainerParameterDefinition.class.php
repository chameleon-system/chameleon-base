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
    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var class-string|null
     */
    private $type;

    /**
     * @var bool
     */
    private $required = false;

    /**
     * @param string $propertyName
     * @param bool $required
     * @param class-string|null $type
     */
    public function __construct($propertyName, $required, $type = null)
    {
        $this->propertyName = $propertyName;
        $this->type = $type;
        $this->required = $required;
    }

    /**
     * @param object|null $val - Instance of $this->type or null
     *
     * @return void
     */
    public function validate($val)
    {
        if (null !== $this->type) {
            $sType = $this->type;
            if (false === ($val instanceof $sType)) {
                throw new TPkgCmsException_Log(
                    "property {$this->propertyName} is not of type ".$sType,
                    [
                         'property' => $this,
                         'value' => $val,
                    ]
                );
            }
        }

        if (true === $this->required && null === $val) {
            throw new TPkgCmsException_Log(
                "property {$this->propertyName} is required ",
                [
                     'property' => $this,
                     'value' => $val,
                ]
            );
        }
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }
}
