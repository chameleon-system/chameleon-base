<?php
if (
    TCMSLogChange::AllowTransaction(1, 'dbversionPkgShopServiceItemWrapping', 'DB-Update Counter für das pkgShopServiceItemWrapping-Package') &&
    !TCMSLogChange::RecordExists('pkg_shop_service_type', 'system_name', 'gift-wrapping')
) {
    TCMSLogChange::RunUpdate('pkgShopServiceItem/pkgShopServiceItem-updates'); ?>
<h1>Chameleon PkgShopServiceItemWrapping Build #1</h1>
<h2>Date: 2011-07-13</h2>
<div class="changelog" style="margin-top: 20px; margin-bottom: 20px;">
    - Install the package<br/>

    <div style="padding: 15px;"></div>
</div>
<?php
    $query = "INSERT INTO `pkg_shop_service_type`
                      SET `name` = 'Geschenkverpackung',
                          `system_name` = 'gift-wrapping',
                          `pkg_shop_service_item` = 'pkg_shop_service_item',
                          `class` = 'TPkgShopServiceType_Wrapping',
                          `class_subtype` = 'pkgShopServiceItemWrapping/objects/TPkgShopServiceType',
                          `class_type` = 'Core',
                          `user_selection_view` = 'gift-wrapping',
                          `user_selection_view_subtype` = 'pkgShopServiceItemWrapping/views/TPkgShopServiceType/user-selection',
                          `user_selection_view_type` = 'Core',
                          `display_service_selected` = 'Es ist eine [{linkStart}][{typeName}][{linkEnd}] hinterlegt',
                          `display_no_service_selected` = '[{linkStart}][{typeName}][{linkEnd}] Auswählen',
                          `display_service_used_as_default` = 'Diese Verpackung gilt für den ganzen Warenkorb, außer für Produkte bei denen Sie eine andere Verpackung gewählt haben.',
                          `display_intro_text` = '<div class=\\\"largeHeadline\\\">Geschenkverpackung</div>\\n<div>&nbsp;</div>\\n<div>Bitte wählen Sie eine Geschenkverpackung</div>'
    ";
    TCMSLogChange::_RunQuery($query, __LINE__);
}
