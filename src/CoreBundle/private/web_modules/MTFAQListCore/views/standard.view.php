<?php
/** @var $oItemList TdbModuleFaqList */
/* @var $sModuleSpotName string */
?>
<script type="text/javascript">
    function toogleClass(obj) {
        if (obj.className == "active") {
            obj.className = "closed";
        }
        else {
            obj.className = "active";
        }
    }
</script>
<div id="MTFAQList">
    <div class="standard">
        <a name="faq<?php echo TGlobal::OutHTML($sModuleSpotName); ?>"></a>
        <a name="FAQ"></a>

        <div class="itemlist">
            <?php while ($oItem = $oItemList->Next()) {
                /* @var $oItem TdbModuleFaq */ ?>
            <div class="faqItem">
                <div class="faqQuestion">
                    <div class="closed" onclick="$('#faqcontent<?php echo $oItem->id; ?>').toggle();toogleClass(this);">
                        <h2><?php echo $oItem->GetTextField('qdescription'); ?></h2>
                    </div>
                    <div class="cleardiv">&nbsp;</div>
                </div>
                <div class="faqAnswer" id="faqcontent<?php echo $oItem->id; ?>">
                    <?php echo $oItem->GetTextField('artikel'); ?>
                </div>
            </div>
            <?php
            } ?>
        </div>
    </div>
</div>
<script type="text/javascript"> $('.faqAnswer').attr({style:'display: none'}); </script>