<?php
$oTableRow = $data['oTableRow'];
/** @var $oTableRow TCMSRecord */
$aInput = $data['aInput'];
$oError = $data['oError']; /* @var $oError MTFeedbackErrors */
?>
<div class="ModuleFeedback">
    <?php if (!empty($oTableRow->sqlData['name'])) {
        echo '<h1>'.TGlobal::OutHTML($oTableRow->sqlData['name']).'</h1>';
    } ?>
    <?php echo $oTableRow->GetTextField('text'); ?>
    <form method="post" action="">
        <input type="hidden" name="module_fnc[<?php echo TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value="SendEMail"/>
        <table cellpadding="0" cellspacing="0" width="100%" border="0">
            <tr>
                <th>Ihr Name:</th>
                <td>
                    <input type="text" name="name" value="<?php echo TGlobal::OutHTML($aInput['name']); ?>"/>
                    <?php if ($oError->FieldHasErrors('name')) {
                        ?>
                    <div>Sie m&uuml;ssen Ihren Namen angeben</div><?php
                    } ?>
                </td>
            </tr>
            <tr>
                <th>Ihre E-Mail Adresse:</th>
                <td>
                    <input type="text" name="email" value="<?php echo TGlobal::OutHTML($aInput['email']); ?>"/>
                    <?php if ($oError->FieldHasErrors('email')) {
                        ?>
                    <div>Sie m&uuml;ssen Ihre E-Mail Adresse angeben</div><?php
                    } ?>
                </td>
            </tr>
            <tr>
                <th>Betreff:</th>
                <td>
                    <input type="text" name="subject" value="<?php echo TGlobal::OutHTML($aInput['subject']); ?>"/>
                    <?php if ($oError->FieldHasErrors('subject')) {
                        ?>
                    <div>Sie m&uuml;ssen einen Betreff angeben</div><?php
                    } ?>
                </td>
            </tr>
            <tr>
                <th>Ihre Anfrage:</th>
                <td>
                    <textarea name="body" rows="" cols=""><?php echo TGlobal::OutHTML($aInput['body']); ?></textarea>
                    <?php if ($oError->FieldHasErrors('body')) {
                        ?>
                    <div>Sie m&uuml;ssen eine Anfrage angeben</div><?php
                    } ?>
                </td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td><input class="submitbutton" type="submit" name="subit" value="OK"/></td>
            </tr>
        </table>
    </form>
</div>