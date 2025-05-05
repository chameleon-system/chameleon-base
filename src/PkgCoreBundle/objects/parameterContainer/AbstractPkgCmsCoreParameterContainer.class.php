<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

abstract class AbstractPkgCmsCoreParameterContainer
{
    /**
     * @var TPkgCmsCoreParameterContainerParameterDefinition[]
     */
    private $aRequirements = [];

    /**
     * @var bool
     */
    private $requirementsChecked = false;

    /**
     * use $this->addRequirement to add the requirements of the container.
     *
     * @return void
     */
    abstract protected function defineRequirements();

    /**
     * @return $this
     */
    final protected function addRequirement(TPkgCmsCoreParameterContainerParameterDefinition $oRequirement)
    {
        $this->aRequirements[] = $oRequirement;

        return $this;
    }

    /**
     * @return void
     *
     * @throws TPkgCmsException_Log
     */
    final public function checkRequirements()
    {
        $this->defineRequirements();
        reset($this->aRequirements);
        foreach ($this->aRequirements as $oRequirement) {
            /** @var TPkgCmsCoreParameterContainerParameterDefinition $oRequirement */
            $sGetter = 'get'.ucfirst($oRequirement->getPropertyName());
            $sVal = null;
            if (method_exists($this, $sGetter)) {
                $sVal = $this->$sGetter();
            } else {
                throw new TPkgCmsException_Log("getter [{$sGetter}] for [".$oRequirement->getPropertyName(
                ).'] not defined', ['this' => $this]);
            }
            $oRequirement->validate($sVal);
        }
        $this->requirementsChecked = true;
    }

    /**
     * @param string $sPropertyName
     *
     * @throws TPkgCmsException_Log
     */
    protected function get($sPropertyName)
    {
        if (false === $this->requirementsChecked) {
            $this->checkRequirements();
        }

        return $this->$sPropertyName;
    }
}
