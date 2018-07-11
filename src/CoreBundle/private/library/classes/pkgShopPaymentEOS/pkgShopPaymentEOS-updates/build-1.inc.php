<?php
if (TCMSLogChange::AllowTransaction(1, 'dbversion-pkgShopPaymentEOS')) {
    ?>
    <h1>Chameleon pkgShopPaymentEOS Build #1</h1>
    <h2>Date: 2013-04-08</h2>
    <div class="changelog" style="margin-top: 20px; margin-bottom: 20px;">
        - Add payment handler and config<br/>
        - Add seo url handler<br/>
        <div style="padding: 15px;"></div>
    </div>
<?php
    $query = "INSERT INTO `shop_payment_handler` SET `name` = 'eos-neopay-creditcard', `block_user_selection` = '0', `class` = 'TShopPaymentHandler_EOSNeoPayCreditCard', `class_type` = 'Core', `class_subtype` = 'pkgShopPaymentEOS/pkgShop/objects/db/TShopPaymentHandler', `shop_payment_handler_parameter` = 'shop_payment_handler_parameter'";
    $rRes = TCMSLogChange::_RunQuery($query, __LINE__);

    $sInsertedQuery = "SELECT * FROM `shop_payment_handler` WHERE `class` = 'TShopPaymentHandler_EOSNeoPayCreditCard'";
    $aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sInsertedQuery));
    if ($aRow) {
        $sInsertedId = $aRow['id'];

        $query = "INSERT INTO `shop_payment_handler_parameter` SET `shop_payment_handler_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sInsertedId)."', `name` = 'Profil-ID', `systemname` = 'ProfileID', `description` = 'Dient zur Identifizierung des Händlerprofils', `value` = ''";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "INSERT INTO `shop_payment_handler_parameter` SET `shop_payment_handler_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sInsertedId)."', `name` = 'Live-Mode', `systemname` = 'LiveMode', `description` = '1 =Produktiv-Schnittstellen benutzen. Achtung: Wenn USE_LIVE_PAYMENT = true wird IMMER die Testschnittstelle benutzt.', `value` = '0'";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "INSERT INTO `shop_payment_handler_parameter` SET `shop_payment_handler_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sInsertedId)."', `name` = 'Hashing-Salt', `systemname` = 'Salt', `description` = '<p>Wird benötigt, um den Sicherheits-Hash für Request zu generieren. </p>\\n<p>Wird bei der Erstellung des Händlerprofils zusammen mit der ProfileID von EOS Payment zur Verfügung gestellt.<br />\\n&nbsp; </p>', `value` = ''";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "INSERT INTO `shop_payment_handler_parameter` SET `shop_payment_handler_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sInsertedId)."', `name` = 'Testmodus: Profil-ID', `systemname` = 'Test_ProfileID', `description` = 'Dient zur Identifizierung des Händlerprofils im Test-Mode<br />', `value` = ''";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "INSERT INTO `shop_payment_handler_parameter` SET `shop_payment_handler_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sInsertedId)."', `name` = 'Testmodus: Hashing-Salt', `systemname` = 'Test_Salt', `description` = '<p>Wird benötigt, um den Sicherheits-Hash für Requests zu generieren. </p>\\nWird bei der Erstellung des Händlerprofils zusammen mit der ProfileID von EOS Payment zur Verfügung gestellt.', `value` = ''";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $iPos = 1;
        $sPosQuery = 'SELECT MAX(position) AS maxpos FROM `cms_smart_url_handler`';
        $aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sPosQuery));
        if ($aRow) {
            $iPos = $aRow['maxpos'] + 1;
        }
        $query = "INSERT INTO `cms_smart_url_handler` SET `position` = '".MySqlLegacySupport::getInstance()->real_escape_string($iPos)."', `name` = 'TCMSSmartURLHandler_EOSNeoPay', `class_subtype` = 'pkgShopPaymentEOS', `class_type` = 'Core'";
        TCMSLogChange::_RunQuery($query, __LINE__);
    }
}
