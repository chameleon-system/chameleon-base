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
 * @deprecated since 6.3.0 - view should use twig and Twig filters instead.
 */
class TTemplateTools
{
    /**
     * @param string      $sName
     * @param string|null $sValue
     * @param int         $iWidth
     * @param string      $sOtherAttributes
     * @param string      $sType
     * @param bool        $bEmptyOnClick
     *
     * @return string
     */
    public static function InputField($sName, $sValue = null, $iWidth = 200, $sOtherAttributes = '', $sType = 'text', $bEmptyOnClick = false)
    {
        if (is_null($sValue)) {
            $sValue = '';
        }
        $sEmptyField = '';
        if ($bEmptyOnClick) {
            $sEmptyField = 'onclick="this.value=\'\'"';
        }
        $sClass = 'class="userinput"';
        if (false !== stristr($sOtherAttributes, 'class')) {
            $sClass = '';
        }
        $sXHTML = '<input '.$sClass.' '.$sEmptyField.' onfocus="this.select();" '.$sOtherAttributes.' type="'.$sType.'" style="width: '.($iWidth - 21).'px;" value="'.TGlobal::OutHTML(
                $sValue
            ).'" name="'.$sName.'" />';

        return $sXHTML;
    }

    /**
     * Creates a select field. You have to specify the select's name,
     * an array of values, an array of descriptors, the width in pixels and
     * which entry to select (optional)
     * Something like SelectField('Pizza', array('SA' => 'Salami', 'QU' => 'Quattro stagioni'), 200, 0);
     * would be fine.
     *
     * @param string $sName
     * @param array  $aValues
     * @param int    $iWidth
     * @param int    $iSelected        - selected value
     * @param array  $aOtherParameters - any other parameters you want to set in the <select>
     *                                 tag (parameters are not encoded for html output - so make
     *                                 sure you do this if necessary
     *
     * @return string
     */
    public static function SelectField($sName, $aValues, $iWidth = 200, $iSelected = null, $aOtherParameters = array())
    {
        $sXHTML = '<select class="userinput" name="'.$sName.'" style="width: '.$iWidth.'px;">'."\n";
        $iCounter = 0;
        foreach ($aValues as $sValue => $sDescriptor) {
            if ($sValue == $iSelected) {
                $sSelected = 'selected="selected"';
            } else {
                $sSelected = '';
            }
            $sXHTML .= "\t\t\t".'<option '.$sSelected.' value="'.$sValue.'">'.$sDescriptor.'</option>'."\n";
            ++$iCounter;
        }
        $sXHTML .= "\t\t".'</select>'."\n";

        return $sXHTML;
    }

    /**
     * @param string         $sName
     * @param TCMSRecordList $oList
     * @param string         $sValue
     * @param int            $iWidth
     * @param string         $sSelectParameters
     * @param string         $sInitialValue
     *
     * @return string
     */
    public static function DrawDBSelectField($sName, $oList, $sValue, $iWidth = 200, $sSelectParameters = '', $sInitialValue = 'Bitte wählen')
    {
        $sXHTML = '<select class="userinput" name="'.$sName.'" style="width: '.$iWidth.'px;">'."\n";

        $sSelected = '';
        if (is_null($sValue)) {
            $sSelected = 'selected="selected"';
        }
        $sXHTML .= "<option value=\"\" {$sSelected}>{$sInitialValue}</option>\n";

        $oList->GoToStart();
        while ($oItem = $oList->Next()) {
            $sSelected = '';
            if ($oItem->id == $sValue) {
                $sSelected = 'selected="selected"';
            }
            $sXHTML .= '<option value="'.TGlobal::OutHTML($oItem->id)."\" {$sSelected}>".TGlobal::OutHTML(
                    $oItem->GetName()
                )."</option>\n";
        }
        $sXHTML .= "\t\t".'</select>'."\n";

        return $sXHTML;
    }

    /**
     * @param string $sContent
     *
     * @return string
     */
    public static function Button($sContent)
    {
        $sXHTML = '
       <div class="button">
          <div class="buttonBorderLeft"><img src="/design/images/layout/buttonBorderLeft.gif" alt="" /></div>
          <div class="buttonContent">'.$sContent.'</div>
          <div class="buttonBorderRight"><img src="/design/images/layout/buttonBorderRight.gif" alt="" /></div>
          <div class="cleardiv">&nbsp;</div>
        </div>';

        return $sXHTML;
    }

    /**
     * opens a div box that can be opend/closed using javascript
     * you will need to close the box with 2 single divs.
     *
     * @param bool $bDefaultOpen
     */
    public static function OpenDynamicBox($bDefaultOpen = true)
    {
        static $aIdList = array();
        $iId = md5(rand());
        while (in_array($iId, $aIdList)) {
            $iId = md5(rand());
        }
        $aIdList[] = $iId;
        echo '<div class="dynamicBlock" id="dynamicBlock'.$iId.'">';
        echo '<a href="javascript:void(0);" class="actionLinkHide" onclick="$(\'#dynamicBlock'.$iId.' .dynamicBlockContent\').hide();$(this).hide();$(\'#dynamicBlock'.$iId.' .actionLinkShow\').show()">'.TGlobal::OutHTML(
                TGlobal::Translate('chameleon_system_core.template_tool.hide_dynamic_box')
            ).' <img src="/static/images/icons/arrow_square_downwards_op.png" alt="'.TGlobal::OutHTML(
                TGlobal::Translate('chameleon_system_core.template_tool.hide_dynamic_box')
            ).'" border="0"></a>';
        echo '<a href="javascript:void(0);" class="actionLinkShow" onclick="$(\'#dynamicBlock'.$iId.' .dynamicBlockContent\').show();$(this).hide();$(\'#dynamicBlock'.$iId.' .actionLinkHide\').show()">'.TGlobal::OutHTML(
                TGlobal::Translate('chameleon_system_core.template_tool.show_dynamic_box')
            ).' <img src="/static/images/icons/arrow_square_op.png" alt="'.TGlobal::OutHTML(
                TGlobal::Translate('chameleon_system_core.template_tool.show_dynamic_box')
            ).'" border="0"></a>';
        echo '
        <script type="text/javascript">
          /* <![CDATA[ */
          $(document).ready(function(){
            ';
        if (!$bDefaultOpen) {
            echo '
          $("#dynamicBlock'.$iId.' .dynamicBlockContent").hide();
          $("#dynamicBlock'.$iId.' .actionLinkShow").show();
        ';
        } else {
            echo '$("#dynamicBlock'.$iId.' .actionLinkHide").show();';
        }

        echo '
          });
          /* ]]> */
        </script>
      ';
        echo '<div class="dynamicBlockContent">';
    }

    /**
     * @param string $sText
     * @param string $sOnClick
     * @param string $sClass
     */
    public static function SubmitButton($sText, $sOnClick, $sClass = 'button')
    {
        echo '<script type="text/javascript">/* <![CDATA[ */';
        echo 'document.write("<a href=\"javascript:void(0);\" class=\"'.$sClass.'\" onclick=\"'.$sOnClick.';return false;\">'.TGlobal::OutHTML(
                $sText
            ).'<\/a>");';
        echo '/* ]]> */ </script><noscript><input type="submit" class="submitbutton" value="'.TGlobal::OutHTML(
                $sText
            ).'" /></noscript>';
    }
}
