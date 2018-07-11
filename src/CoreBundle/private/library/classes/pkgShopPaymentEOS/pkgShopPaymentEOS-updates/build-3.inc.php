<?php
if (TCMSLogChange::AllowTransaction(20, 'dbversion-pkgShopPaymentEOS')) {
    ?>
    <h1>Chameleon pkgShopPaymentEOS Build #3</h1>
    <h2>Date: 2013-07-23</h2>
    <div class="changelog" style="margin-top: 20px; margin-bottom: 20px;">
        - Add option for alias gateway<br/>
        <div style="padding: 15px;"></div>
    </div>
    <?php
    $sInsertedQuery = "SELECT * FROM `shop_payment_handler` WHERE `class` = 'TShopPaymentHandler_EOSNeoPayCreditCard'";
    $aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sInsertedQuery));
    if ($aRow) {
        $sInsertedId = $aRow['id'];

        $query = "INSERT INTO `shop_payment_handler_parameter` SET `shop_payment_handler_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sInsertedId)."', `name` = 'Aliase verwenden', `systemname` = 'useAlias', `description` = 'Sollen Kunden bereits benutze Kreditkarten zur Zahlung verwenden können? Die Recurring-Option muss dafür im EOS-Backend freigeschaltet werden.', `value` = '0'";
        TCMSLogChange::_RunQuery($query, __LINE__);
    }
}
