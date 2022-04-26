<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\ListManager;

use TCMSListManagerMediaSelector;

class TCMSListManagerMediaManager extends TCMSListManagerMediaSelector
{
    /**
     * {@inheritdoc}
     *
     * @return void
     *
     * @param \TCMSImage $oImage
     */
    public function Init(&$oImage)
    {
        parent::Init($oImage);
        $this->bListCacheEnabled = false;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetRecordClickJavaScriptFunctionName()
    {
        return 'parent.loadDetailPage';
    }
}
