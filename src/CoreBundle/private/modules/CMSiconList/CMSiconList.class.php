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
 * @deprecated since 7.1.6
 */
class CMSiconList extends TCMSModelBase
{
    public $iconName = null;
    public $fieldName = null;

    public function Execute()
    {
        $this->data = parent::Execute();
        $fieldName = $this->global->GetUserData('fieldName');

        $html = '';

        $iconPath = $this->GetIconPath();
        $path = PATH_USER_CMS_PUBLIC.'/blackbox/'.$this->GetIconPath();
        $dir = opendir($path);
        while ($file = readdir($dir)) {
            if ('.' != $file && '..' != $file && ('gif' == substr($file, -3, 3) || 'png' == substr($file, -3, 3))) {
                $fileArray[] = $file;
            }
        }

        asort($fileArray);

        $iconBoxClass = 'iconBox';
        if ('CMSsmallIconlist' == $this->global->GetUserData('pagedef')) {
            $iconBoxClass = 'smallIconBox';
        }

        foreach ($fileArray as $key => $file) {
            $filename = substr($file, 0, -4);
            $filename = str_replace('_', ' ', $filename);

            $html .= "<div class=\"{$iconBoxClass}\" onMouseOver=\"this.style.backgroundColor='#F2F8FC';\" onMouseOut=\"this.style.backgroundColor='transparent';\" onClick=\"chooseIcon('{$fieldName}','{$iconPath}','{$file}');parent.CloseModalIFrameDialog();\">
         <div class=\"icon\"><img src=\"".TGlobal::GetStaticURLToWebLib($iconPath.$file)."\" style=\"padding-top: 3px; padding-bottom: 2px;\" alt=\"\" /></div>
         <div class=\"iconName\">{$filename}</div>
         </div>";
        }

        $this->data['iconList'] = $html;

        return $this->data;
    }

    public function GetIconPath()
    {
        $path = $this->aModuleConfig['iconPath'];

        return $path;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/modules/iconlist.css" rel="stylesheet" type="text/css" />';

        return $aIncludes;
    }
}
