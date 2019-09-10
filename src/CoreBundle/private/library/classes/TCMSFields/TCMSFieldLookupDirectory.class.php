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
 * lists the entries for a directory (not recursive at the moment)
 * - needs a directory parameter in the field config like:.
 *
 * @example directory=%PATH_CUSTOMER_FRAMEWORK%/modules/
 * - you can use any constant from advanced_config.inc.php
 * - optional you can set a comma seperated list of file extensions that you want to list via param: "filetypes=jpg,png,gif"
 * - if no filetypes are set it will list only directories
 *
/**/
class TCMSFieldLookupDirectory extends TCMSField
{
    public function GetHTML()
    {
        $this->GetOptions();

        $html = '<select name="'.TGlobal::OutHTML($this->name).'" id="'.TGlobal::OutHTML($this->name)."\" class=\"form-control form-control-sm\">\n";
        $chooseMessage = TGlobal::Translate('chameleon_system_core.form.select_box_nothing_selected');
        $html .= '<option value="">'.TGlobal::OutHTML($chooseMessage)."</option>\n";
        $html .= '<option value="">'.TGlobal::OutHTML('-------------------------------------------')."</option>\n";

        foreach ($this->options as $key => $value) {
            $selected = '';
            if ($this->data == $key) {
                $selected = 'selected';
            }
            $html .= '<option value="'.TGlobal::OutHTML($key)."\" {$selected}>".TGlobal::OutHTML($value)."</option>\n";
        }
        $html .= "</select>\n";

        return $html;
    }

    /**
     * load the directory entries
     * see class description for needed field config params.
     */
    public function GetOptions()
    {
        $this->options = array();

        // get fieldtypes from field config
        $aFileTypes = array();
        $sFileTypes = $this->oDefinition->GetFieldtypeConfigKey('filetypes');
        if (!empty($sFileTypes)) {
            if (stristr($sFileTypes, ',')) {
                $aFileTypes = explode(',', $sFileTypes);
            } else {
                $aFileTypes[] = $sFileTypes;
            }
        }

        // get directory from field config
        $sDirectory = $this->oDefinition->GetFieldtypeConfigKey('directory');
        $sDirectory = preg_replace_callback('%[A-Z_]+%', 'GetConstant', $sDirectory, 1);
        $sDirectory = str_replace('%', '', $sDirectory);

        if (!empty($sDirectory) && is_dir($sDirectory)) {
            $aScanlisting = scandir($sDirectory);
            if (is_array($aScanlisting) && count($aScanlisting) > 0) {
                foreach ($aScanlisting as $key => $file) {
                    if ('.' != $file && '..' != $file && '.' != substr($file, 0, 1)) {
                        // we need to check for fileExtensions
                        if (count($aFileTypes) > 0) {
                            if (is_file($sDirectory.'/'.$file)) {
                                $extension = mb_substr($file, (mb_strrpos($file, '.') ? mb_strrpos($file, '.') + 1 : mb_strlen($file)), mb_strlen($file));
                                $extension = strtolower($extension);
                                if (in_array($extension, $aFileTypes)) {
                                    $formattedFileName = $this->FormatFileName($file);
                                    $this->options[$formattedFileName] = $formattedFileName;
                                }
                            }
                        } else { // list directories only
                            if (is_dir($sDirectory.'/'.$file)) {
                                $this->options[$file] = $this->FormatFileName($file);
                            }
                        }
                    }
                }
            }
        }
    }

    public function GetHTMLExport()
    {
        $this->GetOptions();
        if (array_key_exists($this->data, $this->options)) {
            return $this->options[$this->data];
        } else {
            return 'not set';
        }
    }

    protected function GetFieldWriterData()
    {
        $aData = parent::GetFieldWriterData();

        $value = \str_replace("'", "\'", $this->data);
        $aData['sFieldDefaultValue'] = "'$value'";

        return $aData;
    }

    public function GetReadOnly()
    {
        $this->GetOptions();
        if (array_key_exists($this->data, $this->options)) {
            return $this->_GetHiddenField().'<div class="form-content-simple">'.TGlobal::OutHTML($this->options[$this->data]).'</div>';
        } else {
            return $this->_GetHiddenField();
        }
    }

    /**
     * return the new charset latin1 so that we get more memory
     * size for a record.
     *
     * @return string
     */
    public function _GetSQLCharset()
    {
        return ' CHARACTER SET latin1 COLLATE latin1_general_ci';
    }

    /**
     * hook to format the filename (e.g. strip file extension).
     *
     * @param string $fileName
     *
     * @return string
     */
    protected function FormatFileName($fileName)
    {
        $fileName = str_replace('.layout.php', '', $fileName);
        $fileName = str_replace('.view.php', '', $fileName);

        return $fileName;
    }

    protected function GetFieldMethodName($sMethodPostString = '')
    {
        $sPrefix = TCMSTableToClass::PREFIX_PROPERTY;
        $sPrefix = ucfirst($sPrefix);

        return 'Get'.$sPrefix.TCMSTableToClass::ConvertToClassString($this->name).$sMethodPostString;
    }

    public function RenderFieldMethodsString()
    {
        return '';
    }

    /**
     * render any methods for the auto list class for this field.
     *
     * @return string
     */
    public function RenderFieldListMethodsString()
    {
        return '';
    }
}

function GetConstant($aMatches)
{
    $constant = '';
    if (is_array($aMatches)) {
        $constant = constant($aMatches[0]);
    }

    return $constant;
}
