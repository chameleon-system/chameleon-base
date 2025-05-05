<?php
$oLocal = TCMSLocal::GetActive(); /* @var $oLocal TCMSLocal */
$oUser = TdbDataExtranetUser::GetInstance();
$sReCommentURL = $oComment->GetURLToReComment($aCallTimeVars['iAktPage'], true);
$sReportCommentURL = $oComment->GetURLToReportComment($aCallTimeVars['iAktPage'], true);
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
                        <span>|</span><?php echo TGlobal::OutHTML($oLocal->FormatDate($oComment->fieldCreatedTimestamp, 12)); ?>
                    </div>
                    <div class="name">
                        <?php
                        $sFirstName = substr($oCommentUser->fieldFirstname, 0, 1).' ';
$sLastName = $oCommentUser->fieldLastname;
$sCommentName = $sFirstName.$sLastName;
if (!empty($oCommentUser->fieldAliasName)) {
    echo 'von '.TGlobal::OutHTML($oCommentUser->fieldAliasName);
} else {
    echo 'von '.$sCommentName;
}
?>
                    </div>
                    <div class="jscomment_inner" style="<?php if (!$oComment->IsActiveComment()) {
                        echo 'display:none';
                    }?>">
                        <form name="recomment-<?php echo TGlobal::OutHTML($oComment->id); ?>" accept-charset="utf-8" method="post"
                              action="" enctype="multipart/form-data">
                            <input type="hidden" name="objectid" value="<?php echo TGlobal::OutHTML($oComment->fieldItemId); ?>"/>
                            <input type="hidden" name="<?php echo TdbPkgComment::URL_NAME_ID; ?>"
                                   value="<?php echo TGlobal::OutHTML($oComment->id); ?>"/>
                            <input type="hidden" name="<?php echo TdbPkgComment::URL_NAME_ID_PAGE; ?>"
                                   value="<?php echo TGlobal::OutHTML($aCallTimeVars['iAktPage']); ?>"/>
                            <input type="hidden" name="commenttypeid"
                                   value="<?php echo TGlobal::OutHTML($oComment->fieldPkgCommentTypeId); ?>"/>
                            <input type="hidden"
                                   name="module_fnc[<?php echo TGlobal::OutHTML($oModulePointer->sModuleSpotName); ?>]"
                                   value="EditComment"/>
                            <input type="hidden" name="sresponseid" value=""/>
                            <textarea class="commenttextarea" cols="0" rows="0" name="commentsavetext"></textarea>
                            <input type="submit" name="savecomment" id="editbuton-<?php echo TGlobal::OutHTML($oComment->id); ?>"
                                   class="button_savecomment"
                                   value="<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.refresh')); ?>"/>
                        </form>
                    </div>
                    <?php if (!$oComment->fieldMarkAsDeleted) {
                        ?>
                    <?php if ($oComment->fieldMarkAsReported) {
                        ?>
                        <div
                            class="notification <?php echo $sCmsIdent; ?>  inapplicable"><?php echo TGlobal::OutHtml(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.text.marked_as_inappropriate')); ?></div>
                        <?php if ($sReCommentURL) {
                            ?>
                            <div class="recomment <?php echo $sCmsIdent; ?>"><a href="<?php echo $sReCommentURL; ?>"
                                                                      onclick="RespondComment('<?php echo TGlobal::OutHTML($oComment->id); ?>','<?php echo TGlobal::OutHTML($sCmsIdent); ?>');$(this).parent().siblings('.jscomment_inner').show(400);return false;"><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.answer')); ?></a>
                            </div>
                            <?php
                        } ?>
                        <?php
                    } else {
                        ?>
                        <div class="comment_inner"
                             id="'.$sCommentCmsIdent.'"><?php echo TGlobal::OutHTML($oComment->fieldComment); ?></div>
                        <?php if ($sReportCommentURL) {
                            ?>
                            <div class="notification <?php echo $sCmsIdent; ?>"><a
                                href="<?php echo $sReportCommentURL; ?>"><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.mark_as_inappropriate')); ?></a>
                            </div>
                            <?php
                        } ?>
                        <?php if ($sReCommentURL) {
                            ?>
                            <div class="recomment <?php echo $sCmsIdent; ?>"><a href="<?php echo $sReCommentURL; ?>"
                                                                      onclick="RespondComment('<?php echo TGlobal::OutHTML($oComment->id); ?>','<?php echo TGlobal::OutHTML($sCmsIdent); ?>');$(this).parent().siblings('.jscomment_inner').show(400);return false;"><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.answer')); ?></a>
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
                    <?php if ($oChildCommentList->Length() > 0) {
                        ?>
            <div class=" respondcommentlist respondcommentlistc<?php echo $oComment->id; ?>">&nbsp;<div>
            <div
                class="antwortCount"><?php echo $oChildCommentList->Length().'&nbsp;'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.answer')); ?></div>
                    <?php while ($oChildComment = $oChildCommentList->Next()) {
                        echo $oChildComment->Render('commentresponse', ['iCommentNr' => $aCallTimeVars['iCommentNr'], 'oActiveItem' => $aCallTimeVars['oActiveItem'], 'iAktPage' => $aCallTimeVars['iAktPage']]);
                    } ?>
                    <?php
                    }?>
                </div>
                </div>
                </div>
            </div>
        </div>