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
 * a long text field.
/**/
class TCMSFieldText extends TCMSField
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldText';

    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        parent::GetHTML();

        // make usage of the textarea resizer and save scrolling by setting variable textarea size based on field content

        if (empty($this->data)) {
            $iTextareaSize = 30;
        } elseif (strlen($this->data) <= 400) {
            $iTextareaSize = 50;
        } elseif (strlen($this->data) <= 1000) {
            $iTextareaSize = 100;
        } else {
            $iTextareaSize = null;
        }

        if (!is_null($iTextareaSize) && !empty($this->data)) {
            $count = count(explode("\n", $this->data));
            $iTextareaSize = $iTextareaSize + ($count * 14);
            if ($iTextareaSize > 200) {
                $iTextareaSize = 200;
            }
        }

        $cssWidth = '';
        if ('100%' !== $this->fieldCSSwidth) {
            $cssWidth = 'width: '.$this->fieldCSSwidth;
        }

        $html = '';
        $html .= '<textarea id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name)."\" class=\"fieldtext form-control form-control-sm resizable\" width=\"{$this->fieldWidth}\" style=\"".$cssWidth;
        if (!is_null($iTextareaSize)) {
            $html .= ' ;height: '.$iTextareaSize.'px';
        }
        $html .= '">';
        $html .= TGlobal::OutHTML($this->data);
        $html .= '</textarea>';

        return $html;
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
