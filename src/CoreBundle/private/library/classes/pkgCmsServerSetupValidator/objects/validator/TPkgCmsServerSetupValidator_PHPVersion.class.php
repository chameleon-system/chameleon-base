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
class TPkgCmsServerSetupValidator_PHPVersion implements IPkgCmsServerSetupValidator
{
    /**
     * @return IPkgCmsServerSetupValidatorMessage
     */
    public function run()
    {
        $sRequiredVersion = '5.3.3';
        if (version_compare(PHP_VERSION, $sRequiredVersion) >= 0) {
            $oMsg = new TPkgCmsServerSetupValidatorMessage(
                IPkgCmsServerSetupValidatorMessage::MESSAGE_TYPE_VALID,
                PHP_VERSION." >= {$sRequiredVersion}");
        } else {
            $oMsg = new TPkgCmsServerSetupValidatorMessage(
                IPkgCmsServerSetupValidatorMessage::MESSAGE_TYPE_ERROR,
                "you need at least php {$sRequiredVersion} (you have ".PHP_VERSION.')');
        }

        return $oMsg;
    }
}
