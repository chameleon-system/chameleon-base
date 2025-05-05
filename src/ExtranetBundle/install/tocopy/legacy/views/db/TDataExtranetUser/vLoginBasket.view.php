<?php
/* @var $oUser TdbDataExtranetUser */
/* @var $oExtranetConfig TdbDataExtranet */
/* @var $aCallTimeVars array */

use ChameleonSystem\ExtranetBundle\objects\ExtranetUserConstants;

$sSpotName = '';
if (array_key_exists('sSpotName', $aCallTimeVars)) {
    $sSpotName = $aCallTimeVars['sSpotName'];
} else {
    echo 'You must pass the name of the module spot in aCallTimeVars!';
}

$sConsumer = $sSpotName.'-form';
if (array_key_exists('sConsumer', $aCallTimeVars)) {
    $sConsumer = $aCallTimeVars['sConsumer'];
}

$oMessageManager = TCMSMessageManager::GetInstance();
?>
<div class="TDataExtranetUser">
    <div class="vLoginBasket">
        <form name="loginBox<?php echo $sSpotName; ?>" method="post" action="" accept-charset="UTF-8" >

            <div class="box">
                <div class="mediumHeadline">Ich bin bereits Kunde</div>
                <br/>
                <input type="hidden" name="module_fnc[<?php echo TGlobal::OutHTML($sSpotName); ?>]" value="Login"/>
                <input type="hidden" name="spwdhash" value=""/>
                <input type="hidden" name="sConsumer" value="<?php echo TGlobal::OutHTML($sConsumer); ?>"/>

                <?php if (array_key_exists('sFailureURL', $aCallTimeVars)) {
                    ?><input type="hidden" name="sFailureURL"
                                                                                      value="<?php echo TGlobal::OutHTML($aCallTimeVars['sFailureURL']); ?>"/><?php
                } ?>
                <?php if (array_key_exists('sSuccessURL', $aCallTimeVars)) {
                    ?><input type="hidden" name="sSuccessURL"
                                                                                      value="<?php echo TGlobal::OutHTML($aCallTimeVars['sSuccessURL']); ?>"/><?php
                } ?>

                <!--<div class="email"><input type="text" class="email" name="<?php echo ExtranetUserConstants::LOGIN_FORM_FIELD_LOGIN_NAME; ?>" value="" /></div>-->

                <table class="userinput" summary="">
                    <tr>
                        <th>E-Mail-Adresse</th>
                        <td><input class="userinput" type="text" name="<?php echo ExtranetUserConstants::LOGIN_FORM_FIELD_LOGIN_NAME; ?>" value=""/></td>
                    </tr>
                    <tr>
                        <th>Passwort</th>
                        <td><input class="userinput" type="password" name="password" value=""/></td>
                    </tr>
                    <tr>
                        <th>&nbsp;</th>
                        <td><a href="<?php echo $oExtranetConfig->GetLinkForgotPasswordPage(); ?>" rel="nofollow">Passwort
                            vergessen?</a></td>
                    </tr>
                </table>


                <?php
                            if ($oMessageManager->ConsumerHasMessages(TGlobal::OutHTML($sConsumer))) {
                                echo $oMessageManager->RenderMessages($sConsumer);
                            }
?>
                <div style="overflow: hidden; width: 1px; height: 1px; position: relative;"><input type="submit"
                                                                                                   name="dummy"
                                                                                                   style="position: relative; left: 10px; top: 10px;"/>
                </div>
            </div>
            <br/>
            <input type="submit" name="" value="Einloggen" class="button buttonRight">
            <div class="cleardiv">&nbsp;</div>
            <br/>
            <br/>

        </form>
    </div>
</div>