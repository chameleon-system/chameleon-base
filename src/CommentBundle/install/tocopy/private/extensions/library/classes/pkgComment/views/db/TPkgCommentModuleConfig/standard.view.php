<?php
/** @var $oModconf TdbPkgCommentModuleConfig */
$oUser = TdbDataExtranetUser::GetInstance();
$iCommentNr = $iCommentNr - ($iAktPage - 1) * $iPageSizege;
$iCount = 0;
$iShowCommentsOnStart = $oModconf->fieldNumberOfCommentsPerPage;
?>
<div class="TPkgCommentModuleconfig">
    <div class="standard">
        <?php if ($oCommentList->Length() > 0) {
            ?>
        <hr>
        <div class="commentliststart"><?php echo TGlobal::OutHtml(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.text.comments_of_a_comment_headline')); ?></div>
        <div class="commentlist">
            <?php
                    $oCommentList->GoToStart();
            while ($oComment = $oCommentList->Next()) { /* @var $oComment TdbPkgComment */
                if ($iCount >= $iShowCommentsOnStart) {
                    echo '<div class="jshide">';
                }
                echo $oComment->Render('standard', ['iCommentNr' => $iCommentNr, 'oActiveItem' => $oActiveItem, 'iAktPage' => $iAktPage, 'sAnnounceCommentLink' => $sAnnounceCommentLink, 'bAllowReportComment' => $oModconf->fieldShowReportedComments]);
                if ($iCount >= $iShowCommentsOnStart) {
                    echo '</div>';
                }
                ++$iCount;
                --$iCommentNr;
            }
            if ($iCount > $iShowCommentsOnStart) {
                ?>
                <script type="text/javascript">
                    document.write("<" + 'a href="" class="comment_showall" onclick="$(\'.commentlist .jshide\').toggle(); $(this).toggle();$(\'.comment_showstart\').toggle();return false;"><?php echo TGlobal::OutHtml(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.show_all')); ?></a' + ">");
                    document.write("<" + 'a href="" class="comment_showstart" onclick="$(\'.commentlist .comment_showall\').toggle();$(this).toggle();$(\'.commentlist .jshide\').toggle();return false;"><?php echo TGlobal::OutHtml(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.show_fewer')); ?></a' + ">");
                    $(document).ready(function () {
                        $('.commentlist .jshide').hide();
                        $('.commentlist .comment_showstart').hide()
                    });
                </script>
                <?php
            } ?>
        </div>
        <?php
        } ?>
    </div>
</div>
