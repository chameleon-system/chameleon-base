<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DataModelParts;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

/**
 * picks a node from a tree.
/**/
class TCMSFieldTreeNode extends TCMSField
{
    public function GetHTML()
    {
        $path = $this->_GetTreePath();
        $html = '<input type="hidden" id="'.TGlobalBase::OutHTML($this->name).'" name="'.TGlobalBase::OutHTML($this->name).'" value="'.TGlobalBase::OutHTML($this->data).'" />';
        $html .= '<div id="'.TGlobalBase::OutHTML($this->name).'_path">'.$path.'</div>';
        $html .= '<div class="cleardiv">&nbsp;</div>';
        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_tree_node.assign_node'), 'javascript:'.$this->_GetOpenWindowJS().';', 'fas fa-check');
        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.action.reset'), "javascript:ResetTreeNodeSelection('".TGlobalBase::OutHTML($this->name)."');", 'fas fa-undo');

        return $html;
    }

    public function getDoctrineDataModelParts(string $namespace): ?DataModelParts
    {
        $type = sprintf('%s\%s', $namespace, $this->snakeToCamelCase('cms_tree', false));

        $data = $this->getDoctrineDataModelViewData(['type' => $type]);

        $rendererProperty = $this->getDoctrineRenderer('model/default.property.php.twig', $data);
        $rendererMethod = $this->getDoctrineRenderer('model/default.methods.php.twig', $data);

        return new DataModelParts($rendererProperty->render(), $rendererMethod->render(), $data['allowDefaultValue']);
    }


    public function GetReadOnly()
    {
        $html = $this->_GetHiddenField();
        if (!empty($this->data)) {
            $oNode = new TdbCmsTree();
            $oNode->Load($this->data);
            $html .= TGlobal::OutHTML($oNode->fieldPathcache);
        }

        return $html;
    }

    public function _GetTreePath()
    {
        $path = '';
        if (!empty($this->data)) {
            $oTreeNode = new TdbCmsTree();
            $oTreeNode->Load($this->data);
            $path = $oTreeNode->GetTreeNodePathAsBackendHTML();
        }

        return $path;
    }

    public function _GetOpenWindowJS()
    {
        $urlParts = [
            'pagedef' => 'navigationTreeSingleSelect',
            'fieldName' => $this->name,
            'id' => $this->data,
            'currentRecordId' => $this->recordId,
        ];

        if (null !== $this->sTableName && '' !== $this->sTableName) {
            $urlParts['tableName'] = $this->sTableName;
        }

        if (true === \array_key_exists('cms_portal_id', $this->oTableRow->sqlData)) {
            $urlParts['portalID'] = $this->oTableRow->sqlData['cms_portal_id'];
        }

        $url = $this->getUrlUtil()->getArrayAsUrl($urlParts, PATH_CMS_CONTROLLER.'?', '&');

        return "CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."')";
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        return [];
    }

    public function RenderFieldMethodsString()
    {
        $aMethodData = $this->GetFieldMethodBaseDataArray();

        $aMethodData['sMethodName'] = $this->GetFieldMethodName('PageURL');
        $aMethodData['aParameters'] = array(
            'bForceDomain' => array(
                'sType' => 'boolean',
                'description' => 'force include the portal domain (generate an absolute link)',
                'default' => 'false',
            ),
            'forcePageLanguage' => array(
                'sType' => 'boolean',
                'description' => 'force page language (if true, the language of the page is used; if false, the currently active language is used)',
                'default' => 'false',
            ),
        );
        $aMethodData['sReturnType'] = 'string|null';

        $oViewParser = new TViewParser();
        /** @var $oViewParser TViewParser */
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $oViewParser->AddVarArray($aMethodData);

        $sMethodCode = $oViewParser->RenderObjectView('geturl', 'TCMSFields/TCMSFieldTreeNode');
        $oViewParser->AddVar('sMethodCode', $sMethodCode);

        return $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');
    }

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}
