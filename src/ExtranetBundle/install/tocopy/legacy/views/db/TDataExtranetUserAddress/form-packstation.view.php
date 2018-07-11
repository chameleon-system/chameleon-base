<?php
/** @var $oUserAddress TdbDataExtranetUserAddress */
/** @var $oExtranetConfig TdbDataExtranet */
/** @var $aCallTimeVars array */
$oMessageManager = TCMSMessageManager::GetInstance();
$sAddressName = TdbDataExtranetUserAddress::FORM_DATA_NAME_BILLING;
if (array_key_exists('sAddressName', $aCallTimeVars)) {
    $sAddressName = $aCallTimeVars['sAddressName'];
}
$sAddressName = TGlobal::OutHTML($sAddressName);

?>
<div class="TDataExtranetUserAddress">
<div class="form userinput">

<?=$oMessageManager->RenderMessages($sAddressName); ?>
<input type="hidden" name="<?=$sAddressName.'[id]'; ?>" value="<?=TGlobal::OutHTML($oUserAddress->id); ?>"/>
<table summary="">
<tr>
    <th>&nbsp;</th>
    <td>
        <label><input type="checkbox" name="<?=$sAddressName.'[is_dhl_packstation]'; ?>"
                      value="1" <?php if ($oUserAddress->fieldIsDhlPackstation) {
    echo 'checked="checked"';
}?>
                      onclick="document.user.<?=MTShopOrderWizardCore::URL_PARAM_STEP_METHOD; ?>.value = 'ChangeShippingAddressIsPackstationState';document.user.submit();"/><?=TGlobal::OutHTML(TGlobal::Translate('An eine Packstation versenden')); ?>
        </label>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-is_dhl_packstation')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-is_dhl_packstation');
        }
        ?>
    </td>
</tr>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Anrede')); ?><span class="required">*</span></th>
    <td>
        <?php
        $oSalutationList = &TdbDataExtranetSalutationList::GetList();
        $sSelectedId = $oUserAddress->fieldDataExtranetSalutationId;
        while ($oSalutation = $oSalutationList->Next()) {
            $sSelected = '';
            if ($sSelectedId == $oSalutation->id) {
                $sSelected = 'checked="checked"';
            }
            echo '<label><input class="plain" '.$sSelected.' type="radio" value="'.TGlobal::OutHTML($oSalutation->id).'" name="'.$sAddressName.'[data_extranet_salutation_id]" />'.TGlobal::OutHTML($oSalutation->GetName()).'</label>';
        }
        ?>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-data_extranet_salutation_id')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-data_extranet_salutation_id');
        }
        ?>
    </td>
</tr>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Vorname')); ?><span class="required">*</span></th>
    <td>
        <?=TTemplateTools::InputField($sAddressName.'[firstname]', $oUserAddress->fieldFirstname, 310); ?>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-firstname')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-firstname');
        }
        ?>
    </td>
</tr>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Name')); ?><span class="required">*</span></th>
    <td>
        <?=TTemplateTools::InputField($sAddressName.'[lastname]', $oUserAddress->fieldLastname, 310); ?>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-lastname')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-lastname');
        }
        ?>
    </td>
</tr>

<?php if (false == $oUserAddress->fieldIsDhlPackstation) {
            ?>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Firma')); ?></th>
    <td><?=TTemplateTools::InputField($sAddressName.'[company]', $oUserAddress->fieldCompany, 300); ?>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-company')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-company');
        } ?>
    </td>
</tr>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Adresszusatz')); ?></th>
    <td><?=TTemplateTools::InputField($sAddressName.'[address_additional_info]', $oUserAddress->fieldAddressAdditionalInfo, 310); ?>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-address_additional_info')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-address_additional_info');
        } ?>
    </td>
</tr>

<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Straße, Nr.')); ?><span class="required">*</span></th>
    <td>
        <?=TTemplateTools::InputField($sAddressName.'[street]', $oUserAddress->fieldStreet, 270); ?>
        <div class="cleardiv">&nbsp;</div>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-street')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-street');
        } ?>
    </td>
