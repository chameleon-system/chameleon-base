<?php
$oLocal = TCMSLocal::GetActive(); /*@var $oLocal TCMSLocal*/
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
                <div class="commentheader commentheader<?=$oComment->id; ?>">
                    <div class="time"><?= TGlobal::OutHTML($oLocal->FormatDate($oComment->fieldCreatedTimestamp, 224)); ?>
                        <span>|</span><?=TGlobal::OutHTML($oLocal->FormatDate($oComment->fieldCreatedTimestamp, 12)); ?>
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
                        <form name="recomment-<?=TGlobal::OutHTML($oComment->id); ?>" accept-charset="utf-8" method="post"
                              action="" enctype="multipart/form-data">
                            <input type="hidden" name="objectid" value="<?=TGlobal::OutHTML($oComment->fieldItemId); ?>"/>
                            <input type="hidden" name="<?=TdbPkgComment::URL_NAME_ID; ?>"
                                   value="<?=TGlobal::OutHTML($oComment->id); ?>"/>
                            <input type="hidden" name="<?=TdbPkgComment::URL_NAME_ID_PAGE; ?>"
                                   value="<?=TGlobal::OutHTML($aCallTimeVars['iAktPage']); ?>"/>
                            <input type="hidden" name="commenttypeid"
                                   value="<?=TGlobal::OutHTML($oComment->fieldPkgCommentTypeId); ?>"/>
                            <input type="hidden"
                                   name="module_fnc[<?=TGlobal::OutHTML($oModulePointer->sModuleSpotName); ?>]"
                                   value="EditComment"/>
                            <input type="hidden" name="sresponseid" value=""/>
                            <textarea class="commenttextarea" cols="0" rows="0" name="commentsavetext"></textarea>
                            <input type="submit" name="savecomment" id="editbuton-<?=TGlobal::OutHTML($oComment->id); ?>"
                                   class="button_savecomment"
                                   value="<?=TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.refresh')); ?>"/>
                        </form>
                    </div>
                    <?php if (!$oComment->fieldMarkAsDeleted) {
                            ?>
                    <?php if ($oComment->fieldMarkAsReported) {
                                ?>
                        <div
                            class="notification <?=$sCmsIdent; ?>  inapplicable"><?=TGlobal::OutHtml(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.text.marked_as_inappropriate')); ?></div>
                        <?php if ($sReCommentURL) {
                                    ?>
                            <div class="recomment <?=$sCmsIdent; ?>"><a href="<?=$sReCommentURL; ?>"
                                                                      onclick="RespondComment('<?=TGlobal::OutHTML($oComment->id); ?>','<?=TGlobal::OutHTML($sCmsIdent); ?>');$(this).parent().siblings('.jscomment_inner').show(400);return false;"><?=TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.answer')); ?></a>
                            </div>
                            <?php
                                } ?>
                        <?php
                            } else {
                                ?>
                        <div class="comment_inner"
                             id="'.$sCommentCmsIdent.'"><?= TGlobal::OutHTML($oComment->fieldComment); ?></div>
                        <?php if ($sReportCommentURL) {
                                    ?>
                            <div class="notification <?=$sCmsIdent; ?>"><a
                                href="<?=$sReportCommentURL; ?>"><?=TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.mark_as_inappropriate')); ?></a>
                            </div>
                            <?php
                                } ?>
                        <?php if ($sReCommentURL) {
                                    ?>
                            <div class="recomment <?=$sCmsIdent; ?>"><a href="<?=$sReCommentURL; ?>"
                                                                      onclick="RespondComment('<?=TGlobal::OutHTML($oComment->id); ?>','<?=TGlobal::OutHTML($sCmsIdent); ?>');$(this).parent().siblings('.jscomment_inner').show(400);return false;"><?=TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.answer')); ?></a>
                            </div>
                            <?php
                                } ?>
                        <?php
                            } ?>
                    <?php
                        } else {
                            ?>
                    <div
                        class="notification <?=$sCmsIdent; ?>  inapplicable"><?=TGlobal::OutHtml(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.text.is_deleted_comment')); ?></div>
                    <?php
                        } ?>
                    <?php if ($oChildCommentList->Length() > 0) {
                            ?>
            <div class=" respondcommentlist respondcommentlistc<?=$oComment->id; ?>">&nbsp;<div>
            <div
                class="antwortCount"><?=$oChildCommentList->Length().'&nbsp;'.TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.answer')); ?></div>
                    <?php   while ($oChildComment = $oChildCommentList->Next()) {
                                echo $oChildComment->Render('commentresponse', array('iCommentNr' => $aCallTimeVars['iCommentNr'], 'oActiveItem' => $aCallTimeVars['oActiveItem'], 'iAktPage' => $aCallTimeVars['iAktPage']));
                            } ?>
                    <?php
                        }?>
                </div>
                </div>
                </div>
            </div>
        </div>