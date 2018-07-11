<?php
if (TCMSLogChange::AllowTransaction(10, 'dbversion-pkgShopPaymentEOS')) {
    ?>
<h1>Chameleon pkgShopPaymentEOS Build #2</h1>
<h2>Date: 2013-04-09</h2>
<div class="changelog" style="margin-top: 20px; margin-bottom: 20px;">
    - Add error messages<br/>
    <div style="padding: 15px;"></div>
</div>
<?php
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-INVALID-XML-RESPONSE', 'Fehler beim Verarbeiten der Anfrage.', 4, 'XML-Daten konnten nicht geladen werden (simplexml error)');
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-COULD-NOT-INIT-FORM', 'Fehler beim Verarbeiten der Anfrage.', 4, 'Init-Form ist fehlgeschlagen. ');
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-GENERAL-ERROR', 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut oder kontaktieren Sie den Support.', 4, 'sErrorCode und sErrorClassifier wie von EOS übermittelt stehen zur Verfügung');
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-USER-ABORTED', 'Es ist ein Fehler aufgetreten: Die Zahlung wurde abgebrochen.', 4, 'Abbruch durch den Kunden.');
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-REJECTED-BY-ACQUIRER', 'Es ist ein Fehler aufgetreten: Die Zahlung wurde vom Dienstleister zurückgewiesen.', 4, 'Dienstleister lehnte die Transaktion ab.');
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-AUTHENTICATION-FAILED', 'Es ist ein Fehler aufgetreten: Authentifizierung fehlgeschlagen.', 4, 'Authentifizierung ist fehlgeschlagen.');
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-PAYMENT-DATA-NOT-ALLOWED', 'Es ist ein Fehler aufgetreten: Zahlungsdaten nicht erlaubt.', 4, 'Zahlungsdaten sind für diesen Transaktionstyp nicht erlaubt.');
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-FRAUD', 'Es ist ein Fehler aufgetreten: Die Zahlung wurde durch Betrugserkennung blockiert.', 4, 'Betrug.');
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-LIMIT-REACHED', 'Es ist ein Fehler aufgetreten: Limit erreicht.', 4, 'Limit ist erreicht.');
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-3D-SECURE', 'Es ist ein Fehler aufgetreten: 3D-Secure-Fehler.', 4, '3D-Secure fehlgeschlagen.');
    TCMSLogChange::AddFrontEndMessage('ERROR-PAYMENT-EOS-TRANSACTION-NOT-POSSIBLE', 'Es ist ein Fehler aufgetreten: Transaktion nicht möglich.', 4, 'Transaktion nicht möglich.');
}
