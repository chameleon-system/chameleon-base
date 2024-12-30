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
 * provides some standard render functions for cms containers and buttons.
 */
class TCMSRender
{
    /**
     * Renders a bootstrap 4 CSS button.
     *
     * @example TCMSRender::DrawButton($buttonTitle, $mediaManagerUrl, 'far fa-image', null, null, null, null, '_blank');
     */
    public static function DrawButton(?string $title = null, ?string $link = null, ?string $icon = null, ?string $linkClass = null, ?string $onMouseOver = null, ?string $id = null, ?string $onclick = null, ?string $sTarget = null): string
    {
        $sTemplate = 'singleButton';

        // migrate BS3 to BS4 float class name
        if ('pull-left' === $linkClass) {
            $linkClass = 'float-left';
        }

        if (is_null($onclick) && !is_null($link)) {
            if ('javascript:' == substr($link, 0, 11)) {
                $onclick = substr($link, 11);
                $sTarget = null;
            } else {
                // real link
                $sTemplate = 'linkButton';
            }
        } else {
            $onclick = str_replace('this.href', 'document.location.href', $onclick);
            $sTarget = null;
        }

        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->AddSourceObject('sTitle', $title);
        $oViewRenderer->AddSourceObject('sCSSClass', $linkClass);
        $oViewRenderer->AddSourceObject('sOnClick', $onclick);

        if (strpos($icon, '/') > 0 || strpos($icon, '.') > 0) {
            $oViewRenderer->AddSourceObject('sIconURL', $icon);
        } else {
            $oViewRenderer->AddSourceObject('sIcon', $icon);
        }
        $oViewRenderer->AddSourceObject('sButtonStyle', 'btn-secondary');
        $oViewRenderer->AddSourceObject('onMouseOver', $onMouseOver);
        $oViewRenderer->AddSourceObject('cssID', $id);
        $oViewRenderer->AddSourceObject('link', $link);
        $oViewRenderer->AddSourceObject('target', $sTarget);

        return $oViewRenderer->Render('MTTableEditor/'.$sTemplate.'.html.twig', null, false);
    }
}
