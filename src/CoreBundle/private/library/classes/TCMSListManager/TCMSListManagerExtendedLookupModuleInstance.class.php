<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSListManagerExtendedLookupModuleInstance extends TCMSListManagerModuleInstance
{
    public function _GetRecordClickJavaScriptFunctionName()
    {
        return 'selectModuleInstanceRecord';
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        $aIncludes[] = '
        <script type="text/javascript">
        function selectModuleInstanceRecord(id) {
          parent.selectModuleInstanceRecord(document.cmsformAjaxCall.fieldName.value,id);
        }
        </script>
      ';

        return $aIncludes;
    }
}
