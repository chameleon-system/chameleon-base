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
use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineTransformableInterface;

/**
 * varchar text field (max 255 chars).
 *
 * {@inheritdoc}
 */
class TCMSFieldVarchar extends TCMSField implements DoctrineTransformableInterface
{
    /**
     * @var string
     */
    protected $sFieldHTMLInputType = 'text';

    /**
     * @var string|null
     */
    protected $sFieldHTMLPlaceholder;

    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldVarchar';

    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $parameters = [
            'source' => get_class($this),
            'type' => 'string',
            'docCommentType' => 'string',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->name),
            'defaultValue' => sprintf("'%s'", addslashes($this->oDefinition->sqlData['field_default_value'])),
            'allowDefaultValue' => true,
            'getterName' => 'get'. $this->snakeToCamelCase($this->name, false),
            'setterName' => 'set'. $this->snakeToCamelCase($this->name, false),
        ];
        $propertyCode = $this->getDoctrineRenderer('model/default.property.php.twig', $parameters)->render();
        $methodCode = $this->getDoctrineRenderer('model/default.methods.php.twig', $parameters)->render();

        return new DataModelParts(
            $propertyCode,
            $methodCode,
            $this->getDoctrineDataModelXml($namespace, $tableNamespaceMapping),
            [],
            true
        );
    }

    protected function getDoctrineDataModelXml(string $namespace, $tableNamespaceMapping): string
    {
        return $this->getDoctrineRenderer('mapping/string.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'type' => 'string',
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
            'default' => $this->oDefinition->sqlData['field_default_value'],
            'length' => '' === $this->oDefinition->sqlData['length_set'] ? 255 : $this->oDefinition->sqlData['length_set'],
        ])->render();
    }

    public function getDoctrineDataModelImports(string $namespace): array
    {
        return [];
    }


    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        parent::GetHTML();

        $count = $this->fieldWidth - mb_strlen($this->_GetHTMLValue());

        $html = '<div class="input-group input-group-sm"><input';
        foreach ($attributes = $this->getInputFieldAttributes() as $key => $value) {
            $html .= sprintf(' %s="%s"', TGlobal::OutHTML($key), TGlobal::OutHTML($value));
        }
        $html .= sprintf(' value="%s"', $this->_GetHTMLValue());
        $html .= ' /><span class="input-group-append"><span class="input-group-text charCounter alert alert-warning mb-0">
              <span id="'.TGlobal::OutHTML($this->name).'Count" class="mr-1">'.$count."</span> <i class=\"fas fa-text-width\"></i></span>
            </span>
        </div>
      <script type=\"text/javascript\">
  			$(document).ready(function() {
  			  $('#".TGlobal::OutJS($this->name)."').countRemainingChars('".TGlobal::OutJS(
                $this->fieldWidth
            )."','#".TGlobal::OutJS($this->name)."Count');
  			});
      </script>";

        return $html;
    }

    public function _GetHTMLValue()
    {
        $html = parent::_GetHTMLValue();
        $html = TGlobal::OutHTML($html);

        return $html;
    }

    /**
     * Returns attributes that are added to the HTML input field.
     *
     * @return array
     */
    protected function getInputFieldAttributes()
    {
        $attributes = [
            'class' => 'form-control form-control-sm',
            'type' => $this->sFieldHTMLInputType,
            'id' => $this->name,
            'name' => $this->name,
            'maxlength' => $this->fieldWidth,
        ];

        if ('100%' !== $this->fieldCSSwidth) {
            $attributes['style'] = 'width: '.$this->fieldCSSwidth;
        }

        if (null !== $this->sFieldHTMLPlaceholder) {
            $attributes['placeholder'] = $this->sFieldHTMLPlaceholder;
        }

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = parent::GetCMSHtmlHeadIncludes();
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/countRemainingChars/jquery.countremainingchars.js').'" type="text/javascript"></script>';

        return $aIncludes;
    }

    /**
     * {@inheritdoc}
     */
    public function HasContent()
    {
        $bHasContent = false;
        if ('' != trim($this->data)) {
            $bHasContent = true;
        }

        return $bHasContent;
    }
}
