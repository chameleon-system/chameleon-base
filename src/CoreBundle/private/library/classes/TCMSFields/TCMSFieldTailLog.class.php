<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldTailLog extends TCMSField
{
    protected $methodCallAllowed = array('getLogData');

    protected $bLogNotReadable = false;

    public function getDoctrineDataModelAttribute(string $namespace): ?string
    {
        return null;
    }
    public function GetHTML()
    {
        parent::GetHTML();

        $html = '<div id="'.TGlobal::OutHTML($this->name).'_lastReloaded" style="border-bottom: 1px solid #362B36; margin-bottom: 10px;">
        <strong>
        '.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.field_tail_log.last_update')).': <span>'.date('H:i:s').'</span>
        </strong>
        </div>';
        $html .= '<div style="overflow: auto; height: 350px; border: 1px solid #A9C4E7; padding: 5px;" id="'.TGlobal::OutHTML($this->name).'">';

        $html .= $this->getLogData();

        $html .= '</div>';

        $sReloadDisabled = $this->getFieldTypeConfigKey('disableReload');
        if ('1' != $sReloadDisabled && 'true' != $sReloadDisabled) {
            $html .= "
        <script type=\"text/javascript\">
        $(document).ready(function() {
            setInterval(function() {
                GetAjaxCallTransparent('".$this->GenerateAjaxURL(array('_fnc' => 'getLogData', '_fieldName' => $this->name))."', reloadLog);
            }, 10000);
        });

        function reloadLog(data,statusText) {
            if(data != '') {
                $('#".TGlobal::OutHTML($this->name)."').html(data);
                var currentDate = new Date();
                var hours = currentDate.getHours()
                if(hours < 10) hours = '0'+hours.toString();
                var minutes = currentDate.getMinutes();
                if(minutes < 10) minutes = '0'+minutes.toString();
                var seconds = currentDate.getSeconds();
                if(seconds < 10) seconds = '0'+seconds.toString();

                dateString = hours+':'+minutes+':'+seconds;
                $('#".TGlobal::OutHTML($this->name)."_lastReloaded span').html(dateString);
            }
        }
        </script>
        ";
        }

        return $html;
    }

    /**
     * @see http://stackoverflow.com/questions/2961618/how-to-read-only-5-last-line-of-the-txt-file
     *
     * @return string
     */
    public function getLogData()
    {
        $html = '';
        $maxLineLength = 1000;
        $sPath = $this->getFieldTypeConfigKey('logPath');

        $securedLogFilePath = '';
        if (!empty($sPath)) {
            $logFilePath = realpath(PATH_CMS_CUSTOMER_DATA.'/'.$sPath);
            if (empty($logFilePath)) {
                $this->bLogNotReadable = true;
                $html = TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_tail_log.error_unable_to_read_log').': '.$logFilePath);

                return $html;
            }
            $securedLogFilePath = TGlobal::ProtectedPath($logFilePath, '.log');
        }

        $iNumLines = $this->getFieldTypeConfigKey('numLines');
        if (is_null($iNumLines) || empty($iNumLines)) {
            $iNumLines = 50;
        }

        $aLogLines = array();
        if (!empty($securedLogFilePath) && is_file($securedLogFilePath)) {
            if ($fp = fopen($securedLogFilePath, 'r')) {
                fseek($fp, -($iNumLines * $maxLineLength), SEEK_END); // move pointer to end of file

                $lines = array();
                while (!feof($fp)) {
                    $lines[] = fgets($fp);
                }

                $c = count($lines);
                $i = $c >= $iNumLines ? $c - $iNumLines : 0;
                for (; $i < $c; ++$i) {
                    $aLogLines[] = $lines[$i];
                }

                $aLogLines = array_reverse($aLogLines);

                $html .= implode('<br />', $aLogLines);
            }
        } else {
            $this->bLogNotReadable = true;
            $html = TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_tail_log.error_unable_to_read_log').': '.$logFilePath);
        }

        return $html;
    }
}
