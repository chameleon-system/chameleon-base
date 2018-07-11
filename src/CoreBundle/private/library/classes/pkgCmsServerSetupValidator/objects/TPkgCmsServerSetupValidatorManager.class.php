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
 * @deprecated since 6.2.0 - use the ChameleonSystemSanityCheckBundle instead.
 */
class TPkgCmsServerSetupValidatorManager
{
    /**
     * @param $validatorList
     *
     * @return array of IPkgCmsServerSetupValidatorMessage
     */
    public function runValidatorList($validatorList)
    {
        $aMessages = array();
        foreach ($validatorList as $class) {
            $aMessages[$class] = $this->runValidator($class);
        }

        return $aMessages;
    }

    /**
     * @param $className
     *
     * @return IPkgCmsServerSetupValidatorMessage
     */
    public function runValidator($className)
    {
        /** @var IPkgCmsServerSetupValidator $validator */
        $validator = new $className();

        return $validator->run();
    }

    /**
     * runs all checks, but returns only messages that exceed the specified level.
     *
     * @return array of IPkgCmsServerSetupValidatorMessage
     */
    public function runSilentCheck($allowedLevel)
    {
        $validatorList = $this->getValidatorList();
        $aMessages = $this->runValidatorList($validatorList);
        $aMessagesExceedingLimit = array();
        foreach ($aMessages as $sClass => $oMessage) {
            /** @var IPkgCmsServerSetupValidatorMessage $oMessage */
            if ($oMessage->getMessageType() > $allowedLevel) {
                $aMessagesExceedingLimit[$sClass] = $oMessage;
            }
        }

        return $aMessagesExceedingLimit;
    }

    protected function getValidatorList()
    {
        return array('TPkgCmsServerSetupValidator_PHPVersion');
    }
}
