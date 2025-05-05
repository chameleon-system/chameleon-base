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
 * WYSIWYG Text field.
 * /**/
class TCMSFieldWYSIWYGLight extends TCMSFieldWYSIWYG
{
    protected function getToolbar()
    {
        $aToolbar = parent::getToolbar();
        $aItemsToRemove = [
            'Undo', 'Redo', 'Find', 'Replace', 'BulletedList', 'Outdent', 'Indent',
            'Blockquote', 'CreateDiv', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock',
            'Anchor', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe',
        ];
        $aToolbar = $this->removeItemListFromToolbar($aToolbar, $aItemsToRemove);
        $aToolbar = $this->removeSectionFromToolbar($aToolbar, 'styles');

        return $aToolbar;
    }
}
