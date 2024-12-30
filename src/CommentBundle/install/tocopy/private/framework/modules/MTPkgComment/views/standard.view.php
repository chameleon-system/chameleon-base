<?php
/** @var $oModconf TdbPkgCommentModuleConfig* */
/** @var $bAllowGuestComments bool* */
/** @var $oActiveArticle TCMSRecord* */
/** @var $oResponseComment TCMSRecord* */
/** @var $sResponseId string* */
/** @var $sAction string* */
$oGlobal = TGlobal::instance();
$oUser = TdbDataExtranetUser::GetInstance();
$oMessageManager = TCMSMessageManager::GetInstance();
$aFormData = array('commentsavetext' => '', 'title' => '');
if ($oGlobal->UserDataExists('commenttypeid')) {
    if ($oGlobal->UserDataExists('commentsavetext')) {
        $aFormData['commentsavetext'] = $oGlobal->GetUserData('commentsavetext');
    }
    if ($oGlobal->UserDataExists('title')) {
        $aFormData['title'] = $oGlobal->GetUserData('title');
    }
}
?>
<?php if ($oUser->IsLoggedIn() || $bAllowGuestComments) {
    ?>
<form accept-charset="utf-8" method="post" action="" enctype="multipart/form-data">
    <div id="comment_form">
        <input type="hidden" name="objectid" value="<?=TGlobal::OutHTML($oActiveArticle->id); ?>"/>
        <input type="hidden" name="commenttypeid" value="<?=TGlobal::OutHTML($oModconf->fieldPkgCommentTypeId); ?>"/>
        <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value="WriteComment"/>
        Titel: <input class="title" type="text" name="title" value="<?=TGlobal::OutHTML($aFormData['title']); ?>"/><br/>
        <textarea class="commenttextarea" cols="0" rows="0"
                  name="commentsavetext"><?=TGlobal::OutHTML($aFormData['commentsavetext']); ?></textarea><br/>
        <?php
        if ($oMessageManager->ConsumerHasMessages(TdbPkgComment::MESSAGE_CONSUMER_NAME.$oActiveArticle->id)) {
            $oMessages = $oMessageManager->ConsumeMessages(TdbPkgComment::MESSAGE_CONSUMER_NAME.$oActiveArticle->id);
            while ($oMessage = $oMessages->Next()) {
                echo $oMessage->Render();
            }
        } ?>
    </div>
    <input type="submit" name="savecomment" value=" <?=TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.submit')); ?> "/>
</form>
<div id="comment_preview_button"><?=TGlobal::OutHtml(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.preview')); ?></div>
<div id="comment_preview">
    <?php
    $oPreviewComment = TdbPkgComment::GetNewInstance();
    echo $oPreviewComment->Render('preview_article_comment'); ?>
</div>
<?php
} else {
        ?>
<?php
    $oExtranetConfig = TdbDataExtranet::GetInstance();
        $sLinkLogin = $oExtranetConfig->GetFieldNodeLoginIdPageURL();
        $aReplaceArray = array('%linkloginstart%' => '<a href="'.$sLinkLogin.'">', '%linkloginend%' => '</a>'); ?>
    <?= \ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.error.login_required', $aReplaceArray); ?>
<?php
    } ?>
<?php
echo $oModconf->Render('standard');
?>