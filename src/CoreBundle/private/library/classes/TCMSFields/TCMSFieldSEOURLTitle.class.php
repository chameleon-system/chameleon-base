<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;

/**
 * varchar field with javascript to set the navigation url.
 * /**/
class TCMSFieldSEOURLTitle extends TCMSFieldVarchar
{
    public function GetHTML()
    {
        parent::GetHTML();

        $sourceFieldName = $this->oDefinition->GetFieldtypeConfigKey('sourcefieldname');

        $html = parent::GetHTML();

        if (!empty($sourceFieldName)) {
            $html .= "
        <script type=\"text/javascript\">
        $(document).ready(function() {
          $('#".TGlobal::OutJS($this->name)."').focus(function (){
            GetSEOURLTitle(this.value);
          });

          $('#".TGlobal::OutJS($sourceFieldName)."').on('input', function (){
            GetSEOURLTitle('');
          });
        });

        function GetSEOURLTitle(value) {
          if(value == '') {
            var title = $('#".TGlobal::OutJS($sourceFieldName)."').val();
            GetAjaxCallTransparent('".$this->GenerateAjaxURL(['_fnc' => 'GetFilteredSEOURLTitle', '_fieldName' => $this->name])."&title=' + encodeURIComponent(title), GetSEOURLTitleFinal);
          }
        }

        function GetSEOURLTitleFinal(data,statusText) {
          $('#".TGlobal::OutJS($this->name)."').val(data);
        }
        </script>
        ";
        }

        return $html;
    }

    /**
     * Filters the name field and returns an URL safe version.
     *
     * @return string
     */
    public function GetFilteredSEOURLTitle()
    {
        $URLTitle = '';
        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists('title')) {
            $title = $oGlobal->GetUserData('title');
            $title = str_replace('\n', '-', $title);
            $URLTitle = $this->getUrlNormalizationUtil()->normalizeUrl($title);
        }

        return $URLTitle;
    }

    /**
     * sets methods that are allowed to be called via URL (ajax call).
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'GetFilteredSEOURLTitle';
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     */
    public function ConvertPostDataToSQL()
    {
        return $this->getUrlNormalizationUtil()->normalizeUrl($this->data);
    }

    /**
     * returns true if field data is not empty
     * overwrite this method for mlt and property fields.
     *
     * @return bool
     */
    public function HasContent()
    {
        $bHasContent = false;
        if ('' != trim($this->data)) {
            $bHasContent = true;
        } else {
            if (!empty($this->oDefinition->fieldFieldtypeConfig)) {
                $aConfigValue = explode('=', $this->oDefinition->fieldFieldtypeConfig);
                if (count($aConfigValue) > 1) {
                    if (array_key_exists($aConfigValue[1], $this->oTableRow->sqlData) && !empty($this->oTableRow->sqlData[$aConfigValue[1]])) {
                        $bHasContent = true;
                    }
                }
            }
        }

        return $bHasContent;
    }

    /**
     * @return UrlNormalizationUtil
     */
    private function getUrlNormalizationUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }
}
