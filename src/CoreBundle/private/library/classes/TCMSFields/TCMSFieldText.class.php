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

        return $this->renderTextArea($this->data, false);
    }

    private function renderTextArea(string $data, bool $readOnly): string
    {
        // make usage of the textarea resizer and save scrolling by setting variable textarea size based on field content

        if (empty($data)) {
            $iTextareaSize = 30;
        } elseif (strlen($data) <= 400) {
            $iTextareaSize = 50;
        } elseif (strlen($data) <= 1000) {
            $iTextareaSize = 100;
        } else {
            $count = count(explode("\n", $data));
            $iTextareaSize = $count * 14 + 50;
            if ($iTextareaSize > 200) {
                $iTextareaSize = 200;
            }
        }

        $cssWidth = '';
        if ('100%' !== $this->fieldCSSwidth) {
            $cssWidth = 'width: '.$this->fieldCSSwidth;
        }

        $html = '';
        $html .= sprintf(
            '<textarea id="%s" name="%s" class="fieldtext form-control form-control-sm resizable" width="%s" style="%s" %s>',
            TGlobal::OutHTML($this->name),
            TGlobal::OutHTML($this->name),
            $this->fieldWidth,
            'height: '.$iTextareaSize.'px;'.$cssWidth,
            true === $readOnly ? 'readonly' : ''
        );
        $html .= TGlobal::OutHTML($data);
        $html .= '</textarea>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        // todo: remove need to call _GetFieldWidth here
        // instead of just returning the field width, the method sets some properties on $this - which are needed
        // by renderTextArea.
        $this->_GetFieldWidth();

        return $this->renderTextArea($this->data, true);
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
