<?php
if (
    !TCMSLogChange::RecordExists('pkg_shop_service_type', 'system_name', 'card')
) {
    TCMSLogChange::RunUpdate('pkgShopServiceItem/pkgShopServiceItem-updates'); ?>
<h1>Chameleon PkgShopServiceItemGiftcard Build #1</h1>
<h2>Date: 2011-07-13</h2>
<div class="changelog" style="margin-top: 20px; margin-bottom: 20px;">
    - Install the package<br/>

    <div style="padding: 15px;"></div>
</div>
<?php
    $query = "INSERT INTO `pkg_shop_service_type`
                      SET `name` = 'Grußkarten',
                          `system_name` = 'card',
                          `pkg_shop_service_item` = 'pkg_shop_service_item',
                          `class` = 'TPkgShopServiceType_Giftcard',
                          `class_subtype` = 'pkgShopServiceItemGiftcard/objects/TPkgShopServiceType',
                          `class_type` = 'Core',
                          `user_selection_view` = 'card',
                          `user_selection_view_subtype` = 'pkgShopServiceItemGiftcard/views/TPkgShopServiceType/user-selection',
                          `user_selection_view_type` = 'Core',
                          `display_service_selected` = 'Es ist eine [{linkStart}][{typeName}][{linkEnd}] hinterlegt',
                          `display_no_service_selected` = '[{linkStart}][{typeName}][{linkEnd}] Auswählen',
                          `display_service_used_as_default` = 'Diese Karte gilt für den ganzen Warenkorb, außer für Produkte bei denen Sie eine andere Karte gewählt haben.',
                          `display_intro_text` = '<div class=\\\"largeHeadline\\\">Geschenkkarte</div>\\n<div>&nbsp;</div>\\n<div>Bitte wählen Sie eine Karte</div>'
    ";
    TCMSLogChange::_RunQuery($query, __LINE__);
}
