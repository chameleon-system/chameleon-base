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
 * varchar text field (max 255 chars).
 *
 * {@inheritdoc}
 */
class TCMSFieldVarchar extends TCMSField
{
    /**
     * @var string
     */
    protected $sFieldHTMLInputType = 'text';

    /**
     * @var null|string
     */
    protected $sFieldHTMLPlaceholder;

    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldVarchar';

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
        $html .= ' /><span class="input-group-append"><span class="input-group-text charCounter"><span id="'.TGlobal::OutHTML($this->name).'Count">'.$count."</span> <i class=\"glyphicon glyphicon-text-width\"></i></span></span>
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
            'class' => 'form-control input-sm',
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