</tr>
    <?php
        } else {
            ?>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Postnummer')); ?><span class="required">*</span></th>
    <td><?=TTemplateTools::InputField($sAddressName.'[address_additional_info]', $oUserAddress->fieldAddressAdditionalInfo, 310); ?>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-address_additional_info')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-address_additional_info');
        } ?>
    </td>
</tr>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Packstation')); ?><span class="required">*</span></th>
    <td>
        <?=TTemplateTools::InputField($sAddressName.'[street]', $oUserAddress->fieldStreet, 270); ?>
        <?php
        $sLanguageCode = TTools::GetActiveLanguageIsoName();
            if (empty($sLanguageCode)) {
                $sLanguageCode = 'de';
            } ?>
        <a href="http://standorte.dhl.de/Standortsuche?standorttyp=packstationen_paketboxen&lang=<?=TGlobal::OutHTML($sLanguageCode); ?>"
           target="_blank"><?=TGlobal::OutHTML(TGlobal::Translate('zum Packstationenfinder')); ?></a>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-street')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-street');
        } ?>
    </td>
</tr>
    <?php
        } ?>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('PLZ')); ?><span class="required">*</span></th>
    <td>
        <?=TTemplateTools::InputField($sAddressName.'[postalcode]', $oUserAddress->fieldPostalcode, 310, 'id="postalcode"'); ?>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-postalcode')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-postalcode');
        }
        ?>
        <script type="text/javascript" language="javascript">
            /* <![CDATA[ */
            $(document).ready(function () {
                $("#postalcode").blur(function () {
                    var myTmpVar = $(this).val();
                <?php
                $aAjaxParam = array('module_fnc' => array(TdbShop::GetInstance()->fieldShopCentralHandlerSpotName => 'ExecuteAjaxCall'), '_fnc' => 'GetCityByPostalcode', 'plz' => '');
                $sAjaxLink = '?'.str_replace('&amp;', '&', TTools::GetArrayAsURL($aAjaxParam));
                echo "var url = '$sAjaxLink' + myTmpVar;";
                ?>
                    //alert('ajax-run:' + url);
                    GetAjaxCall(url, GetFieldCityContent);
                });

                function GetFieldCityContent(data, textStatus, jqXHR) {
                    $("#city").val(data);
                }
            });
            /* ]]> */
        </script>
    </td>
</tr>

<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Ort')); ?><span class="required">*</span></th>
    <td>
        <?=TTemplateTools::InputField($sAddressName.'[city]', $oUserAddress->fieldCity, 310, 'id="city"'); ?>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-city')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-city');
        }
        ?>
    </td>
</tr>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Land')); ?><span class="required">*</span></th>
    <td>
        <?php
        $oCountries = &TdbDataCountryList::GetList();
        $oShop = TdbShop::GetInstance();
        $iCountryId = $oUserAddress->fieldDataCountryId;
        if (is_null($iCountryId) || $iCountryId < 1) {
            $iCountryId = $oShop->fieldDataCountryId;
        }
        echo TTemplateTools::DrawDbSelectField($sAddressName.'[data_country_id]', $oCountries, $iCountryId, 281);

        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-data_country_id')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-data_country_id');
        }
        ?>
    </td>
</tr>

<?php if (false == $oUserAddress->fieldIsDhlPackstation) {
            ?>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Telefon')); ?></th>
    <td><?=TTemplateTools::InputField($sAddressName.'[telefon]', $oUserAddress->fieldTelefon, 310); ?>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-telefon')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-telefon');
        } ?>
    </td>
</tr>
<tr>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('Fax')); ?></th>
    <td><?=TTemplateTools::InputField($sAddressName.'[fax]', $oUserAddress->fieldFax); ?>
        <?php
        if ($oMessageManager->ConsumerHasMessages($sAddressName.'-fax')) {
            echo $oMessageManager->RenderMessages($sAddressName.'-fax');
        } ?>
    </td>
</tr>
    <?php
        } ?>

</table>
</div>
</div>
