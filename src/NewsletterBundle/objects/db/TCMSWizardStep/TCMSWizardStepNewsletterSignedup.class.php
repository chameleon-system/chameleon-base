<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSWizardStepNewsletterSignedup extends TdbCmsWizardStep
{
    /**
     * return true if you want to permit caching. if you do, make sure to overwrite the
     * other cache methods as well.
     *
     * @return bool
     */
    protected function AllowCaching()
    {
        return true;
    }
}
