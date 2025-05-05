<?php
$oLocal = TCMSLocal::GetActive(); /* @var $oLocal TCMSLocal */
$oUser = TdbDataExtranetUser::GetInstance();
$sReportCommentURL = $oComment->GetURLToReportComment($aCallTimeVars['iAktPage'], true);
$bAllowReportComment = $aCallTimeVars['bAllowReportComment'];
$iCmsIdent = $oComment->sqlData['cmsident'];
$sCmsIdent = 'commentModifyButton'.strval($iCmsIdent);
$sCommentCmsIdent = 'commentContent'.strval($iCmsIdent);
$oGlobal = TGlobal::instance();
$oModulePointer = $oGlobal->GetExecutingModulePointer();
?>
<div class="TPkgComment">
    <div class="standard">
        <div class="comment">
            <div class="comment_inner">
                <div class="commentheader commentheader<?php echo $oComment->id; ?>">
                    <div class="time"><?php echo TGlobal::OutHTML($oLocal->FormatDate($oComment->fieldCreatedTimestamp, 224)); ?>
                        <span></div>
                    <div class="name">
                        <?php
                        $sCommentName = $oCommentUser->fieldFirstname.$oCommentUser->fieldLastname;
if (!empty($oCommentUser->fieldAliasName)) {
    echo TGlobal::OutHTML($oCommentUser->fieldAliasName);
} else {
    echo TGlobal::OutHTML($sCommentName);
}
?>
                    </div>
                    <?php if (!$oComment->fieldMarkAsDeleted) {
                        ?>
                    <?php if ($oComment->fieldMarkAsReported) {
                        ?>
                        <div
                            class="notification <?php echo $sCmsIdent; ?>  inapplicable"><?php echo TGlobal::OutHtml(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.text.marked_as_inappropriate')); ?></div>
                        <?php
                    } else {
                        ?>
                        <div class="comment_inner"
                             id="'.$sCommentCmsIdent.'"><?php echo TGlobal::OutHTML($oComment->fieldComment); ?></div>
                        <?php if ($sReportCommentURL && $bAllowReportComment) {
                            ?>
                            <div class="notification <?php echo $sCmsIdent; ?>"><a
                                href="<?php echo $sReportCommentURL; ?>"><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.mark_as_inappropriate')); ?></a>
                            </div>
                            <?php
                        } ?>
                        <?php
                    } ?>
                    <?php
                    } else {
                        ?>
                    <div
                        class="notification <?php echo $sCmsIdent; ?>  inapplicable"><?php echo TGlobal::OutHtml(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.text.is_deleted_comment')); ?></div>
                    <?php
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>