<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\SanityCheck;

use ChameleonSystem\SanityCheck\Check\AbstractCheck;
use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;
use TCMSConfig;

/**
 * Checks if ImageMagick is installed.
 */
class ImageMagickCheck extends AbstractCheck
{
    /**
     * @return array(CheckOutcome)
     */
    public function performCheck()
    {
        $retValue = array();

        if (true === DISABLE_IMAGEMAGICK) {
            $retValue[] = new CheckOutcome('check.imagemagick.inactive', array(), CheckOutcome::OK);
        } else {
            $config = TCMSConfig::GetInstance();
            if (false === $config->GetImageMagickVersion()) {
                $retValue[] = new CheckOutcome('check.imagemagick.notfound', array(), $this->getLevel());
            } else {
                $retValue[] = new CheckOutcome('check.imagemagick.found', array(), CheckOutcome::OK);
            }
        }

        return $retValue;
    }
}
