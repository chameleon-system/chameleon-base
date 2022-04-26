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
     * @param string   $sTitle
     * @param string   $boxIcon - not used anymore
     * @param int|null $width
     *
     * @deprecated since 6.3.0 - only used for deprecated classic main menu
     */
    public static function DrawBoxHeader($sTitle, $boxIcon, $width = null)
    {
        if (!is_null($width)) {
            $width = 'style="width:'.htmlspecialchars($width).'"';
        }
        echo "<div {$width} class=\"card mb-3\">\n";
        echo '      <div class="card-header"><h5 class="card-title mb-0">'.TGlobal::OutHTML($sTitle)."</h5></div>\n";
        echo "      <div class=\"card-body p-0\">\n";
    }

    /**
     * @deprecated since 6.3.0 - only used for deprecated classic main menu
     */
    public static function DrawBoxFooter()
    {
        echo "    </div>\n";
        echo "</div>\n";
    }

    /**
     * renders a bootstrap 4 CSS button.
     *
     * sample usage with thickbox and javascript mouseover event:
     * TCMSRender::DrawButton('Webseite anzeigen',"",URL_CMS."/images/icons/icon_world.gif",'thickbox','document.location.href=document.getElementById(\''.TGlobal::OutHTML($this->name).'\').value + \'?TB_iframe=true&height=600&width=800\'');
     *
     * @param string $title
     * @param string $link
     * @param string $icon
     * @param string $linkClass
     * @param string $onMouseOver
     * @param string $id
     * @param string $onclick
     * @param string|null $sTarget     was cleared if link is javascript button
     *
     * @return string
     */
    public static function DrawButton($title = null, $link = null, $icon = null, $linkClass = null, $onMouseOver = null, $id = null, $onclick = null, $sTarget = null)
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
