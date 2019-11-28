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
            $count = count(explode("\n", $this->data));
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
            true === $this->bReadOnlyMode ? 'readonly' : ''
        );
        $html .= TGlobal::OutHTML($this->data);
        $html .= '</textarea>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        $this->bReadOnlyMode = true;

        return $this->GetHTML();
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
