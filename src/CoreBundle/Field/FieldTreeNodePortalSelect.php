<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Field;

use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Allows the selection of only the portal root tree nodes (level 1 of tree).
 *
 * {@inheritdoc}
 */
class FieldTreeNodePortalSelect extends \TCMSFieldTreeNode
{
    public function GetHTML()
    {
        $translator = $this->getTranslator();
        $path = $this->_GetTreePath();
        $html = '<input type="hidden" id="'.\TGlobal::OutHTML($this->name).'" name="'.\TGlobal::OutHTML($this->name).'" value="'.\TGlobal::OutHTML($this->data).'" />';
        $html .= '<div id="'.\TGlobal::OutHTML($this->name).'_path">'.$path.'</div>';
        $html .= '<div class="cleardiv">&nbsp;</div>';

        $html .= \TCMSRender::DrawButton(
            $translator->trans('chameleon_system_core.field_tree_node.assign_node'),
            "javascript:loadTreeNodePortalSelection('".\TGlobal::OutJS($this->name)."');",
            'fas fa-check');
        $html .= \TCMSRender::DrawButton(
            $translator->trans('chameleon_system_core.action.reset'),
            "javascript:ResetTreeNodeSelection('".\TGlobal::OutJS($this->name)."');",
            'fas fa-undo');

        return $html;
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('chameleon_system_core.translator');
    }
}
