<?php
/** @var $oStep TdbCmsWizardStep */
$oMessageManager = TCMSMessageManager::GetInstance();
$oNewsletterSignup = $data['oNewsletterSignup'];
/** @var $oNewsletterSignup TdbPkgNewsletterUser */
$oNewsletterConfig = $data['oNewsletterConfig'];
/** @var $oNewsletterConfig TdbPkgNewsletterModuleSignupconfig */
$oUser = TdbDataExtranetUser::GetInstance();
// show the newsletter signup only if the user has not signed on yet

?>
<div class="MTPkgNewsletterSignup">
    <div class="standard">
        <div class="headline"><?php echo TGlobal::OutHTML($oNewsletterConfig->fieldSignupHeadline); ?></div>
        <div class="newsletterSignup">
            <form name="newnestlettersign" accept-charset="utf-8" method="post"
                  action="<?php echo $data['aMainModuleInfo']['URL']; ?>">
                <input type="hidden" name="module_fnc[<?php echo TGlobal::OutHTML($data['aMainModuleInfo']['spotname']); ?>]"
                       value="SignUp"/>

                <div class="introtext"><?php echo $oNewsletterConfig->GetTextField('signup_text'); ?></div>
                <div class="formdata">
                    <?php
                    if ($oMessageManager->ConsumerHasMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME)) {
                        echo $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME);
                    }
?>
                    <div class="inputbox_salutaion inputbox">
                        <div class="left">
                            <?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_newsletter.form.salutation'); ?>
                        </div>
                        <div class="right">
                            <?php
        $oSalutations = TdbDataExtranetSalutationList::GetList();
if (empty($oNewsletterSignup->fieldDataExtranetSalutationId)) {
    $sValue = $oUser->fieldDataExtranetSalutationId;
} else {
    $sValue = $oNewsletterSignup->fieldDataExtranetSalutationId;
}
echo TTemplateTools::DrawDBSelectField(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'[data_extranet_salutation_id]', $oSalutations, $sValue, 100, '', '');
if ($oMessageManager->ConsumerHasMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-data_extranet_salutation_id')) {
    echo $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-data_extranet_salutation_id');
}
?>
                        </div>
                        <div class="cleardiv">&nbsp;</div>
                    </div>

                    <div class="inputbox_firstname inputbox">
                        <div class="left">
                            <?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_newsletter.form.first_name'); ?>
                        </div>
                        <div class="right">
                            <?php
if (empty($oNewsletterSignup->fieldFirstname)) {
    $sValue = $oUser->fieldFirstname;
} else {
    $sValue = $oNewsletterSignup->fieldFirstname;
}
?>
                            <?php echo TTemplateTools::InputField(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'[firstname]', $sValue, 100, '', 'text', true); ?>
                            <?php
if ($oMessageManager->ConsumerHasMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-firstname')) {
    echo $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-firstname');
}
?>
                        </div>
                        <div class="cleardiv">&nbsp;</div>
                    </div>

                    <div class="inputbox_lastname inputbox">
                        <div class="left">
                            <?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_newsletter.form.last_name'); ?>
                        </div>
                        <div class="right">
                            <?php
if (empty($oNewsletterSignup->fieldLastname)) {
    $sValue = $oUser->fieldLastname;
} else {
    $sValue = $oNewsletterSignup->fieldLastname;
}
?>
                            <?php echo TTemplateTools::InputField(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'[lastname]', $sValue, 170, '', 'text', true); ?>
                            <?php
if ($oMessageManager->ConsumerHasMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-lastname')) {
    echo $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-lastname');
}
?>
                        </div>
                        <div class="cleardiv">&nbsp;</div>
                    </div>

                    <div class="inputbox_email inputbox">
                        <div class="left">
                            <?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_newsletter.form.email'); ?>
                        </div>
                        <div class="right">
                            <?php
if (empty($oNewsletterSignup->fieldEmail)) {
    $sValue = $oUser->fieldName;
} else {
    $sValue = $oNewsletterSignup->fieldEmail;
}
?>
                            <?php echo TTemplateTools::InputField(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'[email]', $sValue, 181, '', 'text', true); ?>
                            <?php
if ($oMessageManager->ConsumerHasMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-email')) {
    echo $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-email');
}
?>
                        </div>
                        <div class="cleardiv">&nbsp;</div>
                    </div>
                    <?php $oNewsletterGroupList = $oNewsletterConfig->GetFieldPkgNewsletterGroupList(); /* @var $oNewsletterGroupList TdbPkgNewsletterGroupList */ ?>
                    <?php if ($oNewsletterGroupList->Length() > 0) {
                        ?>
                    <div class="inputbox_newsletter inputbox">
                        <div class="left"><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_newsletter.text.available_newsletter'); ?></div>
                        <div class="right">
                            <?php if ($oNewsletterGroupList->Length() > 1) {
                                ?>
                            <div class="newslcheck">
                                <div class="left"><input id="all" type="checkbox"
                                                         name="<?php echo TGlobal::OutHTML(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'[newsletter][all]'); ?>"
                                                         onclick="$('.newsletterchecker').prop('checked', true)"/>
                                </div>
                                <div class="right"><label
                                    for="all"><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_newsletter.form.subscribe_to_all'); ?></label></div>
                                <div class="cleardiv">&nbsp;</div>
                            </div>
                            <?php
                            } ?>
                            <?php while ($oNesletter = $oNewsletterGroupList->Next()) { /* @var $oNesletter TdbPkgNewsletterGroup */ ?>
                            <?php
                            if (!array_key_exists('aAvailableForUserList', $data)) {
                                $bNewsletterIsSubsrcibed = false;
                            } else {
                                if (array_key_exists($oNesletter->id, $data['aAvailableForUserList'])) {
                                    $bNewsletterIsSubsrcibed = false;
                                } else {
                                    $bNewsletterIsSubsrcibed = true;
                                }
                            }
                                ?>
                            <div class="newslcheck">
                                <?php if ($bNewsletterIsSubsrcibed) {
                                    ?>
                                <div class="left">&nbsp;</div>
                                <?php
                                } else {
                                    ?>
                                <div class="left"><input id="newsletter_<?php echo $oNesletter->id; ?>" class="newsletterchecker"
                                                         type="checkbox"
                                                         name="<?php echo TGlobal::OutHTML(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'[newsletter]['.$oNesletter->id.']'); ?>"
                                                         onclick="if(!$(this).attr('checked')){ $('#all').attr('checked',''); }"/>
                                </div>
                                <?php
                                } ?>
                                <div class="right"><label
                                    for="newsletter_<?php echo $oNesletter->id; ?>"><?php echo TGlobal::OutHTML($oNesletter->fieldName); ?></label>
                                </div>
                                <div class="cleardiv">&nbsp;</div>
                            </div>
                            <?php } ?>
                            <?php
                                if ($oMessageManager->ConsumerHasMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-newsletter')) {
                                    echo $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-newsletter');
                                } ?>
                        </div>
                    </div>
                    <?php
                    } ?>

                    <div class="inputbox_button inputbox">
                        <div class="left"></div>
                        <div class="right">
                            <input type="image" src="/design/images/forms/buttonSubscribe.png" title="Abonnieren"/>
                        </div>
                        <div class="cleardiv">&nbsp;</div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>