        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm table-hover" id="generateVariantTable">
                <thead>
                <tr>
                    <th><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop.variants.variant_editor_headline')); ?></th>
                    <?php
                    foreach ($aVariantTypeNames as $variantTypeName) {
                        echo '<th>'.TGlobal::OutHTML($variantTypeName).'</th>';
                    }
                    /*
                     * @var TdbShopArticle $oArticle
                     */
                    ?>
                    <th><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop.variants.variant_price')); ?> (<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop.variants.variant_base_price')); ?>: <?php echo $oArticle->sqlData['price']; ?>)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $iVariantCount = 0;
                    $VariantValueCount = 0;
                    foreach ($aVariantMatrix as $aVariantValues) {
                        ?>
                    <?php
                        $sVariantTDs = '';
                        $aVariantIDCombination = [];
                        $iVariantValueTypeCount = 0;

                        $dVariantSurchargeTotal = 0;
                        $VariantValueCount = count($aVariantValues);
                        foreach ($aVariantValues as $sVariantValue) {
                            $aVariantValueParts = explode('|', $sVariantValue);
                            $sVariantID = $aVariantValueParts[0];
                            $aVariantIDCombination[] = $sVariantID;
                            $sVariantName = $aVariantValueParts[1];
                            $dVariantSurchargeTotal = $dVariantSurchargeTotal + $aVariantSurcharge[$sVariantID];

                            $sSurchargeInfo = '';
                            if ($aVariantSurcharge[$sVariantID] > 0) {
                                $sSurchargeInfo = ' (+'.$aVariantSurcharge[$sVariantID].')';
                            } elseif ($aVariantSurcharge[$sVariantID] < 0) {
                                $sSurchargeInfo = ' ('.$aVariantSurcharge[$sVariantID].')';
                            }
                            $sVariantTDs .= '<td>'.TGlobal::OutHTML($sVariantName).$sSurchargeInfo.'</td>';
                            ++$iVariantValueTypeCount;
                        }

                        $bVariantExists = false;

                        asort($aVariantIDCombination);
                        $sVariantIDCombination = implode('|', $aVariantIDCombination);
                        if (!in_array($sVariantIDCombination, $aExistingVariantCombinations)) {
                            ?>
                <tr>
                    <td>
                        <input type="checkbox" name="generateVariantItem[<?php echo $iVariantCount; ?>]" class="generateVariantItem" value="" <?php echo 'data-content="'.TGlobal::OutHTML($sVariantIDCombination).'"'; ?> />
                    </td>
                    <?php echo $sVariantTDs; ?>
                    <td>
                        <?php
                            $sFormatValue = number_format($oArticle->sqlData['price'] + $dVariantSurchargeTotal, 2, ',', '.'); ?>
                        <input type="text" name="generateVariantPrice[<?php echo $iVariantCount; ?>]" class="form-control form-control-sm generateVariantPrice" value="<?php echo $sFormatValue; ?>" onblur="this.value=NumberFormat(NumberToFloat(this.value, ',', '.'), 2, ',', '.')" />
                    </td>
                </tr>
                <?php
                        }
                        ++$iVariantCount;
                    }

                    if ($VariantValueCount > 0) {
                        ?>
                <tr>
                    <td colspan="<?php echo $VariantValueCount + 2; ?>"><input type="checkbox" value="" class="generateVariantCheckall" /> <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop.variants.action_select_all_variants')); ?></td>
                </tr>
                <?php
                    }
                    ?>
                </tbody>
            </table>

            <div>
                <a href="javascript:generateVariants();" class="btn btn-success btn-sm"><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop.variants.action_create_variants')); ?></a>
            </div>
        </div>
        <script>
            function generateVariants() {
                var variantObj = [];
                var bVariantsChecked = false;
                var sVariantIDs = '';
                $('#generateVariantTable input.generateVariantItem:checked').not(':disabled').each(function() {
                    bVariantsChecked = true;
                    item = {};
                    sVariantIDs = $(this).attr('data-content');
                    aVariantIDs = sVariantIDs.split("|");
                    item['variantIDs'] = aVariantIDs;
                    item['variantPrice'] = $(this).parent().parent().find('input.generateVariantPrice').val();
                    variantObj.push(item);
                });

                if(bVariantsChecked) {
                    $.ajax({
                        type: "POST",
                        url: '<?php echo $sAjaxURL; ?>&_fieldName=<?php echo $sFieldName; ?>&_fnc=generateVariants',
                        data: { variantParameters: variantObj },
                        success: generateVariantsSuccess,
                        error: AjaxError
                    });
                }
            }

            function generateVariantsSuccess(data, statusText) {
                CloseModalIFrameDialog();
                toasterMessage(data, 'SUCCESS');
            }

            $(".generateVariantCheckall").on("click", function() {
                var checkBoxes = $("#generateVariantTable input.generateVariantItem[type=checkbox]").not(':disabled');
                checkBoxes.prop("checked", !checkBoxes.prop("checked"));
            });
        </script>